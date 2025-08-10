<?php

require_once 'vendor/autoload.php';  // Add this line

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    // Initialize DynamoDB client ONCE
    $dynamodb = new Aws\DynamoDb\DynamoDbClient([
        'region' => 'ap-southeast-2',
        'version' => 'latest',
    ]);

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    $action = $input['action'] ?? '';
    $username = trim($input['username'] ?? '');
    $password = trim($input['password'] ?? '');

    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Username and password are required']);
        exit;
    }

    // Simple file-based storage for local development
    $users_file = 'users.json';

    if ($action === 'LoginUserCreate') {
        // Check if user exists first
        $result = $dynamodb->getItem([
            'TableName' => 'login-data-table',
            'Key' => [
                'username' => ['S' => $username]
            ]
        ]);

        if (!empty($result['Item'])) {
            http_response_code(409);
            echo json_encode(['error' => 'Username already exists']);
            exit;
        }

        // Create new user
        $dynamodb->putItem([
            'TableName' => 'login-data-table',
            'Item' => [
                'username' => ['S' => $username],
                'password' => ['S' => password_hash($password, PASSWORD_DEFAULT)],
                'created_at' => ['S' => date('Y-m-d H:i:s')]
            ]
        ]);

        echo json_encode(['message' => 'User created successfully!']);
        
    } elseif ($action === 'LoginAuthenticate') {
        // Get user from DynamoDB
        $result = $dynamodb->getItem([
            'TableName' => 'login-data-table',
            'Key' => [
                'username' => ['S' => $username]
            ]
        ]);

        if (empty($result['Item'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid username or password']);
            exit;
        }

        // Verify password
        $stored_password = $result['Item']['password']['S'] ?? '';
        if (password_verify($password, $stored_password)) {
            echo json_encode(['message' => 'Login successful!', 'username' => $username]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid username or password']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action. Use LoginUserCreate or LoginAuthenticate']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
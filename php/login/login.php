// login data into DynamoDB table

<?php

require __DIR__ . '/vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;
use Bref\Context\Context;
use Bref\Event\Http\HttpRequest;
use Bref\Event\Http\HttpResponse;

function lambda_handler($event, Context $context): HttpResponse
{
    // Initialize DynamoDB client
    $dynamodb = new DynamoDbClient([
        'region' => 'ap-southeast-2',
        'version' => 'latest'
    ]);

    // Parse the HTTP request
    $request = new HttpRequest($event);
    
    // Set CORS headers
    $headers = [
        'Content-Type' => 'application/json',
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Headers' => 'Content-Type',
        'Access-Control-Allow-Methods' => 'OPTIONS,POST,GET'
    ];

    // Handle OPTIONS request for CORS
    if ($request->getMethod() === 'OPTIONS') {
        return new HttpResponse(
            json_encode(['message' => 'CORS preflight']),
            $headers,
            200
        );
    }

    try {
        // Parse request body
        $body = json_decode($request->getBody(), true);
        
        $action = $body['action'] ?? '';
        $username = trim($body['username'] ?? '');
        $password = trim($body['password'] ?? '');


        if (empty($username) || empty($password)) {
            return new HttpResponse(
                json_encode(['error' => 'Username and password are required']),
                $headers,
                400
            );
        }

        // Route to appropriate function
        if ($action === 'LoginUserCreate') {
            return createUser($dynamodb, $username, $password, $headers);

        } elseif ($action === 'LoginAuthenticate') {
            return authenticateUser($dynamodb, $username, $password, $headers);
        
        } else {
            return new HttpResponse(
                json_encode(['error' => 'Invalid action. Use LoginUserCreate or LoginAuthenticate']),
                $headers,
                400
            );
        }

    } catch (Exception $e) {
        return new HttpResponse(
            json_encode(['error' => 'Internal server error: ' . $e->getMessage()]),
            $headers,
            500
        );
    }
}

// using the data from the HTML form to create a new user data in the DynamoDB table
function createUser(DynamoDbClient $dynamodb, string $username, string $password, array $headers): HttpResponse
{
    $tableName = 'login-data-table';

    // Check if username already exists
    try {
        $existingUser = $dynamodb->getItem([
            'TableName' => $tableName,
            'Key' => [
                'username' => ['S' => $username]
            ]
        ]);

        // If user already exists, return error
        if (isset($existingUser['Item'])) {
            return new HttpResponse(
                json_encode(['error' => 'Username already exists.']),
                $headers,
                409
            );
        }

        // Hash the password for security
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Save new user to DynamoDB
        $result = $dynamodb->putItem([
            'TableName' => $tableName,
            'Item' => [
                'username' => ['S' => $username],
                'password' => ['S' => $hashedPassword],
                'created_at' => ['S' => date('Y-m-d H:i:s')]
            ]
        ]);

        return new HttpResponse(
            json_encode(['message' => 'User created successfully!']),
            $headers,
            201
        );

    } catch (Exception $e) {
        return new HttpResponse(
            json_encode(['error' => 'Could not create user: ' . $e->getMessage()]),
            $headers,
            500
        );
    }
}

// using the data from the HTML form to search a user data matched in the DynamoDB table
function authenticateUser(DynamoDbClient $dynamodb, string $username, string $password, array $headers): HttpResponse
{
    $tableName = 'login-data-table';

    try {
        // Get user by username only
        $result = $dynamodb->getItem([
            'TableName' => $tableName,
            'Key' => [
                'username' => ['S' => $username]
            ]
        ]);

        // Check if user exists
        if (!isset($result['Item'])) {
            return new HttpResponse(
                json_encode(['error' => 'Invalid username or password.']),
                $headers,
                401
            );
        }

        // Verify password
        $storedPassword = $result['Item']['password']['S'];
        if (password_verify($password, $storedPassword)) {
            return new HttpResponse(
                json_encode([
                    'message' => 'Login successful!',
                    'username' => $username
                ]),
                $headers,
                200
            );
        } else {
            return new HttpResponse(
                json_encode(['error' => 'Invalid username or password.']),
                $headers,
                401
            );
        }

    } catch (Exception $e) {
        return new HttpResponse(
            json_encode(['error' => 'Could not authenticate user: ' . $e->getMessage()]),
            $headers,
            500
        );
    }
}
?>
<?php
$current_path = __DIR__;
$parent_path = dirname($current_path);
require_once "${parent_path}/vendor/autoload.php";

use Aws\S3\S3Client;
use Aws\DynamoDb\DynamoDbClient;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');


// try {
$s3 = new S3Client([
    'region' => 'ap-southeast-2',
    'version' => 'latest',
]);
    
$csv = "./csv/dmm-websites.csv"; 
$HTMLPath = "./html/"; 

// Get all objects in the csv
$csv_data = read_csv($csv);
if ($csv_data === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to read CSV file']);
    exit;
}

try {
    $dynamodb_result = put_dynamodb($csv_data);

    $html_result = create_html($title_list, $content_list, $HTMLPath);

    if ($dynamodb_result && $html_result) {
        http_response_code(200);
        echo json_encode([
            'message' => 'CSV processed successfully',
            'files_created' => count($csv_data),
            'html_path' => $HTMLPath
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to process some data']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}



// Read csv file and return all data
function read_csv($file_path) {
    $all_data = [];

    if (!file_exists($file_path)) {
        error_log("Error: CSV file not found at $file_path");
        return false;
    }

    $file = fopen($file_path, 'r');
    if ($file !== false) {
        // Skip header row
        fgetcsv($file);

    if (!file_exists($file_path)) {
        error_log("Error: CSV file not found at $file_path");
        return false;
    }
    
    $file = fopen($file_path, 'r');
    if ($file !== false) {
        // Skip header row
        fgetcsv($file);
        
        // Read data rows
        while (($data = fgetcsv($file)) !== false) {
            // Make sure we have at least 3 columns
            if (count($data) >= 3) {
                $all_data[] = $data;
            }
        }
        fclose($file);
    } else {
        error_log("Error: Could not open CSV file");
        return false;
    }
    
    return $all_data;
}


function put_dynamodb($csv_data) {
    try {
        $client = new DynamoDbClient([
            'region' => 'ap-southeast-2',
            'version' => 'latest',
            // Add credentials if needed
        ]);
        
        $tableName = 'http-data-table';
        $success_count = 0;

        foreach ($csv_data as $row) {
            $item = [
                'title' => ['S' => $row[0]],
                'url' => ['S' => $row[1]],
                'content' => ['S' => $row[2]],
                'created_at' => ['S' => date('Y-m-d H:i:s')]
            ];

            try {
                $client->putItem([
                    'TableName' => $tableName,
                    'Item' => $item
                ]);
                $success_count++;
            } catch (Exception $e) {
                error_log("DynamoDB Error: " . $e->getMessage());
            }
        }

        return $success_count > 0;
        
    } catch (Exception $e) {
        error_log("DynamoDB Client Error: " . $e->getMessage());
        return false;
    }
}


// csv pattern

function create_html($csv_data, $HTMLPath) {
    if (!is_dir($HTMLPath)) {
        if (!mkdir($HTMLPath, 0755, true)) {
            error_log("Failed to create directory: $HTMLPath");
            return false;
        }
    }

    $success_count = 0;

    // Create HTML file for EACH row
    foreach ($csv_data as $index => $row) {
        $title = htmlspecialchars($row[0]);
        $url = htmlspecialchars($row[1]);
        $content = htmlspecialchars($row[2]);

        $html = "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>$title</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h1 { 
            color: #333; 
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        p { 
            line-height: 1.6; 
            color: #555;
        }
        .url { 
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .url a { 
            color: #0066cc; 
            text-decoration: none;
            font-weight: bold;
        }
        .url a:hover {
            text-decoration: underline;
        }
        .content {
            margin-top: 20px;
            font-size: 16px;
        }
        .back-link {
            margin-top: 30px;
            text-align: center;
        }
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>$title</h1>
        <div class='url'>
            <strong>Website URL:</strong><br>
            <a href='$url' target='_blank'>$url</a>
        </div>
        <div class='content'>
            <strong>Description:</strong><br>
            $content
        </div>
        <div class='back-link'>
            <a href='../dmm-websites-description.php'>← Back to Website List</a>
        </div>
    </div>
</body>
</html>";

        // Create unique filename
        $safe_title = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $title);
        $filename = $safe_title . '_' . ($index + 1) . '.html';
        
        if (save_html_file($HTMLPath, $filename, $html)) {
            $success_count++;
        }
    }

    return $success_count > 0;
}

function save_html_file($HTMLPath, $filename, $html_content) {
    $file_path = $HTMLPath . $filename;

    if (file_put_contents($file_path, $html_content)) {
        error_log("HTML file created successfully: $file_path");
        return true;
    } else {
        error_log("Error: Could not create HTML file: $file_path");
        return false;
    }
}
?>
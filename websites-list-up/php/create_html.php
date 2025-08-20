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

require_once "${parent_path}/php/secret_variables.php";
list($web_url, $anime_url, $exception_words) = get_secret_variables();

try {
    $s3 = new S3Client([
        'region' => 'ap-southeast-2',
        'version' => 'latest',
    ]);
} catch (Exception $e) {
    echo "Error initializing S3 client: " . $e->getMessage() . "\n";
}    

$url_data_list = [];
echo "Search word: $search_word\n";

$item_data_list = retrieve_data_from_url($search_word);

echo "Data retrieval completed. Found " . count($item_data_list) . " items.\n";

if ($item_data_list && count($item_data_list) > 0) {
    echo "Writing to CSV...\n";
    csv_write($item_data_list);
    // create_html($item_data_list, './html/');
} else {
    echo "No data retrieved or empty dataset.\n";
}


function retrieve_data_from_url($word) {
    // First test: try to access the main page
    echo "Testing main page access...\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $test_response = curl_exec($ch);
    $test_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Main page test - HTTP Code: $test_code, Response length: " . strlen($test_response) . "\n";
    
    if ($test_code !== 200) {
        echo "Main page is not accessible. Site may be down or blocking requests.\n";
        return [];
    }
    
    $title_list = [];
    $title_href_list = [];
    $item_picture_list = [];

    $item_data_list = [];

    for ($i = 1; $i <= 3; $i++) { // Limited to 3 pages for testing
    
        // Properly encode the Japanese characters
        $encoded_word = urlencode($word);
        $url = "{$url}/?word=$encoded_word&c=&page=$i";
        echo "Fetching URL: " . $url . "\n";
        echo "Encoded word: $encoded_word\n";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // リダイレクト追跡
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'); // より詳細なUser-Agent
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // タイムアウト設定
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // SSL検証を無効化（テスト用）
        curl_setopt($ch, CURLOPT_HTTPGET, true); // Explicitly set GET method
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language: ja,en-US;q=0.7,en;q=0.3',
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1',
            'Referer: $anime_url/',
        ]);
        curl_setopt($ch, CURLOPT_ENCODING, ''); // 自動的に圧縮を処理
        
        $html = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        // エラーチェック
        if ($html === false || !empty($curl_error)) {
            echo "cURL Error: " . $curl_error . "\n";
            
            // Try with file_get_contents as fallback
            echo "Trying file_get_contents as fallback...\n";
            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n" .
                               "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" .
                               "Accept-Language: ja,en-US;q=0.7,en;q=0.3\r\n",
                    'timeout' => 30,
                ]
            ]);
            $html = file_get_contents($url, false, $context);
            
            if ($html === false) {
                echo "file_get_contents also failed\n";
                continue;
            } else {
                echo "file_get_contents succeeded\n";
                $http_code = 200; // Assume success if file_get_contents worked
            }
        }
        
        if ($http_code !== 200) {
            echo "HTTP Error: " . $http_code . "\n";
            continue;
        }
        
        if (empty(trim($html))) {
            echo "Empty response received\n";
            continue;
        }
        
        
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        $xpath = new DOMXPath($dom);

        echo "XPath initialized successfully\n";
        // only search tag <itemTitle>
        $nodes = $xpath->query('//div[@class="itemTitle"]');

        // Loop through each title node
        foreach ($nodes as $node) {
            $title_href_list[] = $xpath->query('.//a', $node);
            $title_list[] = $node->nodeValue;
        }

        $nodes = $xpath->query('//div[@class="itemImage"]');

        // Loop through each image node
        foreach ($nodes as $node) {
            // Get the title from the node
            $item_picture_list[] = $xpath->query('.//img', $node);
            if (empty($item_picture_list)) {
                echo "Empty picture found, skipping this item.\n";
                continue; 
            }
        }            
    }

    if ($i < 10) {
        sleep(1);
    }

    for ($i = 0; $i < count($title_list); $i++) {
        $item_list[] = [
            'title' => $title_list[$i],
            'url' => $title_href_list[$i],
            'picture' => $item_picture_list[$i]
        ];
    }

    return $item_list;
}

// write URL data into CSV
function csv_write($item_data_list) {
    $current_dir = __DIR__;
    $csv_file = $current_dir . '/csv/websites.csv';
    $file = fopen($csv_file, 'w');

    if ($file === false) {
        error_log("Error: Could not open CSV file for writing");
        return false;
    }

    // Write header
    fwrite($file, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel compatibility
    fputcsv($file, ['Title', 'URL', 'Picture']);

    // loop every row to write data
    foreach ($item_data_list as $item) {
        fputcsv($file, [$item['title'], $item['url'], $item['picture']]);
    }

    fclose($file);
    echo "CSV file created successfully: $csv_file\n";
    return true;
}


function put_dynamodb($item_data_list) {
    try {
        $client = new DynamoDbClient([
            'region' => 'ap-southeast-2',
            'version' => 'latest',
            // Add credentials if needed
        ]);
        
        $tableName = 'http-data-table';
        $success_count = 0;

        foreach ($item_data_list as $item) {
            $dynamo_item = [
                'title' => ['S' => $item['title']],
                'url' => ['S' => $item['url']],
                'picture' => ['S' => $item['picture'] ?? ''],
                'created_at' => ['S' => date('Y-m-d H:i:s')]
            ];

            try {
                $client->putItem([
                    'TableName' => $tableName,
                    'Item' => $dynamo_item
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

function create_html($csv_data, $HTMLPath) {
    if (!is_dir($HTMLPath)) {
        if (!mkdir($HTMLPath, 0755, true)) {
            error_log("Failed to create directory: $HTMLPath");
            return false;
        }
    }

    $success_count = 0;

    // Create HTML file for EACH item
    foreach ($csv_data as $index => $item) {
        $title = htmlspecialchars($item['title']);
        $url = htmlspecialchars($item['url']);
        $picture = htmlspecialchars($item['picture'] ?? 'No image available');

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
            <strong>Picture URL:</strong><br>
            $picture
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
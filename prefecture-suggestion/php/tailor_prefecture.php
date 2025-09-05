<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    // Validate required fields
    $required_fields = ['country', 'country_cuisine', 'climate', 'season'];
    $missing_fields = [];

    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        throw new Exception('Missing required fields: ' . implode(', ', $missing_fields));
    }

    // Here you would process the data (save to database, etc.)
    // For now, we'll just log it and return success

    // Log the submission (optional)
    $log_data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => $data,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
    
    // You can save to database or file here
    // For example, save to a JSON log file:
    $log_file = 'submissions.log';
    file_put_contents($log_file, json_encode($log_data) . "\n", FILE_APPEND);

    // Simulate prefecture recommendation logic
    $prefectures = [
        'Tokyo', 'Osaka', 'Kyoto', 'Hiroshima', 'Hokkaido', 
        'Okinawa', 'Kanagawa', 'Aichi', 'Fukuoka', 'Sendai'
    ];
    
    $recommended_prefecture = $prefectures[array_rand($prefectures)];

    echo json_encode([
        'success' => true,
        'message' => 'Form submitted successfully!',
        'recommended_prefecture' => $recommended_prefecture,
        'data_received' => $data
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

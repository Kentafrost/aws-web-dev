<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Validate input parameter
    if (!isset($_GET['country']) || empty($_GET['country'])) {
        throw new Exception('Country parameter is required');
    }

    $country = $_GET['country']; // Country name from JS
    $page = urlencode($country . " cuisine"); // Wikipedia page name for cuisine

    $url = "https://ja.wikipedia.org/w/api.php?action=parse&page=$page&format=json&prop=text";
    
    // Use cURL for better error handling
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; PHP script)');
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($response === false || $http_code !== 200) {
        throw new Exception('Failed to fetch Wikipedia data');
    }
    
    $cuisines_url_data = json_decode($response, true);
    
    if (!$cuisines_url_data || !isset($cuisines_url_data['parse']['text']['*'])) {
        throw new Exception('Invalid Wikipedia API response');
    }

    // HTMLから料理名を抽出（例：liタグやテーブルから）
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($cuisines_url_data['parse']['text']['*']);
    libxml_clear_errors();

    // Extract dish names from the HTML content
    $dishes = [];
    $xpath = new DOMXPath($dom);
    
    // Look for dishes in various HTML structures
    $dish_queries = [
        '//ul//li[string-length(text()) > 3]',  // List items
        '//table//td[string-length(text()) > 3]', // Table cells
        '//h3/following-sibling::ul//li' // Items after headings
    ];
    
    foreach ($dish_queries as $query) {
        $nodes = $xpath->query($query);
        foreach ($nodes as $node) {
            $text = trim($node->textContent);
            if (strlen($text) > 3 && strlen($text) < 50 && !preg_match('/^\d+$/', $text)) { 
                $dishes[] = $text;
            }
        }
    }
    
    // Remove duplicates and limit results
    $dishes = array_unique($dishes);
    $dishes = array_slice($dishes, 0, 20); // Limit to 20 dishes

    // Write debug data to file (optional)
    $debug_data = [
        'country' => $country,
        'page' => $page,
        'dishes_found' => count($dishes),
        'url_used' => $url
    ];
    file_put_contents("country_cuisines_debug.json", json_encode($debug_data, JSON_PRETTY_PRINT));
    
    // Return the extracted dishes
    echo json_encode(array_values($dishes));

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage(),
        'country' => $_GET['country'] ?? 'unknown'
    ]);
}
?>
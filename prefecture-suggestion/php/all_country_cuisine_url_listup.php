<?php
// Function to fetch data from Wikipedia API
function fetchWikipediaAPI($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; PHP cuisine fetcher)');
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($response === false) {
        throw new Exception("cURL Error: " . $error);
    }
    
    if ($http_code !== 200) {
        throw new Exception("HTTP Error: " . $http_code);
    }
    
    return json_decode($response, true);
}

try {
    // Use Wikipedia API to get category members for "Cuisine by country"
    echo "Fetching cuisine data from Wikipedia API...\n";
    
    // API URL for getting category members
    $api_url = "https://en.wikipedia.org/w/api.php?action=query&list=categorymembers&cmtitle=Category:Cuisine_by_country&cmlimit=500&format=json";
    
    $api_data = fetchWikipediaAPI($api_url);
    
    if (!$api_data || !isset($api_data['query']['categorymembers'])) {
        throw new Exception("Invalid Wikipedia API response");
    }
    
    $category_members = $api_data['query']['categorymembers'];
    echo "Found " . count($category_members) . " category members\n";
    
    // Filter and process cuisine pages
    $cuisine_links = [];
    $country_cuisines = [];
    
    // get all data from Wikipedia API
    foreach ($category_members as $member) {
        $title = $member['title'];
        $page_id = $member['pageid'];
        $namespace = $member['ns']; // 0 = main article, 14 = category
        
        // Process main articles (ns=0) that are actual cuisine pages
        if ($namespace == 0) {
            $url = "https://en.wikipedia.org/wiki/" . urlencode(str_replace(' ', '_', $title));
            
            // Extract country name from cuisine title
            $country_name = '';
            if (preg_match('/^(.+?)\s+cuisine$/i', $title, $matches)) {
                $country_name = $matches[1];
            } elseif (preg_match('/^Cuisine\s+of\s+(.+?)$/i', $title, $matches)) {
                $country_name = $matches[1];
            } else {
                $country_name = $title;
            }
            
            $country_cuisines[] = [
                'country' => $country_name,
                'title' => $title,
                'url' => $url,
                'page_id' => $page_id
            ];
        }
        
        // Also collect subcategory links for reference
        if ($namespace == 14 && stripos($title, 'cuisine') !== false) {
            $cuisine_links[] = [
                'category' => $title,
                'url' => "https://en.wikipedia.org/wiki/" . urlencode(str_replace(' ', '_', $title)),
                'page_id' => $page_id
            ];
        }
    }
    
    // Sort countries alphabetically
    usort($country_cuisines, function($a, $b) {
        return strcmp($a['country'], $b['country']);
    });
    
    // Save complete data to JSON file
    $output_data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'total_members' => count($category_members),
        'country_cuisines' => $country_cuisines,
        'cuisine_categories' => $cuisine_links
    ];
    
    // save into a JSON file
    $current_dir = dirname(__FILE__);
    file_put_contents("$current_dir/all_country_cuisines.json", json_encode($output_data, JSON_PRETTY_PRINT));

    // Display results
    echo "\n=== COUNTRY CUISINES ===\n";
    echo "Found " . count($country_cuisines) . " country cuisine pages:\n\n";
    
    foreach ($country_cuisines as $cuisine) {
        echo "Country: " . $cuisine['country'] . "\n";
        echo "Title: " . $cuisine['title'] . "\n";
        echo "URL: " . $cuisine['url'] . "\n";
        echo "Page ID: " . $cuisine['page_id'] . "\n\n";
    }
    
    echo "\n=== CUISINE CATEGORIES ===\n";
    echo "Found " . count($cuisine_links) . " cuisine category pages:\n\n";
    
    foreach ($cuisine_links as $link) {
        echo "Category: " . $link['category'] . "\n";
        echo "URL: " . $link['url'] . "\n\n";
    }
    
    // Output JSON for programmatic use
    echo "\n=== JSON OUTPUT ===\n";
    echo json_encode($country_cuisines, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>

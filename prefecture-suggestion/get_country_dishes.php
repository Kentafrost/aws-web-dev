<?php
$country = $_GET['country']; // JSから送られた国名
$page = urlencode($country . "料理"); // Wikipediaページ名

$url = "https://ja.wikipedia.org/w/api.php?action=parse&page=$page&format=json&prop=text";
$response = file_get_contents($url);
$data = json_decode($response, true);

// HTMLから料理名を抽出（例：liタグやテーブルから）
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($data['parse']['text']['*']);
libxml_clear_errors();

$dishes = [];
foreach ($dom->getElementsByTagName('li') as $li) {
    $text = trim($li->textContent);
    if (mb_strlen($text) > 2 && !preg_match('/^.*料理$/', $text)) {
        $dishes[] = $text;
    }
}

echo json_encode($dishes);
?>
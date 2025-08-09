<?php?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
        }
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .image-item {
            text-align: center;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }
        .image-item img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>All Websites Descriptions</h1>
        
    <div class="html-gallery">
    <?php
    $imageFolder = './html/';

    if (is_dir($imageFolder)) {
        $files = scandir($imageFolder);

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'html') {
                $url = $imageFolder . $file;
                echo '<div class="image-item">';
                echo '<a href="' . htmlspecialchars($url) . '" target="_blank">';
                echo '<strong>' . htmlspecialchars($file) . '</strong><br>';
                echo $url;
                echo '</a>';
                echo '</div>';
            }
        }
    } else {
        echo '<p>HTML folder not found.</p>';
    }
    ?>
    </div>
        </div>
</body>
</html>
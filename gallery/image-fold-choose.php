<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Gallery</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
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
        .back-link {
            margin-bottom: 20px;
        }
        .folder-selector {
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
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
            transition: transform 0.2s;
        }
        .image-item:hover {
            transform: scale(1.05);
        }
        .image-item img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
        select {
            padding: 8px 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="back-link">
            <a href="../top.php">← Back to Top</a>
        </div>
        
        <h1>Image Gallery</h1>

        <div class="folder-selector">
            <h2>Available Folders</h2>
            <p>Select a folder to view images:</p>

            <form action="" method="GET">
                <select name="folder" onchange="this.form.submit()">
                    <option value="">-- Select a folder --</option>
                    <?php
                    // List all folders in images directory
                    $imagesDir = './images/'; // Correct path from gallery subfolder
                    $folders = array_filter(glob($imagesDir . '*'), 'is_dir');
                    
                    if (!empty($folders)) {
                        $selectedFolder = $_GET['folder'] ?? '';
                        
                        foreach ($folders as $folder) {
                            $folderName = basename($folder);
                            $folderPath = $imagesDir . $folderName;
                            $selected = ($selectedFolder === $folderPath) ? 'selected' : '';
                            echo "<option value='$folderPath' $selected>$folderName</option>";
                        }
                    }
                    ?>
                </select>
            </form>
        </div>

        <?php if (!empty($selectedFolder)): ?>
            <a href="./image.php?folder=<?php echo urlencode($selectedFolder); ?>">View Images from '<?php echo htmlspecialchars(basename($selectedFolder)); ?>' in Detail</a>
        <?php else: ?>
            <a href="./image-display.php">View All Website Descriptions</a>
        <?php endif; ?>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Viewer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
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
        .back-link {
            margin-bottom: 20px;
        }
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }
        .image-item {
            text-align: center;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .image-item img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .image-item img:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        }
        .image-title {
            font-weight: bold;
            margin-top: 10px;
            color: #333;
        }
        .folder-info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .main-content {
            display: flex;
            gap: 30px;
        }
        .content-area {
            flex: 2;
        }
        .sidebar {
            flex: 1;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            height: fit-content;
            position: sticky;
            top: 20px;
        }
        .sidebar h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .folder-list {
            list-style: none;
            padding: 0;
        }
        .folder-list li {
            margin: 8px 0;
        }
        .folder-list a {
            color: #667eea;
            text-decoration: none;
            padding: 8px 12px;
            display: block;
            border-radius: 4px;
            transition: background 0.2s;
        }
        .folder-list a:hover {
            background: #e3f2fd;
        }
        .folder-list a.active {
            background: #667eea;
            color: white;
        }
        /* Modal styles for image zoom */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            animation: fadeIn 0.3s;
        }

        .modal-content {
            position: relative;
            margin: auto;
            padding: 20px;
            width: 90%;
            max-width: 1200px;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-image {
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(255, 255, 255, 0.1);
        }

        .close {
            position: absolute;
            top: 20px;
            right: 35px;
            color: #ffffff;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            z-index: 1001;
            transition: color 0.3s;
        }

        .close:hover {
            color: #cccccc;
        }

        .modal-info {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-align: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="back-link">
            <a href="images-list.php">← Back to Gallery</a>
        </div>

        <div class="main-content">
            <div class="content-area">
                <?php
                $selectedFolder = $_GET['folder'] ?? '';
                
                if (!empty($selectedFolder) && is_dir($selectedFolder)) {
                    $folderName = basename($selectedFolder);
                    
                    echo "<div class='folder-info'>";
                    echo "<h1>Images from '$folderName' Folder</h1>";
                    echo "<p>Detailed view of all images in the selected folder</p>";
                    echo "</div>";
                    
                    echo '<div class="image-gallery">';
                    
                    // Get all files from the selected folder
                    $files = scandir($selectedFolder);
                    $imageCount = 0;
                    
                    foreach ($files as $file) {
                        // Check if it's an image file
                        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        if (in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'])) {
                            $imagePath = $selectedFolder . '/' . $file;
                            $fileSize = filesize($imagePath);
                            $fileSizeKB = round($fileSize / 1024, 2);
                            
                            echo '<div class="image-item">';
                            // Add onclick event to image
                            echo '<img src="' . htmlspecialchars($imagePath) . '" 
                                  alt="' . htmlspecialchars($file) . '"
                                  onclick="openModal(\'' . htmlspecialchars($imagePath) . '\', \'' . htmlspecialchars($file) . '\', \'' . $fileSizeKB . '\', \'' . strtoupper($extension) . '\')">';
                            echo '<div class="image-title">' . htmlspecialchars($file) . '</div>';
                            echo '<div>Size: ' . $fileSizeKB . ' KB</div>';
                            echo '<div>Type: ' . strtoupper($extension) . '</div>';
                            echo '</div>';
                            $imageCount++;
                        }
                    }
                    
                    echo '</div>';
                    
                    if ($imageCount === 0) {
                        echo '<p>No images found in this folder.</p>';
                    } else {
                        echo "<p>Total images: $imageCount</p>";
                    }
                    
                } else {
                    echo '<h1>No Folder Selected</h1>';
                    echo '<p>Please go back and select a folder first.</p>';
                    echo '<a href="images-list.php">← Back to Gallery</a>';
                }
                ?>
            </div>

            <!-- Sidebar with all folders -->
            <div class="sidebar">
                <h3>All Folders</h3>
                <ul class="folder-list">
                    <?php
                    // List all available folders
                    $imagesDir = './images/'; // Adjust path as needed
                    $allFolders = array_filter(glob($imagesDir . '*'), 'is_dir');
                    
                    if (!empty($allFolders)) {
                        foreach ($allFolders as $folder) {
                            $folderName = basename($folder);
                            $folderPath = $imagesDir . $folderName;
                            $isActive = ($selectedFolder === $folderPath) ? 'active' : '';
                            
                            // Count images in this folder
                            $folderFiles = scandir($folder);
                            $imageCountInFolder = 0;
                            foreach ($folderFiles as $file) {
                                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                if (in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'])) {
                                    $imageCountInFolder++;
                                }
                            }
                            
                            echo '<li>';
                            echo '<a href="image.php?folder=' . urlencode($folderPath) . '" class="' . $isActive . '">';
                            echo htmlspecialchars($folderName);
                            echo ' <small>(' . $imageCountInFolder . ' images)</small>';
                            echo '</a>';
                            echo '</li>';
                        }
                    } else {
                        echo '<li>No folders found</li>';
                    }
                    ?>
                </ul>
                
                <hr style="margin: 20px 0; border: 1px solid #ddd;">
                
                <h3>Quick Actions</h3>
                <ul class="folder-list">
                    <li><a href="images-list.php">📁 Browse Gallery</a></li>
                    <li><a href="../top.php">🏠 Home Page</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Modal for image zoom -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <img id="modalImage" class="modal-image" src="" alt="">
            <div id="modalInfo" class="modal-info">
                <div id="modalFileName"></div>
                <div id="modalFileDetails"></div>
            </div>
        </div>
    </div>

    <script src="zoom.js"></script>

</body>
</html>
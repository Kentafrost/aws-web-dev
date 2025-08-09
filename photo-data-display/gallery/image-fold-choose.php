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
        
        /* Media Gallery Styles */
        .media-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .media-item {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            background: #f9f9f9;
            transition: transform 0.2s;
        }
        .media-item:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .media-item img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
        }
        .media-item video {
            width: 100%;
            height: 200px;
            border-radius: 5px;
        }
        .media-filename {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
            text-align: center;
            word-break: break-word;
        }
        .media-type {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .type-image {
            background: #e8f5e8;
            color: #2d5a2d;
        }
        .type-video {
            background: #e8f0ff;
            color: #2d4a7d;
        }
        .folder-link {
            display: inline-block;
            padding: 8px 16px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
            transition: background 0.2s;
        }
        .folder-link:hover {
            background: #0056b3;
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

        <!-- D Drive File Browser -->
        <div class="folder-selector">
            <h2>D Drive Browser</h2>
            <p>Browse files and folders in D drive:</p>
            
            <?php
            function displayFolderSection($folderPath, $folderName) {
                if (!is_dir($folderPath) || !is_readable($folderPath)) {
                    echo "<div class='folder-section' style='margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa;'>";
                    echo "<h4 style='color: #dc3545; margin-top: 0;'>❌ " . htmlspecialchars($folderName) . "</h4>";
                    echo "<p style='color: red;'>Cannot access folder: " . htmlspecialchars($folderPath) . "</p>";
                    echo "</div>";
                    return;
                }

                try {
                    $items = scandir($folderPath);
                    if ($items === false) {
                        return;
                    }
                    
                    // Filter and sort items
                    $items = array_diff($items, ['.', '..']);
                    sort($items);
                    
                    $folders = [];
                    $files = [];
                    
                    foreach ($items as $item) {
                        $fullPath = $folderPath . DIRECTORY_SEPARATOR . $item;
                        if (is_dir($fullPath)) {
                            $folders[] = $item;
                        } else {
                            $files[] = $item;
                        }
                    }
                    
                    $totalItems = count($folders) + count($files);
                    
                    // Display folder section
                    echo "<div class='folder-section' style='margin: 20px 0; padding: 15px; border: 1px solid #007bff; border-radius: 8px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);'>";
                    echo "<h4 style='color: #007bff; margin-top: 0; border-bottom: 2px solid #007bff; padding-bottom: 8px;'>";
                    echo "📂 " . htmlspecialchars($folderName) . " <span style='font-size: 14px; color: #6c757d;'>(" . $totalItems . " items)</span>";
                    echo "</h4>";
                    
                    // Organize items alphabetically
                    $allItems = [];
                    
                    // Add folders
                    foreach ($folders as $folder) {
                        $firstLetter = strtoupper(substr($folder, 0, 1));
                        if (!isset($allItems[$firstLetter])) {
                            $allItems[$firstLetter] = [];
                        }
                        $allItems[$firstLetter][] = [
                            'name' => $folder,
                            'type' => 'folder',
                            'path' => $folderPath . DIRECTORY_SEPARATOR . $folder
                        ];
                    }
                    
                    // Add files
                    foreach ($files as $file) {
                        $firstLetter = strtoupper(substr($file, 0, 1));
                        if (!isset($allItems[$firstLetter])) {
                            $allItems[$firstLetter] = [];
                        }
                        $allItems[$firstLetter][] = [
                            'name' => $file,
                            'type' => 'file',
                            'path' => $folderPath . DIRECTORY_SEPARATOR . $file
                        ];
                    }
                    
                    ksort($allItems);
                    
                    // Display organized content
                    if (!empty($allItems)) {
                        foreach ($allItems as $letter => $items) {
                            if (!empty($items)) {
                                echo "<div style='margin: 10px 0;'>";
                                echo "<strong style='color: #28a745; background: #d4edda; padding: 3px 8px; border-radius: 4px; font-size: 14px;'>" . $letter . "...</strong>";
                                echo "<div style='margin-left: 15px; margin-top: 5px;'>";
                                
                                foreach ($items as $item) {
                                    if ($item['type'] === 'folder') {
                                        $itemCount = is_readable($item['path']) ? count(scandir($item['path'])) - 2 : 'Unknown';
                                        echo "<div style='margin: 3px 0; font-family: monospace;'>";
                                        echo "📁 ";
                                        $folderParam = urlencode($item['path']);
                                        echo "<a href='media-viewer.php?folder=" . $folderParam . "' class='folder-link' style='text-decoration: none; color: #007bff; font-weight: bold;'>";
                                        echo htmlspecialchars($item['name']) . "</a>";
                                        echo " <span style='color: #6c757d; font-size: 12px;'>(" . $itemCount . " items)</span>";
                                        echo "</div>";
                                    } else {
                                        $size = is_readable($item['path']) ? filesize($item['path']) : 0;
                                        $sizeFormatted = formatBytes($size);
                                        $extension = pathinfo($item['name'], PATHINFO_EXTENSION);
                                        
                                        echo "<div style='margin: 3px 0; font-family: monospace; color: #666; font-size: 13px;'>";
                                        echo "📄 " . htmlspecialchars($item['name']);
                                        echo " <span style='color: #999; font-size: 11px;'>(" . $sizeFormatted . ")";
                                        if ($extension) {
                                            echo " [" . strtoupper($extension) . "]";
                                        }
                                        echo "</span>";
                                        echo "</div>";
                                    }
                                }
                                
                                echo "</div>";
                                echo "</div>";
                            }
                        }
                    } else {
                        echo "<p style='color: #6c757d; font-style: italic;'>No files or folders found.</p>";
                    }
                    
                    echo "</div>";
                    
                } catch (Exception $e) {
                    echo "<div class='folder-section' style='margin: 20px 0; padding: 15px; border: 1px solid #dc3545; border-radius: 8px; background: #f8d7da;'>";
                    echo "<h4 style='color: #dc3545; margin-top: 0;'>⚠️ " . htmlspecialchars($folderName) . "</h4>";
                    echo "<p style='color: #721c24;'>Error reading directory: " . htmlspecialchars($e->getMessage()) . "</p>";
                    echo "</div>";
                }
            }
            function displayMediaGallery($folderPath) {
                if (!is_dir($folderPath) || !is_readable($folderPath)) {
                    echo "<p style='color: red;'>Cannot access folder: " . htmlspecialchars($folderPath) . "</p>";
                    return;
                }
                
                // Supported image and video extensions
                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv', '3gp'];
                
                $mediaFiles = [];
                $items = scandir($folderPath);
                
                if ($items === false) {
                    echo "<p style='color: red;'>Cannot read folder contents</p>";
                    return;
                }
                
                foreach ($items as $item) {
                    if ($item === '.' || $item === '..') continue;
                    
                    $fullPath = $folderPath . DIRECTORY_SEPARATOR . $item;
                    if (is_file($fullPath)) {
                        $extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                        
                        if (in_array($extension, $imageExtensions)) {
                            $mediaFiles[] = [
                                'name' => $item,
                                'path' => $fullPath,
                                'type' => 'image',
                                'extension' => $extension
                            ];
                        } elseif (in_array($extension, $videoExtensions)) {
                            $mediaFiles[] = [
                                'name' => $item,
                                'path' => $fullPath,
                                'type' => 'video',
                                'extension' => $extension
                            ];
                        }
                    }
                }
                
                if (empty($mediaFiles)) {
                    echo "<p>No images or videos found in this folder.</p>";
                    return;
                }
                
                echo "<h3>Media Gallery: " . htmlspecialchars(basename($folderPath)) . "</h3>";
                echo "<p>Found " . count($mediaFiles) . " media files</p>";
                echo "<div class='media-gallery'>";
                
                foreach ($mediaFiles as $media) {
                    echo "<div class='media-item'>";
                    
                    // Media type badge
                    $typeClass = $media['type'] === 'image' ? 'type-image' : 'type-video';
                    echo "<span class='media-type {$typeClass}'>" . strtoupper($media['type']) . "</span>";
                    
                    if ($media['type'] === 'image') {
                        // Convert Windows path to web-accessible path
                        $webPath = str_replace(['D:\\Entertainments-videos\\', '\\'], ['../media/', '/'], $media['path']);
                        echo "<img src='{$webPath}' alt='" . htmlspecialchars($media['name']) . "' loading='lazy'>";
                    } else {
                        // For videos
                        $webPath = str_replace(['D:\\Entertainments-videos\\', '\\'], ['../media/', '/'], $media['path']);
                        echo "<video controls preload='metadata'>";
                        echo "<source src='{$webPath}' type='video/{$media['extension']}'>";
                        echo "Your browser does not support the video tag.";
                        echo "</video>";
                    }
                    
                    echo "<div class='media-filename'>" . htmlspecialchars($media['name']) . "</div>";
                    echo "</div>";
                }
                
                echo "</div>";
            }
            
            function formatBytes($size, $precision = 2) {
                if ($size == 0) return '0 B';
                $base = log($size, 1024);
                $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
                return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
            }
            
            // Display D drive contents organized by main folders
            echo "<div style='max-height: 600px; overflow-y: auto; border: 1px solid #ddd; padding: 20px; background: #fafafa; border-radius: 8px;'>";
            echo "<h3 style='color: #333; margin-top: 0;'>🗂️ D:\\ Entertainment Videos - Organized by Folders</h3>";
            echo "<p style='color: #666; font-size: 14px; margin-bottom: 20px;'>Each main folder is displayed separately with alphabetically organized content. Click folder names to view in media viewer.</p>";
            
            $d_drive = 'D:\\Entertainments-videos';
            
            if (is_dir($d_drive)) {
                // Get all main folders in the D drive
                $mainFolders = array_filter(glob($d_drive . '\\*'), 'is_dir');
                
                if (!empty($mainFolders)) {
                    // Sort main folders alphabetically
                    sort($mainFolders);
                    
                    echo "<div style='margin-bottom: 15px; padding: 10px; background: #e7f3ff; border-radius: 5px; border-left: 4px solid #007bff;'>";
                    echo "<strong>📋 Found " . count($mainFolders) . " main folders:</strong> ";
                    $folderNames = array_map('basename', $mainFolders);
                    echo "<span style='color: #666;'>" . implode(', ', $folderNames) . "</span>";
                    echo "</div>";
                    
                    // Display each main folder separately
                    foreach ($mainFolders as $mainFolder) {
                        $folderName = basename($mainFolder);
                        displayFolderSection($mainFolder, $folderName);
                    }
                } else {
                    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
                    echo "<strong>⚠️ No folders found</strong><br>";
                    echo "The D:\\Entertainments-videos directory exists but contains no subfolders.";
                    echo "</div>";
                }
            } else {
                echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
                echo "<strong>❌ D drive not found or not accessible</strong><br>";
                echo "<strong>Checked path:</strong> " . htmlspecialchars($d_drive) . "<br><br>";
                echo "<strong>Please verify that:</strong>";
                echo "<ul style='margin: 10px 0;'>";
                echo "<li>The D drive exists on this system</li>";
                echo "<li>The 'Entertainments-videos' folder exists in D drive</li>";
                echo "<li>PHP has permission to read the directory</li>";
                echo "</ul>";
                echo "</div>";
            }
            
            echo "</div>";
            ?>
        </div>

        <?php if (!empty($selectedFolder)): ?>
            <a href="./image.php?folder=<?php echo urlencode($selectedFolder); ?>">View Images from '<?php echo htmlspecialchars(basename($selectedFolder)); ?>' in Detail</a>
        <?php else: ?>
            <a href="./image-display.php">View All Website Descriptions</a>
        <?php endif; ?>
    </div>
</body>
</html>
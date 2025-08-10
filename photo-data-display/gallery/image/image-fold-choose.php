<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Gallery - D Drive Browser</title>
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
        
        /* Image Gallery Styles */
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
        
        /* File link styles */
        .file-link {
            transition: color 0.2s, background-color 0.2s;
            padding: 2px 4px;
            border-radius: 3px;
        }
        .file-link:hover {
            background-color: #e8f5e8;
            color: #1e7e34 !important;
            text-decoration: underline !important;
        }
        
        /* JavaScript-enhanced styles */
        .search-controls {
            margin: 15px 0;
            padding: 15px;
            background: #e9ecef;
            border-radius: 8px;
            border-left: 4px solid #6c757d;
        }
        .search-controls input, .search-controls select {
            margin: 5px;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .control-buttons {
            margin: 10px 0;
            text-align: center;
        }
        .control-buttons button {
            margin: 5px;
            padding: 8px 16px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .control-buttons button:hover {
            background: #218838;
        }
        .folder-item, .file-item {
            transition: background-color 0.2s;
        }
        .folder-item:hover, .file-item:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="back-link">
            <a href="../../top.php">← Back to Top</a>
        </div>
        
        <h1>Image Gallery</h1>

        <!-- D Drive File Browser -->
        <div class="folder-selector">
            <h2>D Drive Browser</h2>
            <p>Browse files and folders in D drive:</p>
            
            <?php
            function displaySimpleFolderSectionG($folderPath, $folderName, $maxDepth = 3, $currentDepth = 0) {
                if (!is_dir($folderPath) || !is_readable($folderPath)) {
                    if ($currentDepth === 0) {
                        echo "<div class='folder-section' style='margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa;'>";
                        echo "<h4 style='color: #dc3545; margin-top: 0;'>❌ " . htmlspecialchars($folderName) . "</h4>";
                        echo "<p style='color: red;'>Cannot access folder: " . htmlspecialchars($folderPath) . "</p>";
                        echo "</div>";
                    }
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
                        $extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                        
                        # Only process if items are images
                        if (is_file($fullPath) && preg_match('/\.(jpg|jpeg|png|gif|bmp|webp|svg|tiff|ico)$/i', $item)) {
                            // This is an image file, continue processing
                        } else if (is_dir($fullPath)) {
                            $folders[] = $item;
                            continue;
                        } else {
                            // Skip non-image files
                            continue;
                        }

                        if (is_dir($fullPath)) {
                            $folders[] = $item;
                        } else {
                            $files[] = $item;
                        }
                    }
                    
                    // Count only image files, not all files
                    $imageFiles = [];
                    foreach ($files as $file) {
                        $fullPath = $folderPath . DIRECTORY_SEPARATOR . $file;
                        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff', 'ico'])) {
                            $imageFiles[] = $file;
                        }
                    }
                    
                    $totalImages = count($imageFiles);
                    $totalFolders = count($folders);
                    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $currentDepth);
                    
                    // Display folder section header (only for main folders)
                    if ($currentDepth === 0) {
                        $sectionId = "section-" . preg_replace('/[^a-zA-Z0-9]/', '', $folderName);
                        echo "<div class='folder-section' id='" . $sectionId . "' style='margin: 25px 0; padding: 20px; border: 2px solid #007bff; border-radius: 10px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); box-shadow: 0 4px 8px rgba(0,0,0,0.1);'>";
                        echo "<h3 style='color: #007bff; margin-top: 0; border-bottom: 3px solid #007bff; padding-bottom: 10px; font-size: 20px;'>";
                        echo "📁 D:\\My Drive\\Entertainment\\" . htmlspecialchars($folderName) . " <span class='item-counter' style='font-size: 16px; color: #28a745; font-weight: bold;'>🖼️ " . $totalImages . " images</span>";
                        echo "<button onclick='FolderBrowser.toggleFolderSection(\"" . $sectionId . "\")' data-section='" . $sectionId . "' style='float: right; padding: 4px 8px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px;'>▼ Hide</button>";
                        echo "</h3>";
                    } else {
                        // Subdirectory header
                        echo "<div style='margin: 12px 0; margin-left: " . ($currentDepth * 25) . "px; padding: 8px; background: #e9ecef; border-left: 4px solid #6c757d; border-radius: 5px;'>";
                        echo "<h5 style='margin: 0; color: #495057; font-size: 14px;'>";
                        echo $indent . "📂 " . htmlspecialchars($folderName) . " <span style='font-size: 12px; color: #28a745; font-weight: bold;'>🖼️ " . $totalImages . " images</span>";
                        echo "</h5>";
                    }
                    
                    // Display folders first (no alphabetical grouping)
                    if (!empty($folders)) {
                        $folderIndent = str_repeat('&nbsp;&nbsp;', $currentDepth * 3);
                        echo "<div style='margin: 10px 0; margin-left: " . ($currentDepth * 20) . "px;'>";
                        echo "<div style='margin-bottom: 8px;'><strong style='color: #28a745; font-size: 14px;'>📁 Folders:</strong></div>";
                        
                        foreach ($folders as $folder) {
                            $fullPath = $folderPath . DIRECTORY_SEPARATOR . $folder;
                            $imageCount = countImagesInFolder($fullPath);

                            if ($imageCount === 0) {
                                continue;
                            }
                            
                            echo "<div style='margin: 4px 0; margin-left: 15px; font-family: monospace;'>";
                            echo $folderIndent . "📁 ";
                            $folderParam = urlencode($fullPath);
                            echo "<a href='../common/folder-viewer.php?folder=" . $folderParam . "' class='folder-link' style='text-decoration: none; color: #007bff; font-weight: bold; font-size: 14px;'>";
                            echo htmlspecialchars($folder) . "</a>";
                            echo " <span style='color: #28a745; font-size: 12px; font-weight: bold;'>🖼️ " . $imageCount . " images</span>";
                            echo "</div>";
                        }
                        echo "</div>";
                    }
                    
                    // Display image files only (no alphabetical grouping)
                    if (!empty($imageFiles)) {
                        $fileIndent = str_repeat('&nbsp;&nbsp;', $currentDepth * 3);
                        echo "<div style='margin: 10px 0; margin-left: " . ($currentDepth * 20) . "px;'>";
                        echo "<div style='margin-bottom: 8px;'><strong style='color: #17a2b8; font-size: 14px;'>�️ Images:</strong></div>";
                        
                        foreach ($imageFiles as $file) {
                            $fullPath = $folderPath . DIRECTORY_SEPARATOR . $file;
                            $size = is_readable($fullPath) ? filesize($fullPath) : 0;
                            $sizeFormatted = formatBytes($size);
                            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            
                            echo "<div class='file-item' style='margin: 3px 0; margin-left: 15px; font-family: monospace; color: #666; font-size: 13px;' data-path='" . htmlspecialchars($fullPath) . "'>";
                            echo $fileIndent . "�️ ";
                            
                            // Display image files with special styling
                            echo "<span style='color: #28a745; font-weight: bold;'>" . htmlspecialchars($file) . "</span>";
                            
                            echo " <span style='color: #999; font-size: 11px;'>(" . $sizeFormatted . ")";
                            if ($extension) {
                                echo " [" . strtoupper($extension) . "] 🖼️";
                            }
                            echo "</span>";
                            echo "</div>";
                        }
                        echo "</div>";
                    }
                    
                    
                    // Close subdirectory section
                    if ($currentDepth > 0) {
                        echo "</div>";
                    }
                    
                    // Recursively display subdirectories if within depth limit
                    if ($currentDepth < $maxDepth - 1) {
                        foreach ($folders as $folder) {
                            $subFolderPath = $folderPath . DIRECTORY_SEPARATOR . $folder;
                            if (is_readable($subFolderPath)) {
                                displaySimpleFolderSection($subFolderPath, $folder, $maxDepth, $currentDepth + 1);
                            }
                        }
                    }
                    
                    // Close main folder section
                    if ($currentDepth === 0) {
                        echo "</div>";
                    }
                    
                } catch (Exception $e) {
                    if ($currentDepth === 0) {
                        echo "<div class='folder-section' style='margin: 20px 0; padding: 15px; border: 1px solid #dc3545; border-radius: 8px; background: #f8d7da;'>";
                        echo "<h4 style='color: #dc3545; margin-top: 0;'>⚠️ " . htmlspecialchars($folderName) . "</h4>";
                        echo "<p style='color: #721c24;'>Error reading directory: " . htmlspecialchars($e->getMessage()) . "</p>";
                        echo "</div>";
                    }
                }
            }
            function displayMediaGallery($folderPath) {
                if (!is_dir($folderPath) || !is_readable($folderPath)) {
                    echo "<p style='color: red;'>Cannot access folder: " . htmlspecialchars($folderPath) . "</p>";
                    return;
                }
                
                // Supported image extensions only
                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff', 'ico'];
                
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
                        }
                    }
                }
                
                if (empty($mediaFiles)) {
                    echo "<p>No images found in this folder.</p>";
                    return;
                }
                
                echo "<h3>Image Gallery: " . htmlspecialchars(basename($folderPath)) . "</h3>";
                echo "<p>Found " . count($mediaFiles) . " image files</p>";
                echo "<div class='media-gallery'>";
                
                foreach ($mediaFiles as $media) {
                    echo "<div class='media-item'>";
                    
                    // Image type badge
                    echo "<span class='media-type type-image'>IMAGE</span>";
                    
                    // Convert Windows path to web-accessible path
                    $webPath = str_replace(['D:\\Entertainments-videos\\', '\\'], ['../media/', '/'], $media['path']);
                    echo "<img src='{$webPath}' alt='" . htmlspecialchars($media['name']) . "' loading='lazy'>";
                    
                    echo "<div class='media-filename'>" . htmlspecialchars($media['name']) . "</div>";
                    echo "</div>";
                }
                
                echo "</div>";
            }
            
            // formatBytes function already declared above, do not redeclare here.
            
            // Display D drive contents organized by main folders - Simple Direct Listing
            echo "<div style='max-height: 800px; overflow-y: auto; border: 1px solid #ddd; padding: 25px; background: #fafafa; border-radius: 8px;'>";
            echo "<h2 style='color: #333; margin-top: 0; text-align: center;'>🖼️ D:\\Entertainments-videos - Image Gallery Browser</h2>";
            
            // Depth control
            $selectedDepth = $_GET['depth'] ?? 3;
            echo "<div style='margin-bottom: 25px; padding: 15px; background: #e7f3ff; border-radius: 8px; border-left: 4px solid #007bff;'>";
            echo "<strong>🔍 Directory Depth Control:</strong> ";
            echo "<form method='GET' style='display: inline-block; margin-left: 10px;'>";
            // Preserve other GET parameters
            if (!empty($_GET['folder'])) {
                echo "<input type='hidden' name='folder' value='" . htmlspecialchars($_GET['folder']) . "'>";
            }
            echo "<select name='depth' onchange='this.form.submit()' style='padding: 8px 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 14px;'>";
            echo "<option value='1'" . ($selectedDepth == 1 ? ' selected' : '') . ">1 level (Surface only)</option>";
            echo "<option value='2'" . ($selectedDepth == 2 ? ' selected' : '') . ">2 levels (One level deep)</option>";
            echo "<option value='3'" . ($selectedDepth == 3 ? ' selected' : '') . ">3 levels (Recommended)</option>";
            echo "<option value='4'" . ($selectedDepth == 4 ? ' selected' : '') . ">4 levels (Very detailed)</option>";
            echo "<option value='5'" . ($selectedDepth == 5 ? ' selected' : '') . ">5 levels (Maximum depth)</option>";
            echo "</select>";
            echo "</form>";
            echo "<p style='color: #666; font-size: 14px; margin: 10px 0 0 0;'>Each main folder (A, B, C, D, E, etc.) is displayed separately. No alphabetical grouping - just direct file/folder listing.</p>";
            echo "</div>";
            
            $d_drive = 'D:\\Entertainments-videos';
            
            if (is_dir($d_drive)) {
                // Get all main folders in the D drive
                $mainFolders = array_filter(glob($d_drive . '\\*'), 'is_dir');
                
                if (!empty($mainFolders)) {
                    // Sort main folders alphabetically by name
                    sort($mainFolders);
                    
                    // Count total images across all folders
                    $totalImages = 0;
                    foreach ($mainFolders as $folder) {
                        $totalImages += countImagesRecursive($folder, intval($selectedDepth));
                    }
                    
            echo "<div style='margin-bottom: 25px; padding: 15px; background: #d4edda; border-radius: 8px; border-left: 4px solid #28a745; text-align: center;'>";
            echo "<strong style='font-size: 16px;'>📋 Found " . count($mainFolders) . " main folders with 🖼️ " . number_format($totalImages) . " total images (scanning " . $selectedDepth . " levels deep)</strong><br>";
            echo "<div style='margin-top: 10px; font-size: 15px; color: #495057;'>";
            foreach ($mainFolders as $folder) {
                $folderName = basename($folder);
                $folderImageCount = countImagesRecursive($folder, intval($selectedDepth));
                echo "<span style='display: inline-block; margin: 3px 8px; padding: 4px 8px; background: #fff; border-radius: 4px; color: #007bff; font-weight: bold;'>";
                echo "D:\\Entertainments-videos\\" . $folderName;
                echo " <span style='color: #28a745;'>🖼️ " . $folderImageCount . "</span>";
                echo "</span>";
            }
            echo "</div>";
            echo "</div>";
            
            // Add search and filter controls
            echo "<div class='search-controls'>";
            echo "<strong>🔍 Search & Filter Controls:</strong><br>";
            echo "<input type='text' id='folder-search' placeholder='🔍 Search folders and image files...' style='width: 300px; margin-right: 10px;'>";
            echo "<select id='type-filter'>";
            echo "<option value='all'>All Items</option>";
            echo "<option value='folders'>📁 Folders Only</option>";
            echo "<option value='files'>📄 Files Only</option>";
            echo "<option value='images'>🖼️ Images Only</option>";
            echo "</select>";
            echo "<div class='control-buttons'>";
            echo "<button onclick='FolderBrowser.expandAllSections()'>▼ Expand All</button>";
            echo "<button onclick='FolderBrowser.collapseAllSections()'>▶ Collapse All</button>";
            echo "<button onclick='FolderBrowser.showOnlyFoldersWithImages()' style='background: #28a745; color: white;'>🖼️ Show Only Folders With Images</button>";
            echo "<button onclick='FolderBrowser.showAllFolders()' style='background: #6c757d; color: white;'>📁 Show All Folders</button>";
            echo "<button onclick='FolderBrowser.showNotification(\"Folder browser ready!\", \"success\")'>✅ Test Notification</button>";
            echo "</div>";
            echo "<small style='color: #666;'>💡 Tip: Right-click on folders/files for context menu. Use Ctrl+F to focus search. Press Escape to clear search. Only image files are displayed.</small>";
            echo "</div>";                    // Display each main folder separately with simple listing (no alphabetical grouping)
                    foreach ($mainFolders as $mainFolder) {
                        $folderName = basename($mainFolder);
                        displaySimpleFolderSectionG($mainFolder, $folderName, intval($selectedDepth), 0);
                    }
                } else {
                    echo "<div style='background: #fff3cd; padding: 20px; border-radius: 8px; border-left: 4px solid #ffc107; text-align: center;'>";
                    echo "<strong style='font-size: 18px;'>⚠️ No folders found</strong><br>";
                    echo "<p style='margin: 10px 0 0 0;'>The D:\\Entertainments-videos directory exists but contains no subfolders.</p>";
                    echo "</div>";
                }
            } else {
                echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; border-left: 4px solid #dc3545;'>";
                echo "<strong style='font-size: 18px;'>❌ D drive not found or not accessible</strong><br>";
                echo "<strong>Checked path:</strong> " . htmlspecialchars($d_drive) . "<br><br>";
                echo "<strong>Please verify that:</strong>";
                echo "<ul style='margin: 15px 0; text-align: left;'>";
                echo "<li>The D drive exists on this system</li>";
                echo "<li>The 'Entertainments-videos' folder exists in D drive</li>";
                echo "<li>PHP has permission to read the directory</li>";
                echo "</ul>";
                echo "</div>";
            }
            
            echo "</div>";
            ?>
        </div>

        <!-- <?php if (!empty($selectedFolder)): ?>
            <a href="./image.php?folder=<?php echo urlencode($selectedFolder); ?>">View Images from '<?php echo htmlspecialchars(basename($selectedFolder)); ?>' in Detail</a>
        <?php else: ?>
            <a href="./image-display.php">View All Website Descriptions</a>
        <?php endif; ?> -->
    </div>
    
    <!-- Include JavaScript functionality -->
    <script src="../common/folder-browser.js"></script>
    
    <script>
        // Additional page-specific JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🎯 Page-specific JavaScript loaded');
            
            // Add classes to folder and file items for JavaScript targeting
            document.querySelectorAll('.folder-link').forEach(link => {
                link.parentElement.classList.add('folder-item');
            });
            
            // Add data attributes for file paths
            document.querySelectorAll('div[style*="font-family: monospace"]').forEach(item => {
                if (item.textContent.includes('📄')) {
                    item.classList.add('file-item');
                    // Try to extract file path from the text
                    const pathMatch = item.textContent.match(/([^\\]+\\[^\\]+)$/);
                    if (pathMatch) {
                        item.dataset.path = pathMatch[1];
                    }
                }
            });
            
            // Add click handlers for enhanced functionality
            document.querySelectorAll('.folder-link').forEach(link => {
                // Add middle-click support for new tabs
                link.addEventListener('mousedown', function(e) {
                    if (e.button === 1) { // Middle mouse button
                        e.preventDefault();
                        const href = this.getAttribute('href');
                        const match = href.match(/folder=([^&]+)/);
                        if (match) {
                            const folderPath = decodeURIComponent(match[1]);
                            FolderBrowser.openMediaViewerNewTab(folderPath, e);
                        }
                    }
                });
                
                // Add Ctrl+Click support for new tabs
                link.addEventListener('click', function(e) {
                    if (e.ctrlKey) {
                        e.preventDefault();
                        const href = this.getAttribute('href');
                        const match = href.match(/folder=([^&]+)/);
                        if (match) {
                            const folderPath = decodeURIComponent(match[1]);
                            FolderBrowser.openMediaViewerNewTab(folderPath, e);
                        }
                    }
                });
            });
            
            // Show welcome notification
            setTimeout(() => {
                FolderBrowser.showNotification('🎉 Enhanced folder browser loaded! Try right-clicking folders or using search.', 'success', 5000);
            }, 1000);
        });
    </script>


        <!-- G Drive File Browser -->
        <div class="folder-selector">
            <h2>G Drive Browser</h2>
            <p>Browse files and folders in G drive:</p>

            <?php
            function displaySimpleFolderSection($folderPath, $folderName, $maxDepth = 3, $currentDepth = 0) {
                if (!is_dir($folderPath) || !is_readable($folderPath)) {
                    if ($currentDepth === 0) {
                        echo "<div class='folder-section' style='margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa;'>";
                        echo "<h4 style='color: #dc3545; margin-top: 0;'>❌ " . htmlspecialchars($folderName) . "</h4>";
                        echo "<p style='color: red;'>Cannot access folder: " . htmlspecialchars($folderPath) . "</p>";
                        echo "</div>";
                    }
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
                        $extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                        
                        # Only process if items are images
                        if (is_file($fullPath) && preg_match('/\.(jpg|jpeg|png|gif|bmp|webp|svg|tiff|ico)$/i', $item)) {
                            // This is an image file, continue processing
                        } else if (is_dir($fullPath)) {
                            $folders[] = $item;
                            continue;
                        } else {
                            // Skip non-image files
                            continue;
                        }

                        if (is_dir($fullPath)) {
                            $folders[] = $item;
                        } else {
                            $files[] = $item;
                        }
                    }
                    
                    // Count only image files, not all files
                    $imageFiles = [];
                    foreach ($files as $file) {
                        $fullPath = $folderPath . DIRECTORY_SEPARATOR . $file;
                        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff', 'ico'])) {
                            $imageFiles[] = $file;
                        }
                    }
                    
                    $totalImages = count($imageFiles);
                    $totalFolders = count($folders);
                    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $currentDepth);
                    
                    // Display folder section header (only for main folders)
                    if ($currentDepth === 0) {
                        $sectionId = "section-" . preg_replace('/[^a-zA-Z0-9]/', '', $folderName);
                        echo "<div class='folder-section' id='" . $sectionId . "' style='margin: 25px 0; padding: 20px; border: 2px solid #007bff; border-radius: 10px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); box-shadow: 0 4px 8px rgba(0,0,0,0.1);'>";
                        echo "<h3 style='color: #007bff; margin-top: 0; border-bottom: 3px solid #007bff; padding-bottom: 10px; font-size: 20px;'>";
                        echo "📁 G:\\My Drive\\Entertainment\\" . htmlspecialchars($folderName) . " <span class='item-counter' style='font-size: 16px; color: #28a745; font-weight: bold;'>🖼️ " . $totalImages . " images</span>";
                        echo "<button onclick='FolderBrowser.toggleFolderSection(\"" . $sectionId . "\")' data-section='" . $sectionId . "' style='float: right; padding: 4px 8px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px;'>▼ Hide</button>";
                        echo "</h3>";
                    } else {
                        // Subdirectory header
                        echo "<div style='margin: 12px 0; margin-left: " . ($currentDepth * 25) . "px; padding: 8px; background: #e9ecef; border-left: 4px solid #6c757d; border-radius: 5px;'>";
                        echo "<h5 style='margin: 0; color: #495057; font-size: 14px;'>";
                        echo $indent . "📂 " . htmlspecialchars($folderName) . " <span style='font-size: 12px; color: #28a745; font-weight: bold;'>🖼️ " . $totalImages . " images</span>";
                        echo "</h5>";
                    }
                    
                    // Display folders first (no alphabetical grouping)
                    if (!empty($folders)) {
                        $folderIndent = str_repeat('&nbsp;&nbsp;', $currentDepth * 3);
                        echo "<div style='margin: 10px 0; margin-left: " . ($currentDepth * 20) . "px;'>";
                        echo "<div style='margin-bottom: 8px;'><strong style='color: #28a745; font-size: 14px;'>📁 Folders:</strong></div>";
                        
                        foreach ($folders as $folder) {
                            $fullPath = $folderPath . DIRECTORY_SEPARATOR . $folder;
                            $imageCount = countImagesInFolder($fullPath);
                            if ($imageCount === 0) {
                                continue;
                            }
                            
                            echo "<div style='margin: 4px 0; margin-left: 15px; font-family: monospace;'>";
                            echo $folderIndent . "📁 ";
                            $folderParam = urlencode($fullPath);
                            echo "<a href='../common/folder-viewer.php?folder=" . $folderParam . "' class='folder-link' style='text-decoration: none; color: #007bff; font-weight: bold; font-size: 14px;'>";
                            echo htmlspecialchars($folder) . "</a>";
                            echo " <span style='color: #28a745; font-size: 12px; font-weight: bold;'>🖼️ " . $imageCount . " images</span>";
                            echo "</div>";
                        }
                        echo "</div>";
                    }
                    
                    // Display image files only (no alphabetical grouping)
                    if (!empty($imageFiles)) {
                        $fileIndent = str_repeat('&nbsp;&nbsp;', $currentDepth * 3);
                        echo "<div style='margin: 10px 0; margin-left: " . ($currentDepth * 20) . "px;'>";
                        echo "<div style='margin-bottom: 8px;'><strong style='color: #17a2b8; font-size: 14px;'>�️ Images:</strong></div>";
                        
                        foreach ($imageFiles as $file) {
                            $fullPath = $folderPath . DIRECTORY_SEPARATOR . $file;
                            $size = is_readable($fullPath) ? filesize($fullPath) : 0;
                            $sizeFormatted = formatBytes($size);
                            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            
                            echo "<div class='file-item' style='margin: 3px 0; margin-left: 15px; font-family: monospace; color: #666; font-size: 13px;' data-path='" . htmlspecialchars($fullPath) . "'>";
                            echo $fileIndent . "�️ ";
                            
                            echo "<span style='color: #28a745; font-weight: bold;'>" . htmlspecialchars($file) . "</span>";
                            
                            echo " <span style='color: #999; font-size: 11px;'>(" . $sizeFormatted . ")";
                            if ($extension) {
                                echo " [" . strtoupper($extension) . "] 🖼️";
                            }
                            echo "</span>";
                            echo "</div>";
                        }
                        echo "</div>";
                    }
                    
                    // Close subdirectory section
                    if ($currentDepth > 0) {
                        echo "</div>";
                    }
                    
                    // Recursively display subdirectories if within depth limit
                    if ($currentDepth < $maxDepth - 1) {
                        foreach ($folders as $folder) {
                            $subFolderPath = $folderPath . DIRECTORY_SEPARATOR . $folder;
                            if (is_readable($subFolderPath)) {
                                displaySimpleFolderSection($subFolderPath, $folder, $maxDepth, $currentDepth + 1);
                            }
                        }
                    }
                    
                    // Close main folder section
                    if ($currentDepth === 0) {
                        echo "</div>";
                    }
                    
                } catch (Exception $e) {
                    if ($currentDepth === 0) {
                        echo "<div class='folder-section' style='margin: 20px 0; padding: 15px; border: 1px solid #dc3545; border-radius: 8px; background: #f8d7da;'>";
                        echo "<h4 style='color: #dc3545; margin-top: 0;'>⚠️ " . htmlspecialchars($folderName) . "</h4>";
                        echo "<p style='color: #721c24;'>Error reading directory: " . htmlspecialchars($e->getMessage()) . "</p>";
                        echo "</div>";
                    }
                }
            }
            function displayMediaGalleryG($folderPath) {
                if (!is_dir($folderPath) || !is_readable($folderPath)) {
                    echo "<p style='color: red;'>Cannot access folder: " . htmlspecialchars($folderPath) . "</p>";
                    return;
                }
                
                // Supported image extensions only
                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff', 'ico'];
                
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
                        }
                    }
                }
                
                if (empty($mediaFiles)) {
                    echo "<p>No images found in this folder.</p>";
                    return;
                }
                
                echo "<h3>Image Gallery: " . htmlspecialchars(basename($folderPath)) . "</h3>";
                echo "<p>Found " . count($mediaFiles) . " image files</p>";
                echo "<div class='media-gallery'>";
                
                foreach ($mediaFiles as $media) {
                    echo "<div class='media-item'>";
                    
                    // Image type badge
                    echo "<span class='media-type type-image'>IMAGE</span>";
                    
                    // Convert Windows path to web-accessible path
                    $webPath = str_replace(['G:\\My Drive\\Entertainment\\', '\\'], ['../media/', '/'], $media['path']);
                    echo "<img src='{$webPath}' alt='" . htmlspecialchars($media['name']) . "' loading='lazy'>";
                    
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

            // Function to count images in a folder
            function countImagesInFolder($folderPath) {
                if (!is_dir($folderPath) || !is_readable($folderPath)) {
                    return 0;
                }
                
                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff', 'ico'];
                $imageCount = 0;
                
                try {
                    $items = scandir($folderPath);
                    if ($items === false) {
                        return 0;
                    }
                    
                    foreach ($items as $item) {
                        if ($item === '.' || $item === '..') continue;
                        
                        $fullPath = $folderPath . DIRECTORY_SEPARATOR . $item;
                        if (is_file($fullPath)) {
                            $extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                            if (in_array($extension, $imageExtensions)) {
                                $imageCount++;
                            }
                        }
                    }
                } catch (Exception $e) {
                    return 0;
                }
                
                return $imageCount;
            }
            
            // Function to count images recursively in a folder
            function countImagesRecursive($folderPath, $maxDepth = 2, $currentDepth = 0) {
                if ($currentDepth >= $maxDepth || !is_dir($folderPath) || !is_readable($folderPath)) {
                    return 0;
                }
                
                $totalImages = countImagesInFolder($folderPath);
                
                try {
                    $items = scandir($folderPath);
                    if ($items !== false) {
                        foreach ($items as $item) {
                            if ($item === '.' || $item === '..') continue;
                            
                            $fullPath = $folderPath . DIRECTORY_SEPARATOR . $item;
                            if (is_dir($fullPath)) {
                                $totalImages += countImagesRecursive($fullPath, $maxDepth, $currentDepth + 1);
                            }
                        }
                    }
                } catch (Exception $e) {
                    // Continue with current count
                }
                
                return $totalImages;
            }
            
            // Display G drive contents organized by main folders - Simple Direct Listing
            echo "<div style='max-height: 800px; overflow-y: auto; border: 1px solid #ddd; padding: 25px; background: #fafafa; border-radius: 8px;'>";
            echo "<h2 style='color: #333; margin-top: 0; text-align: center;'>�️ G:\\My Drive\\Entertainment - Image Gallery Browser</h2>";
            
            // Depth control
            $selectedDepth = $_GET['depth'] ?? 3;
            echo "<div style='margin-bottom: 25px; padding: 15px; background: #e7f3ff; border-radius: 8px; border-left: 4px solid #007bff;'>";
            echo "<strong>🔍 Directory Depth Control:</strong> ";
            echo "<form method='GET' style='display: inline-block; margin-left: 10px;'>";
            // Preserve other GET parameters
            if (!empty($_GET['folder'])) {
                echo "<input type='hidden' name='folder' value='" . htmlspecialchars($_GET['folder']) . "'>";
            }
            echo "<select name='depth' onchange='this.form.submit()' style='padding: 8px 12px; border-radius: 5px; border: 1px solid #ccc; font-size: 14px;'>";
            echo "<option value='1'" . ($selectedDepth == 1 ? ' selected' : '') . ">1 level (Surface only)</option>";
            echo "<option value='2'" . ($selectedDepth == 2 ? ' selected' : '') . ">2 levels (One level deep)</option>";
            echo "<option value='3'" . ($selectedDepth == 3 ? ' selected' : '') . ">3 levels (Recommended)</option>";
            echo "<option value='4'" . ($selectedDepth == 4 ? ' selected' : '') . ">4 levels (Very detailed)</option>";
            echo "<option value='5'" . ($selectedDepth == 5 ? ' selected' : '') . ">5 levels (Maximum depth)</option>";
            echo "</select>";
            echo "</form>";
            echo "<p style='color: #666; font-size: 14px; margin: 10px 0 0 0;'>Each main folder (A, B, C, D, E, etc.) is displayed separately. No alphabetical grouping - just direct file/folder listing.</p>";
            echo "</div>";
            
            $g_drive = 'G:\My Drive\Entertainment';

            if (is_dir($g_drive)) {
                // Get all main folders in the G drive
                $mainFolders = array_filter(glob($g_drive . '\\*'), 'is_dir');

                if (!empty($mainFolders)) {
                    // Sort main folders alphabetically by name
                    sort($mainFolders);
                    
                    // Count total images across all folders
                    $totalImages = 0;
                    foreach ($mainFolders as $folder) {
                        $totalImages += countImagesRecursive($folder, intval($selectedDepth));
                    }
                    
            echo "<div style='margin-bottom: 25px; padding: 15px; background: #d4edda; border-radius: 8px; border-left: 4px solid #28a745; text-align: center;'>";
            echo "<strong style='font-size: 16px;'>📋 Found " . count($mainFolders) . " main folders with 🖼️ " . number_format($totalImages) . " total images (scanning " . $selectedDepth . " levels deep)</strong><br>";
            echo "<div style='margin-top: 10px; font-size: 15px; color: #495057;'>";
            foreach ($mainFolders as $folder) {
                $folderName = basename($folder);
                $folderImageCount = countImagesRecursive($folder, intval($selectedDepth));
                echo "<span style='display: inline-block; margin: 3px 8px; padding: 4px 8px; background: #fff; border-radius: 4px; color: #007bff; font-weight: bold;'>";
                echo "G:\\My Drive\\Entertainment\\" . $folderName;
                echo " <span style='color: #28a745;'>🖼️ " . $folderImageCount . "</span>";
                echo "</span>";
            }
            echo "</div>";
            echo "</div>";
            
            // Add search and filter controls
            echo "<div class='search-controls'>";
            echo "<strong>🔍 Search & Filter Controls:</strong><br>";
            echo "<input type='text' id='folder-search' placeholder='🔍 Search folders and image files...' style='width: 300px; margin-right: 10px;'>";
            echo "<select id='type-filter'>";
            echo "<option value='all'>All Items</option>";
            echo "<option value='folders'>📁 Folders Only</option>";
            echo "<option value='files'>📄 Files Only</option>";
            echo "<option value='images'>🖼️ Images Only</option>";
            echo "</select>";
            echo "<div class='control-buttons'>";
            echo "<button onclick='FolderBrowser.expandAllSections()'>▼ Expand All</button>";
            echo "<button onclick='FolderBrowser.collapseAllSections()'>▶ Collapse All</button>";
            echo "<button onclick='FolderBrowser.showOnlyFoldersWithImages()' style='background: #28a745; color: white;'>🖼️ Show Only Folders With Images</button>";
            echo "<button onclick='FolderBrowser.showAllFolders()' style='background: #6c757d; color: white;'>📁 Show All Folders</button>";
            echo "<button onclick='FolderBrowser.showNotification(\"Folder browser ready!\", \"success\")'>✅ Test Notification</button>";
            echo "</div>";
            echo "<small style='color: #666;'>💡 Tip: Right-click on folders/files for context menu. Use Ctrl+F to focus search. Press Escape to clear search. Only image files are displayed.</small>";
            echo "</div>";                    // Display each main folder separately with simple listing (no alphabetical grouping)
                    foreach ($mainFolders as $mainFolder) {
                        $folderName = basename($mainFolder);
                        displaySimpleFolderSection($mainFolder, $folderName, intval($selectedDepth), 0);
                    }
                } else {
                    echo "<div style='background: #fff3cd; padding: 20px; border-radius: 8px; border-left: 4px solid #ffc107; text-align: center;'>";
                    echo "<strong style='font-size: 18px;'>⚠️ No folders found</strong><br>";
                    echo "<p style='margin: 10px 0 0 0;'>The G:\\Entertainments directory exists but contains no subfolders.</p>";
                    echo "</div>";
                }
            } else {
                echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; border-left: 4px solid #dc3545;'>";
                echo "<strong style='font-size: 18px;'>❌ G drive not found or not accessible</strong><br>";
                echo "<strong>Checked path:</strong> " . htmlspecialchars($g_drive) . "<br><br>";
                echo "<strong>Please verify that:</strong>";
                echo "<ul style='margin: 15px 0; text-align: left;'>";
                echo "<li>The G drive exists on this system</li>";
                echo "<li>The 'Entertainments-videos' folder exists in G drive</li>";
                echo "<li>PHP has permission to read the directory</li>";
                echo "</ul>";
                echo "</div>";
            }
            
            echo "</div>";
            ?>
        </div>
<!-- 
        <?php if (!empty($selectedFolder)): ?>
            <a href="./image.php?folder=<?php echo urlencode($selectedFolder); ?>">View Images from '<?php echo htmlspecialchars(basename($selectedFolder)); ?>' in Detail</a>
        <?php else: ?>
            <a href="./image-display.php">View All Website Descriptions</a>
        <?php endif; ?> -->
    </div>
    
    <!-- Include JavaScript functionality -->
    <script src="folder-browser.js"></script>
    
    <script>
        // Additional page-specific JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🎯 Page-specific JavaScript loaded');
            
            // Add classes to folder and file items for JavaScript targeting
            document.querySelectorAll('.folder-link').forEach(link => {
                link.parentElement.classList.add('folder-item');
            });
            
            // Add data attributes for file paths
            document.querySelectorAll('div[style*="font-family: monospace"]').forEach(item => {
                if (item.textContent.includes('📄')) {
                    item.classList.add('file-item');
                    // Try to extract file path from the text
                    const pathMatch = item.textContent.match(/([^\\]+\\[^\\]+)$/);
                    if (pathMatch) {
                        item.dataset.path = pathMatch[1];
                    }
                }
            });
            
            // Add click handlers for enhanced functionality
            document.querySelectorAll('.folder-link').forEach(link => {
                // Add middle-click support for new tabs
                link.addEventListener('mousedown', function(e) {
                    if (e.button === 1) { // Middle mouse button
                        e.preventDefault();
                        const href = this.getAttribute('href');
                        const match = href.match(/folder=([^&]+)/);
                        if (match) {
                            const folderPath = decodeURIComponent(match[1]);
                            FolderBrowser.openMediaViewerNewTab(folderPath, e);
                        }
                    }
                });
                
                // Add Ctrl+Click support for new tabs
                link.addEventListener('click', function(e) {
                    if (e.ctrlKey) {
                        e.preventDefault();
                        const href = this.getAttribute('href');
                        const match = href.match(/folder=([^&]+)/);
                        if (match) {
                            const folderPath = decodeURIComponent(match[1]);
                            FolderBrowser.openMediaViewerNewTab(folderPath, e);
                        }
                    }
                });
            });
            
            // Show welcome notification
            setTimeout(() => {
                FolderBrowser.showNotification('🎉 Enhanced folder browser loaded! Try right-clicking folders or using search.', 'success', 5000);
            }, 1000);
        });
        
        // Extend FolderBrowser object with new functions for smart folder filtering
        if (typeof FolderBrowser !== 'undefined') {
            // Function to show only folders that contain images
            FolderBrowser.showOnlyFoldersWithImages = function() {
                console.log('🖼️ Filtering to show only folders with images');
                
                // Get all folder sections
                const folderSections = document.querySelectorAll('.folder-section');
                let hiddenCount = 0;
                let visibleCount = 0;
                
                folderSections.forEach(section => {
                    // Check if this folder has any images
                    const imageCounter = section.querySelector('.item-counter');
                    if (imageCounter) {
                        // Extract image count from the text
                        const counterText = imageCounter.textContent;
                        const imageCountMatch = counterText.match(/🖼️\s*(\d+)/);
                        const imageCount = imageCountMatch ? parseInt(imageCountMatch[1]) : 0;
                        
                        if (imageCount === 0) {
                            // Hide folders with no images
                            section.style.display = 'none';
                            hiddenCount++;
                        } else {
                            // Show folders with images
                            section.style.display = 'block';
                            visibleCount++;
                        }
                    }
                });
                
                this.showNotification(`🖼️ Showing ${visibleCount} folders with images (${hiddenCount} empty folders hidden)`, 'success', 4000);
            };
            
            // Function to show all folders
            FolderBrowser.showAllFolders = function() {
                console.log('📁 Showing all folders');
                
                // Get all folder sections and show them
                const folderSections = document.querySelectorAll('.folder-section');
                let totalCount = 0;
                
                folderSections.forEach(section => {
                    section.style.display = 'block';
                    totalCount++;
                });
                
                this.showNotification(`📁 Showing all ${totalCount} folders`, 'info', 3000);
            };
        }
    </script>

</body>
</html>
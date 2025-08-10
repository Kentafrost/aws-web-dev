<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Viewer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
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
            color: #333;
            margin-bottom: 30px;
        }
        .back-link {
            margin-bottom: 20px;
        }
        .back-link a {
            display: inline-block;
            padding: 8px 16px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.2s;
        }
        .back-link a:hover {
            background: #545b62;
        }
        .folder-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
            border-left: 4px solid #007bff;
        }
        .folder-path {
            font-family: monospace;
            background: #e9ecef;
            padding: 5px 10px;
            border-radius: 3px;
            margin: 5px 0;
        }
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
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }
        .media-item:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .media-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
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
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .type-image {
            background: #e8f5e8;
            color: #2d5a2d;
        }
        .type-video {
            background: #e8f0ff;
            color: #2d4a7d;
        }
        .type-audio {
            background: #fff2e8;
            color: #7d4a2d;
        }
        .file-size-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 11px;
            margin-top: 5px;
        }
        .large-file-warning {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            border-radius: 8px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .media-item:hover .loading-overlay {
            opacity: 1;
        }
        .audio-selected {
            background: #d4edda !important;
            border-left: 4px solid #28a745 !important;
        }
        .audio-indicator {
            background: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 10px;
        }
        .video-buttons {
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
        }
        .video-buttons a {
            min-width: 120px;
            text-align: center;
        }
        .media-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #e9ecef;
            border-radius: 5px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        .error-message {
            color: #dc3545;
            background: #f8d7da;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
        }
        .no-media {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        /* Lightbox for images */
        .lightbox {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
            cursor: zoom-in;
        }
        .lightbox.zoomed {
            cursor: grab;
        }
        .lightbox.zoomed:active {
            cursor: grabbing;
        }
        .lightbox-content {
            position: relative;
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 90%;
            top: 50%;
            transform: translateY(-50%);
            transition: transform 0.3s ease;
            cursor: zoom-in;
        }
        .lightbox-content.zoomed {
            cursor: grab;
            max-width: none;
            max-height: none;
        }
        .lightbox-content.zoomed:active {
            cursor: grabbing;
        }
        .lightbox-close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            z-index: 1001;
            background: rgba(0,0,0,0.5);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .lightbox-close:hover {
            color: #bbb;
            background: rgba(0,0,0,0.7);
        }
        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #f1f1f1;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
            z-index: 1001;
            background: rgba(0,0,0,0.5);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            user-select: none;
        }
        .lightbox-nav:hover {
            background: rgba(0,0,0,0.7);
            color: #fff;
        }
        .lightbox-prev {
            left: 20px;
        }
        .lightbox-next {
            right: 20px;
        }
        .lightbox-counter {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: #f1f1f1;
            background: rgba(0,0,0,0.5);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            z-index: 1001;
        }
        .lightbox-controls {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 1001;
        }
        .lightbox-control-btn {
            background: rgba(0,0,0,0.5);
            color: #f1f1f1;
            border: none;
            padding: 8px 12px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.2s;
        }
        .lightbox-control-btn:hover {
            background: rgba(0,0,0,0.7);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="back-link">
            <a href="javascript:history.back()">← Back to Browser</a>
        </div>
        
        <h1>Media Viewer</h1>

        <!-- audio Audio File Selector -->
        <div class="folder-selector" style="margin-bottom: 25px;">
            <h2>🎵 audio Audio Files</h2>
            <p>Select an audio file to play:</p>
            
            <form action="" method="GET" style="margin-bottom: 15px;">
                <!-- Preserve the current folder parameter -->
                <?php if (!empty($_GET['folder'])): ?>
                    <input type="hidden" name="folder" value="<?php echo htmlspecialchars($_GET['folder']); ?>">
                <?php endif; ?>
                
                <select name="audio_file" onchange="this.form.submit()" style="margin-right: 10px;">
                    <option value="">-- Select an audio audio file --</option>
                    <?php
                    $audioFolderPath = 'G:\\My Drive\\Entertainment\\audio\\favorite\\';
                    $selectedAudioFile = $_GET['audio_file'] ?? '';
                    
                    if (is_dir($audioFolderPath)) {
                        // Get audio files recursively (WAV and MP3)
                        $audioFiles = array_merge(
                            glob($audioFolderPath . '**/*.wav', GLOB_BRACE),
                            glob($audioFolderPath . '**/*.mp3', GLOB_BRACE)
                        );
                        
                        if (!empty($audioFiles)) {
                            // Sort files by name
                            sort($audioFiles);
                            
                            foreach ($audioFiles as $audioFile) {
                                $fileName = basename($audioFile);
                                $relativeFolder = str_replace($audioFolderPath, '', dirname($audioFile));
                                $displayName = !empty($relativeFolder) ? $relativeFolder . '/' . $fileName : $fileName;
                                $selected = ($selectedAudioFile === $audioFile) ? 'selected' : '';
                                
                                echo "<option value='" . htmlspecialchars($audioFile) . "' $selected>";
                                echo htmlspecialchars($displayName);
                                echo "</option>";
                            }
                        } else {
                            echo "<option disabled>No audio files found</option>";
                        }
                    } else {
                        echo "<option disabled>audio folder not accessible</option>";
                    }
                    ?>
                </select>
                
                <?php if (!empty($selectedAudioFile)): ?>
                    <button type="submit" name="audio_file" value="" style="padding: 8px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">Clear Selection</button>
                <?php endif; ?>
            </form>
            
            <!-- <?php if (!empty($selectedAudioFile) && file_exists($selectedAudioFile)): ?>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #28a745;">
                    <h4 style="margin-top: 0; color: #155724;">🎧 Now Playing</h4>
                    <div style="margin-bottom: 10px;">
                        <strong>File:</strong> <?php echo htmlspecialchars(basename($selectedAudioFile)); ?><br>
                        <strong>Size:</strong> <?php echo formatBytes(filesize($selectedAudioFile)); ?><br>
                        <strong>Location:</strong> <small><?php echo htmlspecialchars($selectedAudioFile); ?></small>
                    </div>
                    
                    <!-- Debug streaming URL -->
                    <div style="margin-bottom: 10px; font-size: 12px; color: #6c757d;">
                        <strong>Streaming URL:</strong> <small>../common/stream-media.php?file=<?php echo urlencode($selectedAudioFile); ?></small>
                    </div>
                    
                    <audio controls style="width: 100%; margin-top: 10px;" preload="metadata">
                        <?php 
                        $fileExtension = strtolower(pathinfo($selectedAudioFile, PATHINFO_EXTENSION));
                        $mimeType = ($fileExtension === 'mp3') ? 'audio/mpeg' : 'audio/wav';
                        ?>
                        <source src="stream-media.php?file=<?php echo urlencode($selectedAudioFile); ?>" type="<?php echo $mimeType; ?>">
                        Your browser does not support the audio tag.
                    </audio>
                    
                    <div style="margin-top: 15px; padding: 10px; background: #e7f3ff; border-radius: 5px; font-size: 12px;">
                        <strong>� Troubleshooting:</strong><br>
                        • If audio doesn't play, check browser console (F12) for errors<br>
                        • Large WAV files (>100MB) may take time to buffer<br>
                        • Try right-clicking the streaming URL above to test direct access<br>
                        • File is being streamed in chunks to handle large sizes
                    </div>
                </div> -->
            <?php elseif (!empty($selectedAudioFile)): ?>
                <div style="background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24; border-left: 4px solid #dc3545;">
                    ⚠️ Selected audio file not found: <?php echo htmlspecialchars($selectedAudioFile); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Media Gallery such as video, images -->
        <?php
        // Get the folder path from URL parameter
        $folderPath = $_GET['folder'] ?? '';
        
        if (empty($folderPath)) {
            echo "<div class='error-message'>";
            echo "<h3>No Folder Selected</h3>";
            echo "<p>Please go back and select a folder to view.</p>";
            echo "</div>";
            exit;
        }
        
        // Decode the folder path
        $folderPath = urldecode($folderPath);
        
        // Security check - ensure path is within allowed directory
        $allowedBasePath = ['D:\\Entertainments-videos', 'G:\\My Drive\\Entertainment\\'];
        $isAllowed = false;
        foreach ($allowedBasePath as $basePath) {
            if (strpos($folderPath, $basePath) === 0) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            echo "<div class='error-message'>";
            echo "<h3>Access Denied</h3>";
            echo "<p>Access to this directory is not allowed.</p>";
            echo "</div>";
            exit;
        }
        
        // Check if folder exists and is readable
        if (!is_dir($folderPath) || !is_readable($folderPath)) {
            echo "<div class='error-message'>";
            echo "<h3>Folder Not Accessible</h3>";
            echo "<p>The selected folder cannot be accessed: " . htmlspecialchars($folderPath) . "</p>";
            echo "</div>";
            exit;
        }
        
        // Display folder information
        echo "<div class='folder-info " . (!empty($selectedAudioFile) ? 'audio-selected' : '') . "'>";
        echo "<h3>📁 " . htmlspecialchars(basename($folderPath));
        if (!empty($selectedAudioFile)) {
            echo "<span class='audio-indicator'>🎵 audio Mode</span>";
        }
        echo "</h3>";
        echo "<div class='folder-path'>" . htmlspecialchars($folderPath) . "</div>";
        if (!empty($selectedAudioFile)) {
            echo "<div style='margin-top: 10px; font-size: 14px; color: #155724;'>";
            echo "🎧 audio selected: " . htmlspecialchars(basename($selectedAudioFile));
            echo "</div>";
        }
        echo "</div>";
        
        // Function to format file sizes
        function formatBytes($size, $precision = 2) {
            if ($size == 0) return '0 B';
            $base = log($size, 1024);
            $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
            return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
        }
        
        // Function to create web-accessible path
        function createWebPath($filePath) {
            // Use our streaming script for media files
            return '../common/stream-media.php?file=' . urlencode($filePath);
        }
        
        // Supported file extensions
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff', 'ico'];
        
        // Scan folder for media files
        $mediaFiles = [];
        $totalSize = 0;
        $imageCount = 0;
        $videoCount = 0;
        $audioCount = 0;
        
        try {
            $items = scandir($folderPath);
            
            if ($items === false) {
                throw new Exception("Cannot read folder contents");
            }
            
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                
                $fullPath = $folderPath . DIRECTORY_SEPARATOR . $item;
                
                if (is_file($fullPath)) {
                    $extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                    $fileSize = filesize($fullPath);
                    $totalSize += $fileSize;
                    
                    if (in_array($extension, $imageExtensions)) {
                        $mediaFiles[] = [
                            'name' => $item,
                            'path' => $fullPath,
                            'webPath' => createWebPath($fullPath),
                            'type' => 'image',
                            'extension' => $extension,
                            'size' => $fileSize,
                            'modified' => filemtime($fullPath)
                        ];
                        $imageCount++;
                    }
                }
            }
            
        } catch (Exception $e) {
            echo "<div class='error-message'>";
            echo "<h3>Error Reading Folder</h3>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "</div>";
            exit;
        }
        
        // Display statistics
        if (!empty($mediaFiles)) {
            echo "<div class='media-stats'>";
            echo "<div class='stat-item'>";
            echo "<div class='stat-number'>" . $imageCount . "</div>";
            echo "<div class='stat-label'>Images</div>";
            echo "</div>";
            echo "<div class='stat-item'>";
            echo "<div class='stat-number'>" . formatBytes($totalSize) . "</div>";
            echo "<div class='stat-label'>Total Size</div>";
            echo "</div>";
            echo "</div>";
            
            // Sort files by name
            usort($mediaFiles, function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
            
            // Display media gallery
            echo "<div class='media-gallery'>";

            // Create JavaScript array of images for navigation
            $imageData = [];
            foreach ($mediaFiles as $index => $media) {
                if ($media['type'] === 'image') {
                    $imageData[] = [
                        'src' => $media['webPath'],
                        'name' => $media['name'],
                        'index' => $index
                    ];
                }
            }
            
            echo "<script>";
            echo "const imageGallery = " . json_encode($imageData) . ";";
            echo "let currentImageIndex = 0;";
            echo "</script>";

            foreach ($mediaFiles as $index => $media) {
                echo "<div class='media-item'>";
                
                // Media type badge
                $typeClass = 'type-' . $media['type'];
                echo "<span class='media-type {$typeClass}'>" . strtoupper($media['type']) . "</span>";

                if ($media['type'] === 'image') {
                    // Find the image index in the imageData array
                    $imageIndex = 0;
                    foreach ($imageData as $imgIdx => $imgData) {
                        if ($imgData['index'] === $index) {
                            $imageIndex = $imgIdx;
                            break;
                        }
                    }
                    echo "<img src='{$media['webPath']}' alt='" . htmlspecialchars($media['name']) . "' onclick='openLightbox({$imageIndex})' loading='lazy'>";
                }
                
                echo "<div class='media-filename'>";
                echo htmlspecialchars($media['name']) . "<br>";
                echo "<small>" . formatBytes($media['size']) . " • " . strtoupper($media['extension']) . "</small>";

                echo "</div>";
                echo "</div>";
            }
            
            echo "</div>";
            
        } else {
            echo "<div class='no-media'>";
            echo "<h3>No Media Files Found</h3>";
            echo "<p>This folder doesn't contain any supported image or video files.</p>";
            echo "</div>";
        }
        ?>
        
        <!-- Enhanced Lightbox for images -->
        <div id="lightbox" class="lightbox">
            <div class="lightbox-controls">
                <button class="lightbox-control-btn" onclick="resetZoom()">Reset Zoom</button>
                <button class="lightbox-control-btn" onclick="toggleFullscreen()">Fullscreen</button>
            </div>
            <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
            <span class="lightbox-nav lightbox-prev" onclick="prevImage()">&#8249;</span>
            <span class="lightbox-nav lightbox-next" onclick="nextImage()">&#8250;</span>
            <img class="lightbox-content" id="lightbox-img">
            <div class="lightbox-counter" id="lightbox-counter">1 / 1</div>
        </div>
    </div>

    <script>
        // Enhanced lightbox functionality
        let zoomLevel = 1;
        let isDragging = false;
        let startX, startY, currentX = 0, currentY = 0;
        
        function openLightbox(imageIndex) {
            if (imageGallery.length === 0) return;
            
            currentImageIndex = imageIndex;
            const lightbox = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-img');
            const counter = document.getElementById('lightbox-counter');
            
            img.src = imageGallery[currentImageIndex].src;
            img.alt = imageGallery[currentImageIndex].name;
            counter.textContent = `${currentImageIndex + 1} / ${imageGallery.length}`;
            
            lightbox.style.display = 'block';
            resetZoom();
            
            // Preload next and previous images
            preloadImages();
        }
        
        function closeLightbox() {
            document.getElementById('lightbox').style.display = 'none';
            resetZoom();
        }
        
        function nextImage() {
            if (imageGallery.length === 0) return;
            currentImageIndex = (currentImageIndex + 1) % imageGallery.length;
            updateImage();
        }
        
        function prevImage() {
            if (imageGallery.length === 0) return;
            currentImageIndex = (currentImageIndex - 1 + imageGallery.length) % imageGallery.length;
            updateImage();
        }
        
        function updateImage() {
            const img = document.getElementById('lightbox-img');
            const counter = document.getElementById('lightbox-counter');
            
            img.src = imageGallery[currentImageIndex].src;
            img.alt = imageGallery[currentImageIndex].name;
            counter.textContent = `${currentImageIndex + 1} / ${imageGallery.length}`;
            
            resetZoom();
            preloadImages();
        }
        
        function preloadImages() {
            // Preload next image
            if (imageGallery.length > 1) {
                const nextIndex = (currentImageIndex + 1) % imageGallery.length;
                const prevIndex = (currentImageIndex - 1 + imageGallery.length) % imageGallery.length;
                
                const nextImg = new Image();
                nextImg.src = imageGallery[nextIndex].src;
                
                const prevImg = new Image();
                prevImg.src = imageGallery[prevIndex].src;
            }
        }
        
        function resetZoom() {
            zoomLevel = 1;
            currentX = 0;
            currentY = 0;
            const img = document.getElementById('lightbox-img');
            const lightbox = document.getElementById('lightbox');
            
            img.style.transform = 'translateY(-50%) scale(1)';
            img.classList.remove('zoomed');
            lightbox.classList.remove('zoomed');
        }
        
        function zoomImage(factor) {
            zoomLevel *= factor;
            zoomLevel = Math.max(0.5, Math.min(5, zoomLevel)); // Limit zoom between 0.5x and 5x
            
            const img = document.getElementById('lightbox-img');
            const lightbox = document.getElementById('lightbox');
            
            if (zoomLevel > 1) {
                img.classList.add('zoomed');
                lightbox.classList.add('zoomed');
            } else {
                img.classList.remove('zoomed');
                lightbox.classList.remove('zoomed');
                currentX = 0;
                currentY = 0;
            }
            
            updateImageTransform();
        }
        
        function updateImageTransform() {
            const img = document.getElementById('lightbox-img');
            img.style.transform = `translate(${currentX}px, calc(-50% + ${currentY}px)) scale(${zoomLevel})`;
        }
        
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.getElementById('lightbox').requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        }
        
        // Event listeners
        document.getElementById('lightbox-img').addEventListener('click', function(e) {
            e.stopPropagation();
            if (zoomLevel === 1) {
                zoomImage(2);
            } else {
                resetZoom();
            }
        });
        
        // Mouse wheel zoom
        document.getElementById('lightbox').addEventListener('wheel', function(e) {
            e.preventDefault();
            const zoomFactor = e.deltaY > 0 ? 0.9 : 1.1;
            zoomImage(zoomFactor);
        });
        
        // Dragging functionality
        document.getElementById('lightbox-img').addEventListener('mousedown', function(e) {
            if (zoomLevel > 1) {
                isDragging = true;
                startX = e.clientX - currentX;
                startY = e.clientY - currentY;
                e.preventDefault();
            }
        });
        
        document.addEventListener('mousemove', function(e) {
            if (isDragging && zoomLevel > 1) {
                currentX = e.clientX - startX;
                currentY = e.clientY - startY;
                updateImageTransform();
            }
        });
        
        document.addEventListener('mouseup', function() {
            isDragging = false;
        });
        
        // Touch support for mobile
        let initialDistance = 0;
        let initialZoom = 1;
        
        document.getElementById('lightbox-img').addEventListener('touchstart', function(e) {
            if (e.touches.length === 2) {
                initialDistance = Math.hypot(
                    e.touches[0].clientX - e.touches[1].clientX,
                    e.touches[0].clientY - e.touches[1].clientY
                );
                initialZoom = zoomLevel;
            }
        });
        
        document.getElementById('lightbox-img').addEventListener('touchmove', function(e) {
            e.preventDefault();
            if (e.touches.length === 2) {
                const distance = Math.hypot(
                    e.touches[0].clientX - e.touches[1].clientX,
                    e.touches[0].clientY - e.touches[1].clientY
                );
                const newZoom = initialZoom * (distance / initialDistance);
                zoomLevel = Math.max(0.5, Math.min(5, newZoom));
                updateImageTransform();
            }
        });
        
        // Close lightbox when clicking outside the image
        document.getElementById('lightbox').onclick = function(event) {
            if (event.target === this) {
                closeLightbox();
            }
        }
        
        // Keyboard navigation
        document.addEventListener('keydown', function(event) {
            if (document.getElementById('lightbox').style.display === 'block') {
                switch(event.key) {
                    case 'Escape':
                        closeLightbox();
                        break;
                    case 'ArrowLeft':
                        prevImage();
                        break;
                    case 'ArrowRight':
                        nextImage();
                        break;
                    case '+':
                    case '=':
                        zoomImage(1.2);
                        break;
                    case '-':
                        zoomImage(0.8);
                        break;
                    case '0':
                        resetZoom();
                        break;
                }
            }
        });
    </script>
</body>
</html>

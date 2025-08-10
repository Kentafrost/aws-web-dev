<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One File Player</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .back-link {
            float: left;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .file-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: left;
        }
        .player {
            margin: 30px 0;
        }
        video, audio {
            max-width: 100%;
            border-radius: 5px;
        }
        img {
            max-width: 100%;
            max-height: 70vh;
            border-radius: 5px;
            cursor: pointer;
        }
        .controls {
            margin-top: 20px;
        }
        .controls a {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .controls a:hover {
            background: #0056b3;
        }
        .error {
            color: #dc3545;
            background: #f8d7da;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="javascript:history.back()" class="back-link">← Go Back</a>
        <div style="clear: both;"></div>
        
        <h1>🎬 One File Player</h1>

        <?php
        $filePath = $_GET['file'] ?? '';
        
        if (empty($filePath)) {
            echo "<div class='error'>❌ No file specified</div>";
            exit;
        }

        $filePath = urldecode($filePath);

        // Security check
        $allowedPaths = ['D:\\Entertainments-videos', 'G:\\My Drive\\Entertainment\\Audio\\favorite'];
        $isAllowed = false;
        foreach ($allowedPaths as $path) {
            if (strpos($filePath, $path) === 0) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed || !file_exists($filePath)) {
            echo "<div class='error'>❌ File not found or access denied</div>";
            exit;
        }

        $fileName = basename($filePath);
        $fileSize = filesize($filePath);
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        // Format file size
        function formatSize($size) {
            if ($size == 0) return '0 B';
            $units = ['B', 'KB', 'MB', 'GB'];
            $base = log($size, 1024);
            return round(pow(1024, $base - floor($base)), 1) . ' ' . $units[floor($base)];
        }

        // Show file info
        echo "<div class='file-info'>";
        echo "<strong>File:</strong> " . htmlspecialchars($fileName) . "<br>";
        echo "<strong>Size:</strong> " . formatSize($fileSize) . "<br>";
        echo "<strong>Type:</strong> " . strtoupper($extension);
        echo "</div>";

        // Show player based on file type
        echo "<div class='player'>";

        if (in_array($extension, ['mp4', 'avi', 'mkv', 'mov', 'wmv', 'webm'])) {
            echo "<h3>🎬 Video Player</h3>";
            echo "<video controls>";
            echo "<source src='stream-media.php?file=" . urlencode($filePath) . "'>";
            echo "Your browser doesn't support video playback.";
            echo "</video>";
            
        } elseif (in_array($extension, ['mp3', 'wav', 'ogg', 'aac', 'm4a'])) {
            echo "<h3>🎵 Audio Player</h3>";
            echo "<audio controls>";
            echo "<source src='stream-media.php?file=" . urlencode($filePath) . "'>";
            echo "Your browser doesn't support audio playback.";
            echo "</audio>";
            
        } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) {
            echo "<h3>🖼️ Image Viewer</h3>";
            echo "<img src='stream-media.php?file=" . urlencode($filePath) . "' alt='" . htmlspecialchars($fileName) . "' onclick='this.style.maxHeight=this.style.maxHeight===\"none\"?\"70vh\":\"none\"'>";
            
        } else {
            echo "<h3>📄 File Download</h3>";
            echo "<p>This file type cannot be previewed.</p>";
        }

        echo "</div>";
        ?>

        <div class="controls">
            <a href="stream-media.php?file=<?= urlencode($filePath) ?>" target="_blank">🔗 Open in New Tab</a>
            <a href="stream-media.php?file=<?= urlencode($filePath) ?>" download>💾 Download</a>
        </div>
    </div>

    <script>
        // One click-to-zoom for images
        document.addEventListener('DOMContentLoaded', function() {
            const img = document.querySelector('img');
            if (img) {
                img.title = 'Click to toggle full size';
            }
        });
    </script>
</body>
</html>

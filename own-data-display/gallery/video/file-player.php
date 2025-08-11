<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Player</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
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
        .back-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .file-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .player-container {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        video, audio, img {
            max-width: 100%;
            max-height: 70vh;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .download-link {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .download-link:hover {
            background: #218838;
            color: white;
            text-decoration: none;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #dc3545;
            margin: 20px 0;
        }
        .controls {
            margin: 20px 0;
            text-align: center;
        }
        .controls button {
            margin: 5px;
            padding: 8px 16px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .controls button:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="back-link">
            <a href="javascript:history.back()">← Back to Folder Browser</a>
        </div>

        <h1>🎬 File Player</h1>

        <?php
        $filePath = $_GET['file'] ?? '';
        
        if (empty($filePath)) {
            echo "<div class='error-message'>";
            echo "<h3>❌ No File Specified</h3>";
            echo "<p>Please select a file to play.</p>";
            echo "</div>";
            exit;
        }

        // Decode the file path
        $filePath = urldecode($filePath);

        // Security check - ensure path is within allowed directories
        $allowedBasePaths = [
            'D:\\Entertainments-videos',
            'G:\\My Drive\\Entertainment\\'
        ];

        $pathAllowed = false;
        foreach ($allowedBasePaths as $basePath) {
            if (strpos($filePath, $basePath) === 0) {
                $pathAllowed = true;
                break;
            }
        }

        if (!$pathAllowed) {
            echo "<div class='error-message'>";
            echo "<h3>🚫 Access Denied</h3>";
            echo "<p>This file is not in an allowed directory.</p>";
            echo "</div>";
            exit;
        }

        // Check if file exists
        if (!file_exists($filePath) || !is_file($filePath)) {
            echo "<div class='error-message'>";
            echo "<h3>❌ File Not Found</h3>";
            echo "<p>The specified file does not exist or is not accessible.</p>";
            echo "<p><strong>Path:</strong> " . htmlspecialchars($filePath) . "</p>";
            echo "</div>";
            exit;
        }

        // Get file information
        $fileName = basename($filePath);
        $fileSize = filesize($filePath);
        $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeType = '';

        // Determine MIME type and file category
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv', '3gp', 'm4v', 'mpg', 'mpeg'];
        $audioExtensions = ['mp3', 'wav', 'ogg', 'aac', 'm4a', 'flac'];
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];

        if (in_array($fileExtension, $videoExtensions)) {
            $fileType = 'video';
            $mimeType = 'video/' . ($fileExtension === 'mkv' ? 'x-matroska' : $fileExtension);
        } elseif (in_array($fileExtension, $audioExtensions)) {
            $fileType = 'audio';
            $mimeType = $fileExtension === 'mp3' ? 'audio/mpeg' : 'audio/' . $fileExtension;
        } elseif (in_array($fileExtension, $imageExtensions)) {
            $fileType = 'image';
            $mimeType = 'image/' . ($fileExtension === 'jpg' ? 'jpeg' : $fileExtension);
        } else {
            $fileType = 'unknown';
        }

        // Format file size
        function formatBytes($size, $precision = 2) {
            if ($size == 0) return '0 B';
            $base = log($size, 1024);
            $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
            return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
        }

        // Display file information
        echo "<div class='file-info'>";
        echo "<h3>📄 File Information</h3>";
        echo "<p><strong>Name:</strong> " . htmlspecialchars($fileName) . "</p>";
        echo "<p><strong>Size:</strong> " . formatBytes($fileSize) . "</p>";
        echo "<p><strong>Type:</strong> " . strtoupper($fileExtension) . " (" . ucfirst($fileType) . ")</p>";
        echo "<p><strong>Location:</strong> <small>" . htmlspecialchars($filePath) . "</small></p>";
        echo "</div>";

        // Player controls
        echo "<div class='controls'>";
        echo "<button onclick=\"window.open('../common/stream-media.php?file=" . urlencode($filePath) . "', '_blank')\">🔗 Open in New Tab</button>";
        echo "<button onclick=\"copyToClipboard('" . htmlspecialchars($filePath) . "')\">📋 Copy Path</button>";
        if ($fileType === 'video') {
            echo "<button onclick=\"window.open('video-audio-player.php?video=" . urlencode($filePath) . "', '_blank')\" style='background: #28a745;'>🎵 Watch with Audio</button>";
        }
        echo "<button onclick=\"window.history.back()\">🔙 Go Back</button>";
        echo "</div>";

        // Display appropriate player based on file type
        echo "<div class='player-container'>";

        if ($fileType === 'video') {
            echo "<h3>🎬 Video Player</h3>";
            echo "<video controls preload='metadata' style='width: 100%; max-height: 60vh;'>";
            echo "<source src='../common/stream-media.php?file=" . urlencode($filePath) . "' type='" . $mimeType . "'>";
            echo "Your browser does not support the video tag.";
            echo "</video>";
            
        } elseif ($fileType === 'audio') {
            echo "<h3>🎵 Audio Player</h3>";
            echo "<audio controls preload='metadata' style='width: 100%;'>";
            echo "<source src='../common/stream-media.php?file=" . urlencode($filePath) . "' type='" . $mimeType . "'>";
            echo "Your browser does not support the audio tag.";
            echo "</audio>";
            
        } elseif ($fileType === 'image') {
            echo "<h3>🖼️ Image Viewer</h3>";
            echo "<img src='../common/stream-media.php?file=" . urlencode($filePath) . "' alt='" . htmlspecialchars($fileName) . "' style='max-width: 100%; max-height: 70vh; cursor: zoom-in;' onclick='toggleImageSize(this)'>";
            
        } else {
            echo "<h3>📄 File Preview</h3>";
            echo "<p>This file type cannot be previewed directly in the browser.</p>";
            echo "<p>You can download it using the link below.</p>";
        }

        echo "</div>";

        // Download link
        echo "<div style='text-align: center;'>";
        echo "<a href='../common/stream-media.php?file=" . urlencode($filePath) . "' download class='download-link'>💾 Download File</a>";
        echo "</div>";
        ?>

    </div>

    <script>
        // Copy to clipboard function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('File path copied to clipboard!');
            }).catch(() => {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('File path copied to clipboard!');
            });
        }

        // Toggle image size
        function toggleImageSize(img) {
            if (img.style.maxHeight === '100vh') {
                img.style.maxHeight = '70vh';
                img.style.cursor = 'zoom-in';
            } else {
                img.style.maxHeight = '100vh';
                img.style.cursor = 'zoom-out';
            }
        }

        // Auto-focus on media elements
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.querySelector('video');
            const audio = document.querySelector('audio');
            
            if (video) {
                video.focus();
            } else if (audio) {
                audio.focus();
            }
        });
    </script>
</body>
</html>

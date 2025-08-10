<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One File Browser</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .folder-selector {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .folder-section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fafafa;
        }
        .folder-title {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
            cursor: pointer;
        }
        .folder-title:hover {
            text-decoration: underline;
        }
        .file-item {
            margin: 5px 0;
            padding: 5px;
        }
        .file-link {
            color: #28a745;
            text-decoration: none;
            font-weight: bold;
        }
        .file-link:hover {
            background: #e8f5e8;
            padding: 2px 5px;
            border-radius: 3px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="media-viewer.php" class="back-link">← Back to Media Viewer</a>
        
        <h1>📁 One File Browser</h1>
        
        <div class="folder-selector">
            <h3>Choose a folder to browse:</h3>
            <form method="GET">
                <select name="folder" onchange="this.form.submit()">
                    <option value="">Select a folder...</option>
                    <option value="D:\Entertainments-videos" <?php echo ($_GET['folder'] ?? '') === 'D:\Entertainments-videos' ? 'selected' : ''; ?>>D:\Entertainments-videos</option>
                    <option value="G:\My Drive\Entertainment\Audio\favorite" <?php echo ($_GET['folder'] ?? '') === 'G:\My Drive\Entertainment\ASMR\favorite' ? 'selected' : ''; ?>>G:\My Drive\Entertainment\ASMR\favorite</option>
                </select>
            </form>
        </div>

        <?php
        function formatFileSize($filePath) {
            if (!file_exists($filePath)) return 'Unknown';
            $size = filesize($filePath);
            if ($size == 0) return '0 B';
            $units = ['B', 'KB', 'MB', 'GB'];
            $base = log($size, 1024);
            return round(pow(1024, $base - floor($base)), 1) . ' ' . $units[floor($base)];
        }

        function showFolder($folderPath, $folderName) {
            if (!is_dir($folderPath)) return;
            
            echo "<div class='folder-section'>";
            echo "<div class='folder-title'>📁 " . htmlspecialchars($folderName) . "</div>";
            
            $items = scandir($folderPath);
            $folders = [];
            $files = [];
            
            // Separate folders and files
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                $fullPath = $folderPath . DIRECTORY_SEPARATOR . $item;
                
                if (is_dir($fullPath)) {
                    $folders[] = $item;
                } else {
                    $files[] = $item;
                }
            }
            
            // Show folders first
            sort($folders);
            foreach ($folders as $folder) {
                echo "<div class='file-item'>";
                echo "📁 " . htmlspecialchars($folder);
                echo "</div>";
            }
            
            // Show files
            sort($files);
            foreach ($files as $file) {
                $fullPath = $folderPath . DIRECTORY_SEPARATOR . $file;
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                
                // Check if it's a media file
                $mediaExtensions = ['mp4', 'avi', 'mkv', 'mov', 'wmv', 'mp3', 'wav', 'jpg', 'jpeg', 'png', 'gif'];
                
                echo "<div class='file-item'>";
                if (in_array($extension, $mediaExtensions)) {
                    // Get file type icon
                    $icon = '📄';
                    if (in_array($extension, ['mp4', 'avi', 'mkv', 'mov', 'wmv'])) $icon = '🎬';
                    elseif (in_array($extension, ['mp3', 'wav'])) $icon = '🎵';
                    elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) $icon = '🖼️';
                    
                    echo "<a href='one-file-player.php?file=" . urlencode($fullPath) . "' target='_blank' class='file-link'>";
                    echo $icon . " " . htmlspecialchars($file) . " (" . formatFileSize($fullPath) . ")";
                    echo "</a>";
                } else {
                    echo "📄 " . htmlspecialchars($file) . " (" . formatFileSize($fullPath) . ")";
                }
                echo "</div>";
            }
            
            echo "</div>";
        }

        // Main logic
        $selectedFolder = $_GET['folder'] ?? '';
        
        if (!empty($selectedFolder) && is_dir($selectedFolder)) {
            $folderName = basename($selectedFolder);
            showFolder($selectedFolder, $folderName);
            
            // Show subfolders
            $items = scandir($selectedFolder);
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                $subPath = $selectedFolder . DIRECTORY_SEPARATOR . $item;
                if (is_dir($subPath)) {
                    showFolder($subPath, $folderName . ' / ' . $item);
                }
            }
        }
        ?>
    </div>
</body>
</html>

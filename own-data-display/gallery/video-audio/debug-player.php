<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Video + audio Player</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
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
        .section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .debug {
            background: #f8f9fa;
            padding: 10px;
            margin: 10px 0;
            border-left: 4px solid #007bff;
            font-family: monospace;
            font-size: 12px;
        }
        video, audio {
            width: 100%;
            margin: 10px 0;
        }
        button {
            margin: 5px;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .video-btn { background: #007bff; color: white; }
        .audio-btn { background: #28a745; color: white; }
        .control-btn { background: #6c757d; color: white; }
        select { width: 100%; padding: 8px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Debug Video + audio Player</h1>
        
        <?php
        $videoFile = $_GET['video'] ?? '';
        $audioFile = $_GET['audio'] ?? '';
        
        echo "<div class='debug'>";
        echo "Video file: " . htmlspecialchars($videoFile) . "<br>";
        echo "audio file: " . htmlspecialchars($audioFile) . "<br>";
        echo "Video exists: " . (file_exists(urldecode($videoFile)) ? 'YES' : 'NO') . "<br>";
        echo "</div>";
        
        if (empty($videoFile)) {
            echo "<div style='color: red;'>❌ No video file specified</div>";
            exit;
        }

        $videoFile = urldecode($videoFile);
        
        // Get audio files
        $audioDir = 'G:\\My Drive\\Entertainment\\audio\\favorite\\';
        $audioFiles = [];
        if (is_dir($audioDir)) {
            $files = scandir($audioDir);
            foreach ($files as $file) {
                if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['mp3', 'wav', 'ogg', 'aac', 'm4a'])) {
                    $audioFiles[] = $file;
                }
            }
            sort($audioFiles);
        }
        
        echo "<div class='debug'>";
        echo "audio directory: " . htmlspecialchars($audioDir) . "<br>";
        echo "audio files found: " . count($audioFiles) . "<br>";
        if (count($audioFiles) > 0) {
            echo "First audio file: " . htmlspecialchars($audioFiles[0]) . "<br>";
        }
        echo "</div>";
        ?>

        <!-- Video Section -->
        <div class="section">
            <h3>🎬 Video Player</h3>
            <video id="videoPlayer" controls>
                <source src="stream-media.php?file=<?= urlencode($videoFile) ?>" type="video/mp4">
                Video not supported
            </video>
            <button class="video-btn" onclick="videoPlayer.play()">▶️ Play Video</button>
            <button class="video-btn" onclick="videoPlayer.pause()">⏸️ Pause Video</button>
        </div>

        <!-- audio Section -->
        <div class="section">
            <h3>🎵 audio Audio</h3>
            
            <select id="audioSelect">
                <option value="">-- Select audio --</option>
                <?php foreach ($audioFiles as $file): ?>
                    <option value="<?= htmlspecialchars($file) ?>">
                        <?= htmlspecialchars($file) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <audio id="audioPlayer" controls loop style="display: none;">
                Your browser does not support audio.
            </audio>
            
            <div id="audioDebug" class="debug" style="display: none;"></div>
            
            <button class="audio-btn" onclick="loadSelectedaudio()">🔄 Load audio</button>
            <button class="audio-btn" onclick="testaudio()">🧪 Test audio</button>
            <button class="audio-btn" onclick="audioPlayer.play()">▶️ Play audio</button>
            <button class="audio-btn" onclick="audioPlayer.pause()">⏸️ Pause audio</button>
        </div>

        <!-- Controls -->
        <div class="section">
            <h3>🎛️ Combined Controls</h3>
            <button class="control-btn" onclick="playBoth()">▶️ Play Both</button>
            <button class="control-btn" onclick="pauseBoth()">⏸️ Pause Both</button>
            <button class="control-btn" onclick="debugInfo()">🔧 Debug Info</button>
        </div>

        <div id="debugOutput" class="debug"></div>
    </div>

    <script>
        const videoPlayer = document.getElementById('videoPlayer');
        const audioPlayer = document.getElementById('audioPlayer');
        const audioSelect = document.getElementById('audioSelect');
        const debugOutput = document.getElementById('debugOutput');
        const audioDebug = document.getElementById('audioDebug');

        function loadSelectedaudio() {
            const selectedFile = audioSelect.value;
            if (!selectedFile) {
                alert('Please select an audio file first');
                return;
            }
            
            // Construct the full path
            const audioDir = '<?= addslashes($audioDir) ?>';
            const fullPath = audioDir + selectedFile;
            const streamUrl = 'stream-media.php?file=' + encodeURIComponent(fullPath);
            
            // Debug info
            audioDebug.innerHTML = 'Selected: ' + selectedFile + '<br>' +
                                 'Full path: ' + fullPath + '<br>' +
                                 'Stream URL: ' + streamUrl;
            audioDebug.style.display = 'block';
            
            // Set the audio source
            audioPlayer.src = streamUrl;
            audioPlayer.style.display = 'block';
            audioPlayer.volume = 0.3;
            
            console.log('Loading audio:', streamUrl);
        }

        function testaudio() {
            // Test with first available audio file
            if (audioSelect.options.length > 1) {
                audioSelect.selectedIndex = 1;
                loadSelectedaudio();
            }
        }

        function playBoth() {
            videoPlayer.play();
            if (audioPlayer.src) {
                audioPlayer.play();
            }
        }

        function pauseBoth() {
            videoPlayer.pause();
            audioPlayer.pause();
        }

        function debugInfo() {
            let info = 'Video source: ' + videoPlayer.currentSrc + '<br>';
            info += 'audio source: ' + audioPlayer.currentSrc + '<br>';
            info += 'Video ready state: ' + videoPlayer.readyState + '<br>';
            info += 'audio ready state: ' + audioPlayer.readyState + '<br>';
            info += 'Video duration: ' + videoPlayer.duration + '<br>';
            info += 'audio duration: ' + audioPlayer.duration + '<br>';
            
            debugOutput.innerHTML = info;
        }

        // Event listeners for debugging
        audioPlayer.addEventListener('loadstart', () => console.log('audio: Load started'));
        audioPlayer.addEventListener('canplay', () => console.log('audio: Can play'));
        audioPlayer.addEventListener('error', (e) => console.log('audio Error:', e));
        audioPlayer.addEventListener('loadeddata', () => console.log('audio: Data loaded'));

        videoPlayer.addEventListener('loadstart', () => console.log('Video: Load started'));
        videoPlayer.addEventListener('canplay', () => console.log('Video: Can play'));
        videoPlayer.addEventListener('error', (e) => console.log('Video Error:', e));
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video + Audio Player</title>
    <style>
        body {
           <!-- Sync Controls -->
        <div class="sync-controls">
            <h4>🎛️ Playback Controls</h4>
            <div class="controls">
                <button onclick="playBoth()">▶️ Play Both</button>
                <button onclick="pauseBoth()">⏸️ Pause Both</button>
                <button onclick="syncVolumes()">🔊 Balance Volumes</button>
                <button onclick="toggleaudioLoop()" class="audio-btn">🔄 Toggle AudioLoop</button>
                <button onclick="debugaudio()" style="background: #ffc107; color: #000;">🔧 Debug audio</button>
            </div>
            
            <!-- Recovery Controls -->
            <div class="controls" style="margin-top: 10px; border-top: 1px solid #ddd; padding-top: 10px;">
                <h5>🔄 Recovery Controls</h5>
                <button onclick="recoverVideo()" style="background: #17a2b8; color: white;">🎬 Recover Video</button>
                <button onclick="recoveraudio()" style="background: #6f42c1; color: white;">🎵 Recover audio</button>
                <button onclick="forceResync()" style="background: #fd7e14; color: white;">⚡ Force Resync</button>
                <button onclick="checkConnection()" style="background: #20c997; color: white;">📡 Check Connection</button>
            </div>
            
            <p><small>💡 Tip: If media stops playing, use recovery controls. Audio will loop automatically.</small></p>
            
            <!-- Connection Status -->
            <div id="connectionStatus" class="connection-status connection-good" style="display: none;">
                📡 Connection: Good
            </div>
            
            <div id="debugInfo" style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 10px; font-family: monospace; font-size: 12px; display: none;"></div>
        </div>nt-family: Arial, sans-serif;
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
        .player-section {
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #007bff;
        }
        .audio-section {
            border-left-color: #28a745;
        }
        .player-section h3 {
            margin-top: 0;
            color: #333;
        }
        video, audio {
            width: 100%;
            max-height: 60vh;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .controls {
            margin: 15px 0;
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
        .controls button.audio-btn {
            background: #28a745;
        }
        .controls button.audio-btn:hover {
            background: #218838;
        }
        .file-info {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            font-size: 14px;
        }
        .audio-selector {
            margin: 15px 0;
        }
        .audio-selector select {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .sync-controls {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
        .volume-control {
            margin: 10px 0;
        }
        .volume-control input[type="range"] {
            width: 100%;
        }
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            font-family: Arial, sans-serif;
        }
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .loading-text {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .loading-details {
            font-size: 14px;
            color: #666;
            text-align: center;
            max-width: 400px;
        }
        .progress-bar {
            width: 300px;
            height: 8px;
            background: #f0f0f0;
            border-radius: 4px;
            margin: 15px 0;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #007bff, #28a745);
            width: 0%;
            transition: width 0.3s ease;
        }
        .hidden {
            display: none !important;
        }
        
        /* Recovery notification animations */
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        /* Buffering indicator */
        .buffering-indicator {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            z-index: 1000;
        }
        
        /* Connection status indicator */
        .connection-status {
            position: fixed;
            bottom: 20px;
            left: 20px;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            z-index: 9999;
            transition: all 0.3s ease;
        }
        
        .connection-good {
            background: #28a745;
            color: white;
        }
        
        .connection-poor {
            background: #ffc107;
            color: #000;
        }
        
        .connection-bad {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div id="loadingScreen" class="loading-screen">
        <div class="loading-spinner"></div>
        <div class="loading-text" id="loadingText">Loading Audio...</div>
        <div class="progress-bar">
            <div class="progress-fill" id="progressFill"></div>
        </div>
        <div class="loading-details" id="loadingDetails">
            Please wait while we prepare your audio experience...<br>
            <span id="loadingStatus">Initializing...</span>
        </div>
    </div>

    <div class="container" id="mainContent" style="display: none;">
        <div class="back-link">
            <a href="javascript:history.back()">← Back to Browser</a>
        </div>

        <h1>🎬🎵 Video + AudioPlayer</h1>

        <?php
        $videoFile = $_GET['video'] ?? '';
        $audioFile = $_GET['audio'] ?? '';
        
        if (empty($videoFile)) {
            echo "<div class='alert alert-danger'>❌ No video file specified</div>";
            exit;
        }

        $videoFile = urldecode($videoFile);
        $audioFile = urldecode($audioFile); // Decode Audiofile path too
        
        // Debug info
        echo "<div style='background: #e7f3ff; padding: 10px; margin: 10px 0; border-radius: 5px; font-size: 12px;'>";
        echo "<strong>🔧 Debug Info:</strong><br>";
        echo "Video file: " . htmlspecialchars($videoFile) . "<br>";
        echo "Audiofile: " . htmlspecialchars($audioFile) . "<br>";
        echo "Video exists: " . (file_exists($videoFile) ? 'YES' : 'NO') . "<br>";
        echo "Audioexists: " . (!empty($audioFile) && file_exists($audioFile) ? 'YES' : 'NO') . "<br>";
        echo "</div>";
        
        // Security check for video file
        $allowedBasePaths = [
            'D:\\Entertainments-videos',
            'G:\\My Drive\\Entertainment\\'
        ];
        
        $videoAllowed = false;
        foreach ($allowedBasePaths as $basePath) {
            if (strpos($videoFile, $basePath) === 0) {
                $videoAllowed = true;
                break;
            }
        }
        
        if (!$videoAllowed || !file_exists($videoFile)) {
            echo "<div class='alert alert-danger'>❌ Video file not found or access denied</div>";
            exit;
        }

        // Security check for Audiofile if provided
        if (!empty($audioFile)) {
            $audioAllowed = false;
            foreach ($allowedBasePaths as $basePath) {
                if (strpos($audioFile, $basePath) === 0) {
                    $audioAllowed = true;
                    break;
                }
            }
            
            if (!$audioAllowed || !file_exists($audioFile)) {
                echo "<div style='background: #f8d7da; padding: 15px; margin: 10px 0; border-radius: 5px; color: #721c24;'>";
                echo "⚠️ Audiofile not found or access denied: " . htmlspecialchars($audioFile);
                echo "</div>";
                $audioFile = ''; // Reset to empty so we don't try to use it
            }
        }

        // Get Audiofiles list
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

        function formatFileSize($filePath) {
            if (!file_exists($filePath)) return 'Unknown';
            $size = filesize($filePath);
            if ($size == 0) return '0 B';
            $units = ['B', 'KB', 'MB', 'GB'];
            $base = log($size, 1024);
            return round(pow(1024, $base - floor($base)), 1) . ' ' . $units[floor($base)];
        }
        ?>

        <!-- Video Player Section -->
        <div class="player-section">
            <h3>🎬 Video Player</h3>
            <div class="file-info">
                <strong>File:</strong> <?= htmlspecialchars(basename($videoFile)) ?><br>
                <strong>Size:</strong> <?= formatFileSize($videoFile) ?>
            </div>
            <video id="videoPlayer" controls preload="metadata">
                <source src="../common/stream-media.php?file=<?= urlencode($videoFile) ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="volume-control">
                <label>Video Volume:</label>
                <input type="range" id="videoVolume" min="0" max="100" value="100" oninput="setVideoVolume(this.value)">
                <span id="videoVolumeDisplay">100%</span>
            </div>
        </div>

        <!-- Audio Section -->
        <div class="player-section audio-section">
            <h3>🎵 Background Audio</h3>

            <audio id="audioPlayer" controls loop style="<?= $audioFile ? 'display: block;' : 'display: none;' ?>">
                <?php if ($audioFile): ?>
                    <?php 
                    $audioExtension = strtolower(pathinfo($audioFile, PATHINFO_EXTENSION));
                    $audioMimeType = '';
                    switch($audioExtension) {
                        case 'mp3': $audioMimeType = 'audio/mpeg'; break;
                        case 'wav': $audioMimeType = 'audio/wav'; break;
                        case 'ogg': $audioMimeType = 'audio/ogg'; break;
                        case 'aac': $audioMimeType = 'audio/aac'; break;
                        case 'm4a': $audioMimeType = 'audio/mp4'; break;
                        default: $audioMimeType = 'audio/mpeg';
                    }
                    ?>
                    <source src="../common/stream-media.php?file=<?= urlencode($audioFile) ?>" type="<?= $audioMimeType ?>">
                    <!-- Fallback for different browsers -->
                    <source src="../common/stream-media.php?file=<?= urlencode($audioFile) ?>" type="audio/mpeg">
                    <source src="../common/stream-media.php?file=<?= urlencode($audioFile) ?>" type="audio/wav">
                <?php endif; ?>
                Your browser does not support the audio tag.
            </audio>

            <div id="audioInfo" class="file-info" style="<?= $audioFile ? 'display: block;' : 'display: none;' ?>">
                <strong>AudioFile:</strong> <span id="audioFileName"><?= $audioFile ? htmlspecialchars(basename($audioFile)) : '' ?></span><br>
                <strong>Path:</strong> <span id="audioFilePath"><?= $audioFile ? htmlspecialchars($audioFile) : '' ?></span>
            </div>

            <div class="volume-control">
                <label>AudioVolume:</label>
                <input type="range" id="audioVolume" min="0" max="100" value="30" oninput="setaudioVolume(this.value)">
                <span id="audioVolumeDisplay">30%</span>
            </div>
            
            <!-- AudioTest Controls -->
            <?php if ($audioFile): ?>
            <div style="margin-top: 15px; padding: 10px; background: #e7f3ff; border-radius: 5px;">
                <strong>🧪 AudioTest Controls:</strong><br>
                <button onclick="testaudioPlay()" style="margin: 2px; padding: 5px 10px; background: #28a745; color: white; border: none; border-radius: 3px;">▶️ Test Play</button>
                <button onclick="testaudioPause()" style="margin: 2px; padding: 5px 10px; background: #dc3545; color: white; border: none; border-radius: 3px;">⏸️ Test Pause</button>
                <button onclick="testaudioReload()" style="margin: 2px; padding: 5px 10px; background: #ffc107; color: #000; border: none; border-radius: 3px;">🔄 Reload</button>
                <button onclick="openaudioDirect()" style="margin: 2px; padding: 5px 10px; background: #17a2b8; color: white; border: none; border-radius: 3px;">🔗 Open Direct</button>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sync Controls -->
        <div class="sync-controls">
            <h4>🎛️ Playback Controls</h4>
            <div class="controls">
                <button onclick="playBoth()">▶️ Play Both</button>
                <button onclick="pauseBoth()">⏸️ Pause Both</button>
                <button onclick="syncVolumes()">🔊 Balance Volumes</button>
                <button onclick="toggleaudioLoop()" class="audio-btn">🔄 Toggle AudioLoop</button>
            </div>
            <p><small>💡 Tip: Audio will loop automatically. Adjust volumes to your preference!</small></p>
        </div>

    </div>

    <script>
        const videoPlayer = document.getElementById('videoPlayer');
        const audioPlayer = document.getElementById('audioPlayer');
        const audioSelect = document.getElementById('audioSelect');

        // Load Audio when selected
        function loadaudio() {
            const selectedFile = audioSelect.value;
            if (selectedFile) {
                // Construct the full Audiofile path
                const audioPath = '<?= addslashes($audioDir) ?>' + selectedFile;
                const streamUrl = '../common/stream-media.php?file=' + encodeURIComponent(audioPath);
                
                // Set the audio source
                audioPlayer.src = streamUrl;
                audioPlayer.style.display = 'block';
                audioPlayer.load(); // Force reload
                
                // Show file info
                document.getElementById('audioInfo').style.display = 'block';
                document.getElementById('audioFileName').textContent = selectedFile;
                document.getElementById('audioFilePath').textContent = audioPath;
                
                // Set initial volume
                audioPlayer.volume = 0.3;
                
                console.log('Loading audio:', audioPath);
                console.log('Stream URL:', streamUrl);
            } else {
                audioPlayer.style.display = 'none';
                document.getElementById('audioInfo').style.display = 'none';
            }
        }

        // Volume controls
        function setVideoVolume(value) {
            videoPlayer.volume = value / 100;
            document.getElementById('videoVolumeDisplay').textContent = value + '%';
        }

        function setaudioVolume(value) {
            audioPlayer.volume = value / 100;
            document.getElementById('audioVolumeDisplay').textContent = value + '%';
        }

        // Playback controls
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

        function syncVolumes() {
            // Set balanced volumes (video higher, Audiolower)
            videoPlayer.volume = 0.8;
            audioPlayer.volume = 0.3;
            document.getElementById('videoVolume').value = 80;
            document.getElementById('audioVolume').value = 30;
            document.getElementById('videoVolumeDisplay').textContent = '80%';
            document.getElementById('audioVolumeDisplay').textContent = '30%';
        }

        function toggleaudioLoop() {
            audioPlayer.loop = !audioPlayer.loop;
            const button = event.target;
            button.textContent = audioPlayer.loop ? '🔄 Loop ON' : '🔄 Loop OFF';
            button.style.background = audioPlayer.loop ? '#28a745' : '#6c757d';
        }

        function debugaudio() {
            const debugInfo = document.getElementById('debugInfo');
            let info = '<strong>🔧 AudioDebug Information:</strong><br>';
            info += 'URL audio: <?= addslashes($audioFile) ?><br>';
            info += 'AudioDirectory: <?= addslashes($audioDir) ?><br>';
            info += 'Selected in dropdown: ' + audioSelect.value + '<br>';
            info += 'Audio element src: ' + audioPlayer.src + '<br>';
            info += 'Audio ready state: ' + audioPlayer.readyState + '<br>';
            info += 'Audio current time: ' + audioPlayer.currentTime + '<br>';
            info += 'Audio duration: ' + audioPlayer.duration + '<br>';
            info += 'Audio paused: ' + audioPlayer.paused + '<br>';
            info += 'Audio volume: ' + audioPlayer.volume + '<br>';
            info += 'Audio loop: ' + audioPlayer.loop + '<br>';
            info += 'Audio error: ' + (audioPlayer.error ? audioPlayer.error.message : 'None') + '<br>';
            
            debugInfo.innerHTML = info;
            debugInfo.style.display = 'block';
        }

        // AudioTest Functions
        function testaudioPlay() {
            console.log('Testing Audioplay...');
            audioPlayer.play().then(() => {
                console.log('Audioplay successful');
                alert('✅ Audioplay successful!');
            }).catch(error => {
                console.error('Audioplay failed:', error);
                alert('❌ Audioplay failed: ' + error.message);
            });
        }

        function testaudioPause() {
            audioPlayer.pause();
            console.log('Audiopaused');
            alert('⏸️ Audiopaused');
        }

        function testaudioReload() {
            console.log('Reloading audio...');
            audioPlayer.load();
            audioPlayer.volume = 0.3;
            alert('🔄 Audioreloaded');
        }

        function openaudioDirect() {
            const audioUrl = '../common/stream-media.php?file=<?= urlencode($audioFile) ?>';
            window.open(audioUrl, '_blank');
        }

        // Loading System
        let loadingSteps = 0;
        let totalSteps = 3; // Video, audio, UI
        let audioReady = false;
        let videoReady = false;

        function updateLoadingProgress() {
            const progressFill = document.getElementById('progressFill');
            const loadingStatus = document.getElementById('loadingStatus');
            
            let progress = (loadingSteps / totalSteps) * 100;
            progressFill.style.width = progress + '%';
            
            if (loadingSteps >= totalSteps && audioReady && videoReady) {
                loadingStatus.textContent = 'Ready! Initializing player...';
                setTimeout(hideLoadingScreen, 1000);
            }
        }

        function hideLoadingScreen() {
            document.getElementById('loadingScreen').style.display = 'none';
            document.getElementById('mainContent').style.display = 'block';
            console.log('🎉 All media ready - player initialized');
        }

        function showLoadingStep(message) {
            document.getElementById('loadingStatus').textContent = message;
            loadingSteps++;
            updateLoadingProgress();
        }

        // Auto-load Audioif specified in URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlAudio= '<?= addslashes($audioFile) ?>';
            
            showLoadingStep('Initializing player...');
            
            // Set initial volumes
            videoPlayer.volume = 1.0;
            audioPlayer.volume = 0.3;
            audioPlayer.loop = true;
            
            // Start monitoring systems
            startConnectionMonitoring();
            
            // Check if we have Audioto load
            if (urlAudio&& audioPlayer.src) {
                document.getElementById('loadingText').textContent = 'Loading Audio...';
                document.getElementById('loadingStatus').textContent = 'Preparing Audiofile: ' + '<?= addslashes(basename($audioFile)) ?>';
                
                audioPlayer.style.display = 'block';
                document.getElementById('audioInfo').style.display = 'block';
                
                // Wait for Audioto be ready
                const checkaudioReady = setInterval(() => {
                    if (audioPlayer.readyState >= 3) { // HAVE_FUTURE_DATA or better
                        audioReady = true;
                        showLoadingStep('Audio ready');
                        clearInterval(checkaudioReady);
                    }
                }, 100);
                
                // Timeout after 30 seconds
                setTimeout(() => {
                    if (!audioReady) {
                        clearInterval(checkaudioReady);
                        audioReady = true; // Force continue
                        showLoadingStep('Audiotimeout - continuing...');
                        console.warn('Audioloading timeout');
                    }
                }, 30000);
                
            } else {
                audioReady = true;
                showLoadingStep('No Audioselected');
            }
            
            // Check video ready state
            const checkVideoReady = setInterval(() => {
                if (videoPlayer.readyState >= 2) { // HAVE_CURRENT_DATA or better
                    videoReady = true;
                    showLoadingStep('Video ready');
                    clearInterval(checkVideoReady);
                }
            }, 100);
            
            // Video timeout after 15 seconds
            setTimeout(() => {
                if (!videoReady) {
                    clearInterval(checkVideoReady);
                    videoReady = true; // Force continue
                    showLoadingStep('Video timeout - continuing...');
                    console.warn('Video loading timeout');
                }
            }, 15000);
            
            // Show helpful tips after loading
            setTimeout(() => {
                if (document.getElementById('mainContent').style.display !== 'none') {
                    showNotification('💡 Use recovery controls if media stops playing!', 'info');
                }
            }, 3000);
        });

        // Auto-pause Audiowhen video ends
        videoPlayer.addEventListener('ended', function() {
            audioPlayer.pause();
        });

        // Audio Event Listeners for debugging and loading
        audioPlayer.addEventListener('loadstart', function() {
            console.log('🎵 audio: Load started');
            document.getElementById('loadingStatus').textContent = 'audio: Starting to load...';
        });

        audioPlayer.addEventListener('canplay', function() {
            console.log('🎵 audio: Can play - audio is ready');
            if (!audioReady) {
                audioReady = true;
                showLoadingStep('Audioready to play');
            }
        });

        audioPlayer.addEventListener('canplaythrough', function() {
            console.log('🎵 audio: Can play through - fully loaded');
            document.getElementById('loadingStatus').textContent = 'audio: Fully loaded';
        });

        audioPlayer.addEventListener('error', function(e) {
            console.error('🎵 AudioError:', e);
            console.error('Error details:', audioPlayer.error);
            document.getElementById('loadingStatus').textContent = 'AudioError: ' + (audioPlayer.error ? audioPlayer.error.message : 'Unknown');
            // Continue anyway after error
            if (!audioReady) {
                audioReady = true;
                showLoadingStep('Audioerror - continuing...');
            }
        });

        audioPlayer.addEventListener('loadeddata', function() {
            console.log('🎵 audio: Data loaded');
            document.getElementById('loadingStatus').textContent = 'audio: Data loaded';
        });

        audioPlayer.addEventListener('loadedmetadata', function() {
            console.log('🎵 audio: Metadata loaded, duration:', audioPlayer.duration);
            document.getElementById('loadingStatus').textContent = 'audio: Metadata loaded (' + Math.round(audioPlayer.duration) + 's)';
        });

        audioPlayer.addEventListener('progress', function() {
            if (audioPlayer.buffered.length > 0) {
                const buffered = (audioPlayer.buffered.end(0) / audioPlayer.duration) * 100;
                if (buffered > 10 && !audioReady) { // Consider ready when 10% buffered
                    console.log('🎵 audio: Sufficient buffer (' + Math.round(buffered) + '%)');
                    audioReady = true;
                    showLoadingStep('Audiobuffered (' + Math.round(buffered) + '%)');
                }
            }
        });

        audioPlayer.addEventListener('play', function() {
            console.log('🎵 audio: Started playing');
        });

        audioPlayer.addEventListener('pause', function() {
            console.log('🎵 audio: Paused');
        });

        audioPlayer.addEventListener('waiting', function() {
            console.log('🎵 audio: Waiting for data...');
            document.getElementById('loadingStatus').textContent = 'audio: Waiting for more data...';
        });

        audioPlayer.addEventListener('stalled', function() {
            console.log('🎵 audio: Stalled - network issues?');
            document.getElementById('loadingStatus').textContent = 'audio: Network stalled - retrying...';
            // Auto-recovery for stalled audio
            setTimeout(() => {
                if (audioPlayer.readyState < 3) {
                    console.log('🔄 audio: Auto-recovery - reloading...');
                    audioPlayer.load();
                }
            }, 3000);
        });

        // Auto-Recovery System for Both Video and Audio
        let videoRecoveryAttempts = 0;
        let audioRecoveryAttempts = 0;
        const maxRecoveryAttempts = 3;

        // Video recovery events
        videoPlayer.addEventListener('stalled', function() {
            console.log('🎬 Video: Stalled - attempting recovery...');
            handleVideoRecovery();
        });

        videoPlayer.addEventListener('waiting', function() {
            console.log('🎬 Video: Waiting for data...');
            // If waiting too long, try recovery
            setTimeout(() => {
                if (videoPlayer.readyState < 3 && !videoPlayer.paused) {
                    handleVideoRecovery();
                }
            }, 5000);
        });

        videoPlayer.addEventListener('error', function() {
            console.error('🎬 Video Error:', videoPlayer.error);
            handleVideoRecovery();
        });

        // Audiorecovery events (enhanced)
        audioPlayer.addEventListener('error', function(e) {
            console.error('🎵 AudioError:', e);
            console.error('Error details:', audioPlayer.error);
            document.getElementById('loadingStatus').textContent = 'AudioError: ' + (audioPlayer.error ? audioPlayer.error.message : 'Unknown');
            handleaudioRecovery();
            // Continue anyway after error
            if (!audioReady) {
                audioReady = true;
                showLoadingStep('Audioerror - continuing...');
            }
        });

        // Recovery functions
        function handleVideoRecovery() {
            if (videoRecoveryAttempts < maxRecoveryAttempts) {
                videoRecoveryAttempts++;
                console.log(`🔄 Video Recovery Attempt ${videoRecoveryAttempts}/${maxRecoveryAttempts}`);
                
                const currentTime = videoPlayer.currentTime;
                const wasPlaying = !videoPlayer.paused;
                
                videoPlayer.load();
                
                videoPlayer.addEventListener('canplay', function onRecovery() {
                    videoPlayer.currentTime = currentTime;
                    if (wasPlaying) {
                        videoPlayer.play().catch(e => console.log('Video auto-play blocked:', e));
                    }
                    videoPlayer.removeEventListener('canplay', onRecovery);
                }, { once: true });
                
                // Show recovery notification
                showNotification('🔄 Video recovering...', 'info');
            } else {
                showNotification('⚠️ Video recovery failed. Please refresh page.', 'error');
            }
        }

        function handleaudioRecovery() {
            if (audioRecoveryAttempts < maxRecoveryAttempts) {
                audioRecoveryAttempts++;
                console.log(`🔄 AudioRecovery Attempt ${audioRecoveryAttempts}/${maxRecoveryAttempts}`);
                
                const currentTime = audioPlayer.currentTime;
                const wasPlaying = !audioPlayer.paused;
                const currentVolume = audioPlayer.volume;
                
                audioPlayer.load();
                
                audioPlayer.addEventListener('canplay', function onRecovery() {
                    audioPlayer.currentTime = currentTime;
                    audioPlayer.volume = currentVolume;
                    if (wasPlaying) {
                        audioPlayer.play().catch(e => console.log('Audioauto-play blocked:', e));
                    }
                    audioPlayer.removeEventListener('canplay', onRecovery);
                }, { once: true });
                
                // Show recovery notification
                showNotification('🔄 Audiorecovering...', 'info');
            } else {
                showNotification('⚠️ Audiorecovery failed. Please manually reload.', 'error');
            }
        }

        // Keep-alive system to prevent timeouts
        let keepAliveInterval;

        function startKeepAlive() {
            // Ping every 30 seconds to keep connection alive
            keepAliveInterval = setInterval(() => {
                if (!videoPlayer.paused || !audioPlayer.paused) {
                    // Check if we need to buffer more data
                    checkAndBufferMore();
                }
            }, 30000);
        }

        function checkAndBufferMore() {
            // For video
            if (videoPlayer.buffered.length > 0) {
                const videoBuffered = videoPlayer.buffered.end(videoPlayer.buffered.length - 1);
                const videoTimeLeft = videoPlayer.duration - videoPlayer.currentTime;
                
                if (videoTimeLeft > 60 && (videoBuffered - videoPlayer.currentTime) < 30) {
                    console.log('🎬 Video: Buffering more data preventively');
                    // Slight seek to trigger more buffering
                    const currentTime = videoPlayer.currentTime;
                    videoPlayer.currentTime = currentTime + 0.1;
                    videoPlayer.currentTime = currentTime;
                }
            }

            // For audio
            if (audioPlayer.buffered.length > 0 && audioPlayer.src) {
                const audioBuffered = audioPlayer.buffered.end(audioPlayer.buffered.length - 1);
                const audioTimeLeft = audioPlayer.duration - audioPlayer.currentTime;
                
                if (audioTimeLeft > 60 && (audioBuffered - audioPlayer.currentTime) < 30) {
                    console.log('🎵 audio: Buffering more data preventively');
                    // Slight seek to trigger more buffering
                    const currentTime = audioPlayer.currentTime;
                    audioPlayer.currentTime = currentTime + 0.1;
                    audioPlayer.currentTime = currentTime;
                }
            }
        }

        // Notification system
        function showNotification(message, type = 'info') {
            // Remove existing notifications
            const existingNotif = document.querySelector('.recovery-notification');
            if (existingNotif) {
                existingNotif.remove();
            }

            const notification = document.createElement('div');
            notification.className = 'recovery-notification';
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'error' ? '#dc3545' : type === 'success' ? '#28a745' : '#007bff'};
                color: white;
                padding: 15px 20px;
                border-radius: 5px;
                z-index: 10000;
                font-weight: bold;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                animation: slideIn 0.3s ease;
            `;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (notification && notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        // Enhanced play function with recovery
        function playBoth() {
            console.log('🎬🎵 Playing both media...');
            
            // Reset recovery attempts
            videoRecoveryAttempts = 0;
            audioRecoveryAttempts = 0;
            
            // Start keep-alive system
            startKeepAlive();
            
            // Play video
            videoPlayer.play().catch(error => {
                console.error('Video play failed:', error);
                showNotification('⚠️ Video play failed: ' + error.message, 'error');
            });
            
            // Play Audioif available
            if (audioPlayer.src && audioPlayer.readyState >= 2) {
                audioPlayer.play().catch(error => {
                    console.error('Audioplay failed:', error);
                    showNotification('⚠️ Audioplay failed: ' + error.message, 'error');
                });
            }
        }

        // Enhanced pause function
        function pauseBoth() {
            console.log('🎬🎵 Pausing both media...');
            
            // Stop keep-alive when paused
            if (keepAliveInterval) {
                clearInterval(keepAliveInterval);
            }
            
            videoPlayer.pause();
            audioPlayer.pause();
        }

        // Manual Recovery Functions
        function recoverVideo() {
            console.log('🔄 Manual video recovery initiated');
            showNotification('🔄 Recovering video...', 'info');
            handleVideoRecovery();
        }

        function recoveraudio() {
            console.log('🔄 Manual Audiorecovery initiated');
            showNotification('🔄 Recovering audio...', 'info');
            handleaudioRecovery();
        }

        function forceResync() {
            console.log('⚡ Force resync initiated');
            showNotification('⚡ Force resyncing both media...', 'info');
            
            // Stop both
            videoPlayer.pause();
            audioPlayer.pause();
            
            // Reset recovery attempts
            videoRecoveryAttempts = 0;
            audioRecoveryAttempts = 0;
            
            // Reload both
            const videoTime = videoPlayer.currentTime;
            const audioTime = audioPlayer.currentTime;
            
            videoPlayer.load();
            if (audioPlayer.src) {
                audioPlayer.load();
            }
            
            // Wait for both to be ready, then sync and play
            let videoLoaded = false;
            let audioLoaded = false;
            
            function checkBothReady() {
                if (videoLoaded && (audioLoaded || !audioPlayer.src)) {
                    // Sync times
                    videoPlayer.currentTime = videoTime;
                    if (audioPlayer.src) {
                        audioPlayer.currentTime = audioTime;
                    }
                    
                    // Play both
                    setTimeout(() => {
                        playBoth();
                        showNotification('✅ Resync complete!', 'success');
                    }, 500);
                }
            }
            
            videoPlayer.addEventListener('canplay', function onVideoReady() {
                videoLoaded = true;
                videoPlayer.removeEventListener('canplay', onVideoReady);
                checkBothReady();
            }, { once: true });
            
            if (audioPlayer.src) {
                audioPlayer.addEventListener('canplay', function onaudioReady() {
                    audioLoaded = true;
                    audioPlayer.removeEventListener('canplay', onaudioReady);
                    checkBothReady();
                }, { once: true });
            } else {
                audioLoaded = true;
                checkBothReady();
            }
        }

        // Connection monitoring
        let connectionQuality = 'good';
        let lastBufferCheck = Date.now();

        function checkConnection() {
            console.log('📡 Checking connection quality...');
            const statusElement = document.getElementById('connectionStatus');
            statusElement.style.display = 'block';
            
            // Test connection by checking buffering rates
            let videoBufferHealth = 'good';
            let audioBufferHealth = 'good';
            
            // Check video buffering
            if (videoPlayer.buffered.length > 0) {
                const videoBuffered = videoPlayer.buffered.end(videoPlayer.buffered.length - 1);
                const videoBufferAhead = videoBuffered - videoPlayer.currentTime;
                
                if (videoBufferAhead < 5) {
                    videoBufferHealth = 'poor';
                } else if (videoBufferAhead < 2) {
                    videoBufferHealth = 'bad';
                }
            }
            
            // Check Audiobuffering
            if (audioPlayer.buffered.length > 0 && audioPlayer.src) {
                const audioBuffered = audioPlayer.buffered.end(audioPlayer.buffered.length - 1);
                const audioBufferAhead = audioBuffered - audioPlayer.currentTime;
                
                if (audioBufferAhead < 10) {
                    audioBufferHealth = 'poor';
                } else if (audioBufferAhead < 5) {
                    audioBufferHealth = 'bad';
                }
            }
            
            // Determine overall connection quality
            if (videoBufferHealth === 'bad' || audioBufferHealth === 'bad') {
                connectionQuality = 'bad';
                statusElement.className = 'connection-status connection-bad';
                statusElement.textContent = '📡 Connection: Poor - May stutter';
                showNotification('⚠️ Poor connection detected. Consider lowering quality.', 'error');
            } else if (videoBufferHealth === 'poor' || audioBufferHealth === 'poor') {
                connectionQuality = 'poor';
                statusElement.className = 'connection-status connection-poor';
                statusElement.textContent = '📡 Connection: Fair - Monitoring';
                showNotification('📡 Connection is fair. Monitoring...', 'info');
            } else {
                connectionQuality = 'good';
                statusElement.className = 'connection-status connection-good';
                statusElement.textContent = '📡 Connection: Good';
                showNotification('✅ Connection is good!', 'success');
            }
            
            // Auto-hide after 10 seconds if connection is good
            if (connectionQuality === 'good') {
                setTimeout(() => {
                    statusElement.style.display = 'none';
                }, 10000);
            }
        }

        // Auto connection monitoring
        function startConnectionMonitoring() {
            setInterval(() => {
                if (!videoPlayer.paused || !audioPlayer.paused) {
                    const now = Date.now();
                    
                    // Check every 15 seconds during playback
                    if (now - lastBufferCheck > 15000) {
                        lastBufferCheck = now;
                        
                        // Silent check - only show if problems detected
                        let needsAttention = false;
                        
                        // Check if we're running low on buffer
                        if (videoPlayer.buffered.length > 0) {
                            const videoBuffered = videoPlayer.buffered.end(videoPlayer.buffered.length - 1);
                            const videoBufferAhead = videoBuffered - videoPlayer.currentTime;
                            if (videoBufferAhead < 3 && !videoPlayer.paused) {
                                needsAttention = true;
                            }
                        }
                        
                        if (audioPlayer.buffered.length > 0 && audioPlayer.src) {
                            const audioBuffered = audioPlayer.buffered.end(audioPlayer.buffered.length - 1);
                            const audioBufferAhead = audioBuffered - audioPlayer.currentTime;
                            if (audioBufferAhead < 5 && !audioPlayer.paused) {
                                needsAttention = true;
                            }
                        }
                        
                        if (needsAttention) {
                            console.log('⚠️ Low buffer detected - showing connection status');
                            checkConnection();
                        }
                    }
                }
            }, 5000);
        }
    </script>
</body>
</html>

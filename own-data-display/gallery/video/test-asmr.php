<?php
// Simple test to check audio files and paths

echo "<h2>🔧 audio Path Test</h2>";

$audioDir = 'G:\\My Drive\\Entertainment\\audio\\favorite\\';

echo "<h3>Directory Check:</h3>";
echo "audio Directory: " . htmlspecialchars($audioDir) . "<br>";
echo "Directory exists: " . (is_dir($audioDir) ? 'YES' : 'NO') . "<br>";

if (is_dir($audioDir)) {
    echo "<h3>Files in directory:</h3>";
    $files = scandir($audioDir);
    $audioFiles = [];
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $fullPath = $audioDir . $file;
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        
        echo "File: " . htmlspecialchars($file) . " | ";
        echo "Extension: " . $extension . " | ";
        echo "Exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . " | ";
        echo "Size: " . (file_exists($fullPath) ? filesize($fullPath) : 0) . " bytes<br>";
        
        if (in_array($extension, ['mp3', 'wav', 'ogg', 'aac', 'm4a'])) {
            $audioFiles[] = $file;
        }
    }
    
    echo "<h3>Audio Files Found: " . count($audioFiles) . "</h3>";
    
    if (count($audioFiles) > 0) {
        $testFile = $audioFiles[0];
        $testPath = $audioDir . $testFile;
        
        echo "<h3>Test with first audio file:</h3>";
        echo "Test file: " . htmlspecialchars($testFile) . "<br>";
        echo "Full path: " . htmlspecialchars($testPath) . "<br>";
        echo "URL encoded: " . urlencode($testPath) . "<br>";
        echo "Stream URL: ../common/stream-media.php?file=" . urlencode($testPath) . "<br>";
        
        echo "<h3>Test Audio Player:</h3>";
        echo "<audio controls>";
        echo "<source src='../common/stream-media.php?file=" . urlencode($testPath) . "' type='audio/mpeg'>";
        echo "Your browser does not support audio.";
        echo "</audio>";
    }
} else {
    echo "<p style='color: red;'>❌ audio directory not found!</p>";
    echo "<p>Please check if the path exists: " . htmlspecialchars($audioDir) . "</p>";
}
?>

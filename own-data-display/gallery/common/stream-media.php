<?php
/**
 * Media Streaming Script
 * Handles streaming of large video/audio files with proper headers
 */

// Security check
session_start();

$file = $_GET['file'] ?? '';
if (empty($file)) {
    http_response_code(404);
    die('File not specified');
}

// Decode and validate file path
$filePath = urldecode($file);

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
    http_response_code(403);
    die('Access denied');
}

// Check if file exists
if (!file_exists($filePath) || !is_file($filePath)) {
    http_response_code(404);
    die('File not found');
}

// Get file info
$fileSize = filesize($filePath);
$fileName = basename($filePath);
$fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

// Set appropriate MIME type
$mimeTypes = [
    // Video
    'mp4' => 'video/mp4',
    'avi' => 'video/x-msvideo',
    'mov' => 'video/quicktime',
    'wmv' => 'video/x-ms-wmv',
    'flv' => 'video/x-flv',
    'webm' => 'video/webm',
    'mkv' => 'video/x-matroska',
    '3gp' => 'video/3gpp',
    'm4v' => 'video/x-m4v',
    'mpg' => 'video/mpeg',
    'mpeg' => 'video/mpeg',
    
    // Audio
    'mp3' => 'audio/mpeg',
    'wav' => 'audio/wav',
    'ogg' => 'audio/ogg',
    'aac' => 'audio/aac',
    'm4a' => 'audio/m4a',
    'flac' => 'audio/flac',
    
    // Images
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'bmp' => 'image/bmp',
    'webp' => 'image/webp'
];

$mimeType = $mimeTypes[$fileExtension] ?? 'application/octet-stream';

// Handle range requests for large files
$range = $_SERVER['HTTP_RANGE'] ?? '';
$start = 0;
$end = $fileSize - 1;

if (!empty($range)) {
    if (preg_match('/bytes=(\d+)-(\d*)/', $range, $matches)) {
        $start = intval($matches[1]);
        if (!empty($matches[2])) {
            $end = intval($matches[2]);
        }
    }
}

// Set headers
header('Content-Type: ' . $mimeType);
header('Accept-Ranges: bytes');
header('Content-Length: ' . ($end - $start + 1));

if (!empty($range)) {
    http_response_code(206); // Partial Content
    header("Content-Range: bytes $start-$end/$fileSize");
} else {
    http_response_code(200);
}

// Disable output buffering
if (ob_get_level()) {
    ob_end_clean();
}

// Stream the file
$handle = fopen($filePath, 'rb');
if ($handle === false) {
    http_response_code(500);
    die('Could not open file');
}

// Seek to start position
fseek($handle, $start);

// Stream in chunks
$chunkSize = 8192; // 8KB chunks
$bytesToRead = $end - $start + 1;

while ($bytesToRead > 0 && !feof($handle)) {
    $currentChunkSize = min($chunkSize, $bytesToRead);
    $data = fread($handle, $currentChunkSize);
    
    if ($data === false) {
        break;
    }
    
    echo $data;
    flush();
    
    $bytesToRead -= strlen($data);
}

fclose($handle);
exit;
?>

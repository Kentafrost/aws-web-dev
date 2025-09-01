# Own Data Display - Enhanced PHP Media & File Management System

A comprehensive PHP-based web application featuring user authentication, media browsing, file streaming, and data management with AWS DynamoDB integration and local fallback storage.

## 🚀 Features

### Core Functionality
- **🔐 User Authentication**: Secure login/registration with password hashing
- **📁 File Browser**: Browse local drives (D:\, G:\) with depth control
- **🎬 Media Player**: Stream videos, audio, and images with advanced controls
- **🖼️ Image Gallery**: Enhanced image viewer with zoom, pan, and navigation
- **📊 Form Handling**: Questionnaire submission and data processing
- **☁️ AWS Integration**: DynamoDB for user data with local JSON fallback

### Advanced Media Features
- **🎵 Audio Synchronization**: Play background audio with videos
- **🔍 Search & Filter**: Real-time search across folders and files
- **📱 Responsive Design**: Mobile-friendly interface
- **🎯 Media Streaming**: Efficient large file streaming with range requests
- **⚡ Auto-Recovery**: Automatic media recovery on connection issues

## 📂 Project Structure

```
own-data-display/
├── README.md
├── index.php                    # Login page
├── login.php                   # Authentication backend
├── top.php                     # Main dashboard
├── users.json                  # Local user storage fallback
├── composer.json               # Dependency management
├── vendor/                     # Composer dependencies
│
├── gallery/
│   ├── common/
│   │   ├── folder-viewer.php   # Universal media viewer
│   │   ├── stream-media.php    # File streaming handler
│   │   └── folder-browser.js   # Enhanced browser functionality
│   │
│   ├── image/
│   │   ├── image-fold-choose.php # Image browser
│   │   └── zoom.js             # Image zoom functionality
│   │
│   └── video/
│       ├── video-audio-fold-choose.php # Video/audio browser
│       ├── file-player.php     # Media player
│       ├── video-audio-player.php # Enhanced video+audio player
│       └── one-file-browser.php # Single file browser
│
└── send_questionaire.php       # Form processing
```

## 🛠️ Installation

### Prerequisites
- **PHP 7.4+** with extensions: `curl`, `json`, `openssl`
- **Composer** for dependency management
- **Web server** (Apache/Nginx) or PHP built-in server
- **AWS Account** (optional, for DynamoDB)

### 1. Clone & Setup
```bash
git clone <repository-url>
cd own-data-display
composer install
```

### 2. Configure AWS (Optional)
```bash
# Option 1: Environment Variables
export AWS_ACCESS_KEY_ID=your_key
export AWS_SECRET_ACCESS_KEY=your_secret
export AWS_DEFAULT_REGION=ap-southeast-2

# Option 2: AWS Credentials File (~/.aws/credentials)
[default]
aws_access_key_id = your_key
aws_secret_access_key = your_secret
region = ap-southeast-2
```

### 3. Create DynamoDB Table (Optional)
```bash
aws dynamodb create-table \
    --table-name login-data-table \
    --attribute-definitions AttributeName=username,AttributeType=S \
    --key-schema AttributeName=username,KeyType=HASH \
    --billing-mode PAY_PER_REQUEST \
    --region ap-southeast-2
```

### 4. Start Application
```bash
# Using PHP built-in server
php -S localhost:8000

# Or configure with Apache/Nginx
```

## 🎯 Usage Guide

### Authentication System
1. **Registration**: Create new account with username/password
2. **Login**: Authenticate and access main dashboard
3. **Security**: Passwords are hashed using PHP's `password_hash()`

### Media Browser Features

#### 📁 Folder Navigation
- **Depth Control**: Scan 1-5 levels deep in directories
- **Search**: Real-time filtering of folders and files  
- **Smart Filters**: Show only folders with media content
- **Keyboard Shortcuts**: Use Ctrl+F for search, Escape to clear

#### 🎬 Video Player
```php
// Basic video playback
http://localhost:8000/gallery/video/file-player.php?file=D:\path\to\video.mp4

// Enhanced video + audio player
http://localhost:8000/gallery/video/video-audio-player.php?video=D:\path\to\video.mp4&audio=G:\path\to\audio.wav
```

#### 🖼️ Image Viewer
- **Lightbox**: Full-screen image viewing
- **Navigation**: Arrow keys or click to navigate
- **Zoom**: Click image or use +/- keys
- **Pan**: Drag when zoomed in
- **Fullscreen**: Press F for fullscreen mode

#### 🎵 Audio Integration
- **Background Audio**: Play audio tracks with videos
- **Auto-Loop**: Automatic audio looping
- **Volume Control**: Independent video/audio volume
- **Recovery System**: Auto-recovery on playback issues

### File Streaming
- **Range Requests**: Efficient streaming of large files
- **MIME Detection**: Automatic content-type detection
- **Security**: Path validation and access control
- **Performance**: 8KB chunk streaming for optimal performance

## 🔧 Configuration

### Media Directories
Update allowed paths in `stream-media.php`:
```php
$allowedBasePaths = [
    'D:\\Your\\Media\\Path',
    'G:\\Another\\Path\\',
    // Add more paths as needed
];
```

### Database Settings
Modify region in `login.php`:
```php
$dynamodb = new Aws\DynamoDb\DynamoDbClient([
    'region' => 'your-preferred-region',
    'version' => 'latest',
]);
```

### Browser Settings
Adjust depth and search options in PHP files:
```php
$selectedDepth = $_GET['depth'] ?? 3;  // Default scanning depth
$maxDisplayItems = 1000;               // Max items to display
```

## 📊 API Endpoints

### Authentication API
```javascript
// Login
POST /login.php
{
    "action": "LoginAuthenticate",
    "username": "user",
    "password": "pass"
}

// Register
POST /login.php  
{
    "action": "LoginUserCreate",
    "username": "user", 
    "password": "pass"
}
```

### File Streaming API
```
GET /gallery/common/stream-media.php?file=encoded_file_path
```

### Media Player URLs
```
# Basic file player
/gallery/video/file-player.php?file={file_path}

# Enhanced video + audio
/gallery/video/video-audio-player.php?video={video_path}&audio={audio_path}

# Folder viewer  
/gallery/common/folder-viewer.php?folder={folder_path}
```

## 🎨 User Interface

### Responsive Design
- **Mobile-friendly**: Works on all screen sizes
- **Touch Support**: Gesture navigation for mobile
- **Progressive Enhancement**: Works with or without JavaScript

### Keyboard Shortcuts
| Key | Action |
|-----|--------|
| `←` `→` | Navigate images |
| `+` `-` | Zoom in/out |
| `0` | Reset zoom |
| `F` | Toggle fullscreen |
| `Esc` | Close viewer/clear search |
| `Ctrl+F` | Focus search |

### Visual Enhancements
- **Loading Screens**: Progress indicators for media loading
- **Notifications**: User feedback system
- **Animations**: Smooth transitions and effects
- **Icons**: Comprehensive emoji-based file type indicators

## 🔒 Security Features

- **Path Validation**: Prevents directory traversal attacks
- **Input Sanitization**: XSS protection with `htmlspecialchars()`
- **Password Hashing**: Secure password storage
- **CORS Headers**: Proper cross-origin configuration
- **Session Management**: Secure session handling
- **Access Control**: Directory-based permissions

## 🚀 Performance Optimizations

- **Lazy Loading**: Images load as needed
- **Chunked Streaming**: Efficient large file delivery
- **Connection Monitoring**: Auto-recovery systems
- **Caching**: Browser-friendly headers
- **Compression**: Gzip support for text content

## 🐛 Troubleshooting

### Common Issues

**"Stream not working"**
```bash
# Check file permissions
chmod 755 /path/to/media/files

# Verify allowed paths in stream-media.php
```

**"DynamoDB connection failed"**
```bash
# Test AWS credentials
aws sts get-caller-identity

# Check region settings
# Verify DynamoDB table exists
```

**"Search not working"**
- Ensure JavaScript is enabled
- Check browser console for errors
- Verify folder-browser.js is loaded

### Debug Mode
Enable error reporting:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 🔄 Development

### Adding New Media Types
1. Update `$mimeTypes` in `stream-media.php`
2. Add extensions to browser PHP files
3. Update CSS for new file type icons

### Custom Themes
Modify CSS in individual PHP files or create shared stylesheets.

### Database Extensions
Extend DynamoDB schema or add new tables as needed.

## 📈 Monitoring & Analytics

### Performance Metrics
- File streaming performance
- User authentication rates
- Media playback success rates
- Search query performance

### Logging
- Error logs in PHP error log
- Access logs in web server logs
- Custom application logging available

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/new-feature`)
3. Commit changes (`git commit -am 'Add new feature'`)
4. Push branch (`git push origin feature/new-feature`)
5. Create Pull Request

## 🎉 Changelog

### Version 2.0 Features
- ✅ Enhanced media streaming with range requests
- ✅ Advanced image viewer with zoom/pan
- ✅ Audio synchronization with videos
- ✅ Smart folder filtering and search
- ✅ Auto-recovery systems for media playback
- ✅ Comprehensive keyboard navigation
- ✅ Mobile-responsive design
- ✅ Performance optimizations
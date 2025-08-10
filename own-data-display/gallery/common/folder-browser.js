
/**
 * Format file sizes in human readable format
 * @param {number} bytes - File size in bytes
 * @param {number} decimals - Number of decimal places
 * @returns {string} Formatted file size
 */
function formatFileSize(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB'];
    
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

/**
 * Debounce function to limit function calls
 * @param {Function} func - Function to debounce
 * @param {number} wait - Wait time in milliseconds
 * @returns {Function} Debounced function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Show loading spinner
 * @param {string} containerId - ID of container to show loading in
 */
function showLoading(containerId = 'folder-browser-container') {
    const container = document.getElementById(containerId);
    if (container) {
        const loadingHtml = `
            <div class="loading-spinner" style="text-align: center; padding: 40px;">
                <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #007bff; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <p style="margin-top: 15px; color: #666;">Loading folder contents...</p>
            </div>
            <style>
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            </style>
        `;
        container.innerHTML = loadingHtml;
    }
}

/**
 * Hide loading spinner
 * @param {string} containerId - ID of container to hide loading from
 */
function hideLoading(containerId = 'folder-browser-container') {
    const loadingSpinner = document.querySelector('.loading-spinner');
    if (loadingSpinner) {
        loadingSpinner.remove();
    }
}

// ============================================================================
// FOLDER NAVIGATION FUNCTIONS
// ============================================================================

/**
 * Handle depth selector change
 * @param {Event} event - Change event from depth selector
 */
function handleDepthChange(event) {
    const depth = event.target.value;
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('depth', depth);
    
    // Show loading before navigation
    showLoading();
    
    // Navigate to new URL with selected depth
    window.location.href = currentUrl.toString();
}

/**
 * Handle folder selector change
 * @param {Event} event - Change event from folder selector
 */
function handleFolderChange(event) {
    const selectedFolder = event.target.value;
    if (selectedFolder) {
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('folder', selectedFolder);
        
        // Show loading before navigation
        showLoading();
        
        // Navigate to new URL with selected folder
        window.location.href = currentUrl.toString();
    }
}

/**
 * Open media viewer for a specific folder
 * @param {string} folderPath - Path to the folder
 * @param {Event} event - Click event (optional)
 */
function openMediaViewer(folderPath, event = null) {
    if (event) {
        event.preventDefault();
    }
    
    // Construct media viewer URL
    const mediaViewerUrl = `folder-viewer.php?folder=${encodeURIComponent(folderPath)}`;
    
    // Open in same window
    window.location.href = mediaViewerUrl;
}

/**
 * Open media viewer in new tab
 * @param {string} folderPath - Path to the folder
 * @param {Event} event - Click event
 */
function openMediaViewerNewTab(folderPath, event) {
    event.preventDefault();
    event.stopPropagation();
    
    const mediaViewerUrl = `folder-viewer.php?folder=${encodeURIComponent(folderPath)}`;
    window.open(mediaViewerUrl, '_blank');
}

// ============================================================================
// FOLDER SECTION MANAGEMENT
// ============================================================================

/**
 * Toggle folder section visibility
 * @param {string} sectionId - ID of the folder section
 */
function toggleFolderSection(sectionId) {
    const section = document.getElementById(sectionId);
    const toggleBtn = document.querySelector(`[data-section="${sectionId}"]`);
    
    if (section && toggleBtn) {
        const isHidden = section.style.display === 'none';
        
        section.style.display = isHidden ? 'block' : 'none';
        toggleBtn.textContent = isHidden ? '▼ Hide' : '▶ Show';
        
        // Update local storage to remember state
        localStorage.setItem(`folder-section-${sectionId}`, isHidden ? 'visible' : 'hidden');
    }
}

/**
 * Collapse all folder sections
 */
function collapseAllSections() {
    const sections = document.querySelectorAll('.folder-section');
    const toggleBtns = document.querySelectorAll('[data-section]');
    
    sections.forEach(section => {
        section.style.display = 'none';
    });
    
    toggleBtns.forEach(btn => {
        btn.textContent = '▶ Show';
    });
    
    console.log('All folder sections collapsed');
}

/**
 * Expand all folder sections
 */
function expandAllSections() {
    const sections = document.querySelectorAll('.folder-section');
    const toggleBtns = document.querySelectorAll('[data-section]');
    
    sections.forEach(section => {
        section.style.display = 'block';
    });
    
    toggleBtns.forEach(btn => {
        btn.textContent = '▼ Hide';
    });
    
    console.log('All folder sections expanded');
}

/**
 * Restore folder section states from local storage
 */
function restoreFolderStates() {
    const sections = document.querySelectorAll('.folder-section');
    
    sections.forEach(section => {
        const sectionId = section.id;
        const savedState = localStorage.getItem(`folder-section-${sectionId}`);
        const toggleBtn = document.querySelector(`[data-section="${sectionId}"]`);
        
        if (savedState === 'hidden' && toggleBtn) {
            section.style.display = 'none';
            toggleBtn.textContent = '▶ Show';
        }
    });
}

// ============================================================================
// SEARCH AND FILTER FUNCTIONS
// ============================================================================

/**
 * Search within folder contents
 * @param {string} searchTerm - Term to search for
 */
function searchFolders(searchTerm) {
    const folderItems = document.querySelectorAll('.folder-item, .file-item');
    const searchLower = searchTerm.toLowerCase();
    
    folderItems.forEach(item => {
        const itemName = item.textContent.toLowerCase();
        const shouldShow = itemName.includes(searchLower) || searchTerm === '';
        
        item.style.display = shouldShow ? 'block' : 'none';
        
        // Highlight matching text
        if (searchTerm && shouldShow) {
            highlightText(item, searchTerm);
        } else {
            removeHighlight(item);
        }
    });
    
    // Update section counters
    updateSectionCounters();
}

/**
 * Highlight matching text in element
 * @param {Element} element - Element to highlight text in
 * @param {string} searchTerm - Term to highlight
 */
function highlightText(element, searchTerm) {
    const regex = new RegExp(`(${searchTerm})`, 'gi');
    element.innerHTML = element.innerHTML.replace(regex, '<mark style="background: yellow;">$1</mark>');
}

/**
 * Remove highlighting from element
 * @param {Element} element - Element to remove highlighting from
 */
function removeHighlight(element) {
    element.innerHTML = element.innerHTML.replace(/<mark[^>]*>(.*?)<\/mark>/gi, '$1');
}

/**
 * Filter folders by type
 * @param {string} filterType - Type to filter by ('all', 'folders', 'files', 'videos', 'images')
 */
function filterByType(filterType) {
    const allItems = document.querySelectorAll('.folder-item, .file-item');
    
    allItems.forEach(item => {
        let shouldShow = true;
        
        switch (filterType) {
            case 'folders':
                shouldShow = item.classList.contains('folder-item');
                break;
            case 'files':
                shouldShow = item.classList.contains('file-item');
                break;
            case 'videos':
                shouldShow = item.classList.contains('file-item') && 
                           item.textContent.match(/\.(mp4|avi|mkv|mov|wmv|flv|webm|3gp)$/i);
                break;
            case 'images':
                shouldShow = item.classList.contains('file-item') && 
                           item.textContent.match(/\.(jpg|jpeg|png|gif|bmp|webp)$/i);
                break;
            case 'all':
            default:
                shouldShow = true;
                break;
        }
        
        item.style.display = shouldShow ? 'block' : 'none';
    });
    
    updateSectionCounters();
}

/**
 * Update section counters after filtering
 */
function updateSectionCounters() {
    const sections = document.querySelectorAll('.folder-section');
    
    sections.forEach(section => {
        const visibleItems = section.querySelectorAll('.folder-item:not([style*="display: none"]), .file-item:not([style*="display: none"])');
        const counter = section.querySelector('.item-counter');
        
        if (counter) {
            counter.textContent = `(${visibleItems.length} visible items)`;
        }
    });
}

// ============================================================================
// CONTEXT MENU FUNCTIONS
// ============================================================================

/**
 * Show context menu for folder/file items
 * @param {Event} event - Right-click event
 * @param {string} itemPath - Path to the item
 * @param {string} itemType - Type of item ('folder' or 'file')
 */
function showContextMenu(event, itemPath, itemType) {
    event.preventDefault();
    
    // Remove existing context menu
    const existingMenu = document.querySelector('.context-menu');
    if (existingMenu) {
        existingMenu.remove();
    }
    
    // Create context menu
    const menu = document.createElement('div');
    menu.className = 'context-menu';
    menu.style.cssText = `
        position: fixed;
        top: ${event.clientY}px;
        left: ${event.clientX}px;
        background: white;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        z-index: 1000;
        min-width: 150px;
        font-family: Arial, sans-serif;
        font-size: 14px;
    `;
    
    // Create menu items
    const menuItems = [];
    
    if (itemType === 'folder') {
        menuItems.push({
            text: '📂 Open in Media Viewer',
            action: () => openMediaViewer(itemPath)
        });
        menuItems.push({
            text: '🆕 Open in New Tab',
            action: () => openMediaViewerNewTab(itemPath, event)
        });
    }
    
    menuItems.push({
        text: '📋 Copy Path',
        action: () => copyToClipboard(itemPath)
    });
    
    menuItems.push({
        text: '📊 Show Properties',
        action: () => showItemProperties(itemPath, itemType)
    });
    
    // Add menu items to menu
    menuItems.forEach((item, index) => {
        const menuItem = document.createElement('div');
        menuItem.textContent = item.text;
        menuItem.style.cssText = `
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: ${index < menuItems.length - 1 ? '1px solid #eee' : 'none'};
        `;
        
        menuItem.addEventListener('mouseenter', () => {
            menuItem.style.background = '#f0f0f0';
        });
        
        menuItem.addEventListener('mouseleave', () => {
            menuItem.style.background = 'white';
        });
        
        menuItem.addEventListener('click', () => {
            item.action();
            menu.remove();
        });
        
        menu.appendChild(menuItem);
    });
    
    document.body.appendChild(menu);
    
    // Remove menu when clicking elsewhere
    setTimeout(() => {
        document.addEventListener('click', () => {
            menu.remove();
        }, { once: true });
    }, 100);
}

/**
 * Copy text to clipboard
 * @param {string} text - Text to copy
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Path copied to clipboard!', 'success');
    }).catch(() => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Path copied to clipboard!', 'success');
    });
}

/**
 * Show item properties in a modal
 * @param {string} itemPath - Path to the item
 * @param {string} itemType - Type of item
 */
function showItemProperties(itemPath, itemType) {
    // This would typically make an AJAX call to get file properties
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 2000;
        display: flex;
        justify-content: center;
        align-items: center;
    `;
    
    const modalContent = document.createElement('div');
    modalContent.style.cssText = `
        background: white;
        padding: 20px;
        border-radius: 8px;
        max-width: 500px;
        width: 90%;
        max-height: 70vh;
        overflow-y: auto;
    `;
    
    modalContent.innerHTML = `
        <h3>📊 Item Properties</h3>
        <p><strong>Path:</strong> ${itemPath}</p>
        <p><strong>Type:</strong> ${itemType}</p>
        <p><strong>Name:</strong> ${itemPath.split('\\').pop()}</p>
        <button onclick="this.closest('.modal').remove()" style="margin-top: 15px; padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Close</button>
    `;
    
    modal.className = 'modal';
    modal.appendChild(modalContent);
    document.body.appendChild(modal);
    
    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// ============================================================================
// NOTIFICATION SYSTEM
// ============================================================================

/**
 * Show notification message
 * @param {string} message - Message to show
 * @param {string} type - Type of notification ('success', 'error', 'info')
 * @param {number} duration - Duration in milliseconds
 */
function showNotification(message, type = 'info', duration = 3000) {
    const notification = document.createElement('div');
    
    const colors = {
        success: '#28a745',
        error: '#dc3545',
        info: '#007bff',
        warning: '#ffc107'
    };
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${colors[type] || colors.info};
        color: white;
        padding: 12px 20px;
        border-radius: 5px;
        z-index: 3000;
        font-family: Arial, sans-serif;
        font-size: 14px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        animation: slideIn 0.3s ease-out;
    `;
    
    notification.textContent = message;
    
    // Add slide-in animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
    
    document.body.appendChild(notification);
    
    // Auto-remove notification
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            notification.remove();
            style.remove();
        }, 300);
    }, duration);
}

// ============================================================================
// INITIALIZATION AND EVENT LISTENERS
// ============================================================================

/**
 * Initialize the folder browser functionality
 */
function initializeFolderBrowser() {
    console.log('🚀 Initializing Folder Browser JavaScript...');
    
    // Set up event listeners for depth selector
    const depthSelector = document.querySelector('select[name="depth"]');
    if (depthSelector) {
        depthSelector.addEventListener('change', handleDepthChange);
    }
    
    // Set up event listeners for folder selector
    const folderSelector = document.querySelector('select[name="folder"]');
    if (folderSelector) {
        folderSelector.addEventListener('change', handleFolderChange);
    }
    
    // Set up search functionality
    const searchInput = document.querySelector('#folder-search');
    if (searchInput) {
        const debouncedSearch = debounce((e) => searchFolders(e.target.value), 300);
        searchInput.addEventListener('input', debouncedSearch);
    }
    
    // Set up filter functionality
    const filterSelect = document.querySelector('#type-filter');
    if (filterSelect) {
        filterSelect.addEventListener('change', (e) => filterByType(e.target.value));
    }
    
    // Add context menu to folder and file items
    document.addEventListener('contextmenu', (e) => {
        const folderLink = e.target.closest('.folder-link');
        const fileItem = e.target.closest('.file-item');
        
        if (folderLink) {
            const href = folderLink.getAttribute('href');
            const match = href.match(/folder=([^&]+)/);
            if (match) {
                const folderPath = decodeURIComponent(match[1]);
                showContextMenu(e, folderPath, 'folder');
            }
        } else if (fileItem) {
            const filePath = fileItem.dataset.path;
            if (filePath) {
                showContextMenu(e, filePath, 'file');
            }
        }
    });
    
    // Restore folder section states
    restoreFolderStates();
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        // Ctrl+F for search
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            const searchInput = document.querySelector('#folder-search');
            if (searchInput) {
                searchInput.focus();
            }
        }
        
        // Escape to clear search
        if (e.key === 'Escape') {
            const searchInput = document.querySelector('#folder-search');
            if (searchInput && searchInput.value) {
                searchInput.value = '';
                searchFolders('');
            }
        }
    });
    
    console.log('✅ Folder Browser JavaScript initialized successfully!');
}

// ============================================================================
// AUTO-INITIALIZATION
// ============================================================================

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeFolderBrowser);
} else {
    initializeFolderBrowser();
}

// Export functions for global access
window.FolderBrowser = {
    formatFileSize,
    showLoading,
    hideLoading,
    openMediaViewer,
    openMediaViewerNewTab,
    toggleFolderSection,
    collapseAllSections,
    expandAllSections,
    searchFolders,
    filterByType,
    showNotification,
    copyToClipboard
};

console.log('📁 Folder Browser JavaScript loaded successfully!');

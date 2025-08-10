let currentImages = [];
let currentIndex = 0;

// Initialize image array when page loads
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('.image-item img');
    currentImages = Array.from(images).map(img => ({
        src: img.src,
        alt: img.alt,
        fileName: img.alt,
        // Extract file info from the image's parent div
        size: img.parentElement.children[2].textContent,
        type: img.parentElement.children[3].textContent
    }));
});

function openModal(imageSrc, fileName, fileSize, fileType) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalFileName = document.getElementById('modalFileName');
    const modalFileDetails = document.getElementById('modalFileDetails');
    
    // Find current image index
    currentIndex = currentImages.findIndex(img => img.src === imageSrc);
    
    modal.style.display = 'block';
    modalImage.src = imageSrc;
    modalImage.alt = fileName;
    modalFileName.textContent = fileName;
    modalFileDetails.textContent = `Size: ${fileSize} KB | Type: ${fileType} | Image ${currentIndex + 1} of ${currentImages.length}`;
    
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('imageModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function nextImage() {
    if (currentIndex < currentImages.length - 1) {
        currentIndex++;
        updateModalImage();
    }
}

function prevImage() {
    if (currentIndex > 0) {
        currentIndex--;
        updateModalImage();
    }
}

function updateModalImage() {
    const img = currentImages[currentIndex];
    const modalImage = document.getElementById('modalImage');
    const modalFileName = document.getElementById('modalFileName');
    const modalFileDetails = document.getElementById('modalFileDetails');
    
    modalImage.src = img.src;
    modalImage.alt = img.fileName;
    modalFileName.textContent = img.fileName;
    modalFileDetails.textContent = `${img.size} | ${img.type} | Image ${currentIndex + 1} of ${currentImages.length}`;
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('imageModal');
    if (modal.style.display === 'block') {
        switch(e.key) {
            case 'Escape':
                closeModal();
                break;
            case 'ArrowLeft':
                prevImage();
                break;
            case 'ArrowRight':
                nextImage();
                break;
        }
    }
});

// Click outside to close
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
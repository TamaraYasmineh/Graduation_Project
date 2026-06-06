document.querySelectorAll('.medical-image').forEach(img => {
    img.addEventListener('click', function () {
        document.getElementById('modalImage').src = this.src;
        document.getElementById('imageModal').style.display = 'flex';
    });
});

document.querySelector('.close-modal').onclick = function () {
    document.getElementById('imageModal').style.display = 'none';
};

document.getElementById('imageModal').onclick = function (e) {
    if (e.target === this) this.style.display = 'none';
};

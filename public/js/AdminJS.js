function openModal(src) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    modal.style.display = 'block';
    modalImg.src = src;
}

function closeModal(event) {
    const modal = document.getElementById('imageModal');
    if (event.target === modal || event.target.classList.contains('modal-close')) {
        modal.style.display = 'none';
        document.getElementById('modalImage').src = '';
    }
}

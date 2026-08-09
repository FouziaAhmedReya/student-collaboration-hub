import './bootstrap';

document.querySelectorAll('[data-file-drop]').forEach((dropZone) => {
    const input = dropZone.querySelector('input[type="file"]');
    const fileName = dropZone.querySelector('[data-file-name]');

    if (!input || !fileName) return;

    const showFileName = () => {
        if (input.files?.length) {
            fileName.textContent = input.files[0].name;
        }
    };

    input.addEventListener('change', showFileName);

    ['dragenter', 'dragover'].forEach((eventName) => {
        dropZone.addEventListener(eventName, (event) => {
            event.preventDefault();
            dropZone.dataset.dragging = 'true';
        });
    });

    ['dragleave', 'drop'].forEach((eventName) => {
        dropZone.addEventListener(eventName, (event) => {
            event.preventDefault();
            dropZone.dataset.dragging = 'false';
        });
    });

    dropZone.addEventListener('drop', (event) => {
        if (!event.dataTransfer?.files.length) return;

        input.files = event.dataTransfer.files;
        showFileName();
    });
});

document.querySelectorAll('[data-confirm-delete]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm('Delete this note and its Cloudinary file? This cannot be undone.')) {
            event.preventDefault();
        }
    });
});

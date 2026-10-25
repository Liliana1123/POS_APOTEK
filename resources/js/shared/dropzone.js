/**
 * shared/dropzone.js
 * Interaktivitas Drag-and-Drop dengan animasi marching ants border dan live file preview.
 */

export function initDropzones() {
    document.querySelectorAll('.dropzone-modern').forEach(dropzone => {
        if (dropzone.dataset.dropzoneBound) return;
        dropzone.dataset.dropzoneBound = 'true';

        const fileInput = dropzone.querySelector('input[type="file"]');
        const previewEl = dropzone.querySelector('.dropzone-preview');
        const defaultContent = dropzone.querySelector('.dropzone-default-content');

        if (!fileInput) return;

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('is-dragover');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('is-dragover');
            });
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length) {
                fileInput.files = dt.files;
                fileInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];
                const sizeKb = (file.size / 1024).toFixed(1);
                if (previewEl) {
                    previewEl.textContent = `✓ ${file.name} (${sizeKb} KB)`;
                    previewEl.classList.remove('hidden');
                }
                if (defaultContent) {
                    defaultContent.classList.add('opacity-40');
                }
            } else {
                if (previewEl) {
                    previewEl.textContent = '';
                    previewEl.classList.add('hidden');
                }
                if (defaultContent) {
                    defaultContent.classList.remove('opacity-40');
                }
            }
        });
    });
}

if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDropzones);
    } else {
        initDropzones();
    }

    const observer = new MutationObserver(() => {
        initDropzones();
    });
    observer.observe(document.body, { childList: true, subtree: true });
}

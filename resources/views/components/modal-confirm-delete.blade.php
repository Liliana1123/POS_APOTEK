@props([
    'id' => 'modal-confirm-delete',
    'title' => 'Konfirmasi Hapus',
    'defaultMessage' => 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
    'confirmText' => 'Ya, Hapus Data',
    'cancelText' => 'Batal',
])

<div id="{{ $id }}"
    class="fixed inset-0 z-[9998] hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-opacity duration-200"
    aria-hidden="true"
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $id }}-title">

    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 max-w-md w-full p-6 text-center transform transition-all duration-200 scale-95 opacity-0 modal-confirm-content">
        <!-- Icon Peringatan Hapus -->
        <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-50 text-red-600 mb-4 ring-8 ring-red-50/50">
            <x-heroicon-o-trash class="w-7 h-7" />
        </div>

        <!-- Judul Dialog -->
        <h3 id="{{ $id }}-title" class="text-lg font-bold text-gray-900 mb-2">
            {{ $title }}
        </h3>

        <!-- Pesan Deskripsi -->
        <p id="{{ $id }}-message" class="text-sm text-gray-500 mb-4 leading-relaxed">
            {{ $defaultMessage }}
        </p>

        <!-- Kotak Sorotan Nama Item yang akan dihapus -->
        <div id="{{ $id }}-item-box" class="hidden mb-5 p-3 bg-red-50/40 border border-red-100 rounded-xl text-xs font-medium text-gray-700 break-words flex items-center gap-2 justify-center">
            <span class="text-red-500 shrink-0">Item:</span>
            <span id="{{ $id }}-item-name" class="font-bold text-gray-900 truncate max-w-[280px]"></span>
        </div>

        <!-- Form Aksi Hapus -->
        <form id="{{ $id }}-form" method="POST" action="" data-confirmed="true">
            @csrf
            @method('DELETE')

            <div class="flex items-center justify-center gap-3">
                <button type="button"
                    id="{{ $id }}-btn-cancel"
                    class="btn-secondary w-1/2 py-2.5 px-4 text-xs sm:text-sm font-semibold rounded-xl text-gray-700 hover:bg-gray-100 transition-colors">
                    {{ $cancelText }}
                </button>
                <button type="submit"
                    id="{{ $id }}-btn-confirm"
                    class="w-1/2 py-2.5 px-4 text-xs sm:text-sm font-semibold rounded-xl text-white bg-red-600 hover:bg-red-700 shadow-md shadow-red-500/20 transition-all flex items-center justify-center gap-2 focus:ring-2 focus:ring-red-500 focus:outline-none">
                    <x-heroicon-o-trash class="w-4 h-4 shrink-0" />
                    <span>{{ $confirmText }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        const modalId = '{{ $id }}';
        let modalEl, contentEl, formEl, messageEl, itemBoxEl, itemNameEl, cancelBtn;

        function initElements() {
            modalEl = document.getElementById(modalId);
            if (!modalEl) return false;
            contentEl = modalEl.querySelector('.modal-confirm-content');
            formEl = document.getElementById(modalId + '-form');
            messageEl = document.getElementById(modalId + '-message');
            itemBoxEl = document.getElementById(modalId + '-item-box');
            itemNameEl = document.getElementById(modalId + '-item-name');
            cancelBtn = document.getElementById(modalId + '-btn-cancel');
            return true;
        }

        window.confirmDelete = function (actionUrl, itemName = '', customMessage = '') {
            if (!initElements()) return;

            formEl.action = actionUrl;

            if (customMessage) {
                messageEl.textContent = customMessage;
            } else {
                messageEl.textContent = '{{ $defaultMessage }}';
            }

            if (itemName && itemName.trim() !== '') {
                itemNameEl.textContent = itemName;
                itemBoxEl.classList.remove('hidden');
            } else {
                itemBoxEl.classList.add('hidden');
            }

            modalEl.classList.remove('hidden');
            setTimeout(() => {
                modalEl.classList.remove('opacity-0');
                if (contentEl) {
                    contentEl.classList.remove('scale-95', 'opacity-0');
                    contentEl.classList.add('scale-100', 'opacity-100');
                }
            }, 10);

            // Fokus tombol batal untuk keselamatan
            if (cancelBtn) cancelBtn.focus();
        };

        window.closeDeleteModal = function () {
            if (!initElements()) return;
            if (contentEl) {
                contentEl.classList.remove('scale-100', 'opacity-100');
                contentEl.classList.add('scale-95', 'opacity-0');
            }
            modalEl.classList.add('opacity-0');
            setTimeout(() => {
                modalEl.classList.add('hidden');
            }, 200);
        };

        document.addEventListener('DOMContentLoaded', function () {
            initElements();

            if (cancelBtn) {
                cancelBtn.addEventListener('click', window.closeDeleteModal);
            }

            if (formEl) {
                formEl.addEventListener('submit', function () {
                    formEl.dataset.confirmed = 'true';
                    const submitBtn = document.getElementById(modalId + '-btn-confirm');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                        submitBtn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span><span>Menghapus...</span>';
                    }
                });
            }

            // Tutup jika klik backdrop
            if (modalEl) {
                modalEl.addEventListener('click', function (e) {
                    if (e.target === modalEl) {
                        window.closeDeleteModal();
                    }
                });
            }

            // Tutup jika tekan tombol Escape
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && modalEl && !modalEl.classList.contains('hidden')) {
                    window.closeDeleteModal();
                }
            });

            // Delegasi klik otomatis untuk elemen dengan data-confirm-delete
            document.addEventListener('click', function (e) {
                const trigger = e.target.closest('[data-confirm-delete]');
                if (trigger) {
                    e.preventDefault();
                    const actionUrl = trigger.getAttribute('data-confirm-delete') || trigger.getAttribute('href') || trigger.getAttribute('data-url');
                    const itemName = trigger.getAttribute('data-item-name') || '';
                    const message = trigger.getAttribute('data-confirm-message') || '';
                    if (actionUrl) {
                        window.confirmDelete(actionUrl, itemName, message);
                    }
                }
            });
        });
    })();
</script>

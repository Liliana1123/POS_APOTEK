@props([
    'id' => 'modal-confirm',
    'title' => 'Konfirmasi Tindakan',
    'defaultMessage' => 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'type' => 'warning', // warning, danger, info, success
])

<div id="{{ $id }}"
    class="fixed inset-0 z-[9998] hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-opacity duration-200"
    aria-hidden="true"
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $id }}-title">

    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 max-w-md w-full p-6 text-center transform transition-all duration-200 scale-95 opacity-0 modal-confirm-content relative overflow-hidden">
        <!-- Top Indeterminate Progress Bar (Aktif saat loading) -->
        <div id="{{ $id }}-progress-bar" class="absolute top-0 left-0 right-0 h-1 hidden overflow-hidden bg-slate-100">
            <div id="{{ $id }}-progress-indicator" class="h-full w-1/2 rounded-full modal-progress-running bg-amber-500"></div>
        </div>

        <!-- Icon Container dengan varian tema -->
        <div id="{{ $id }}-icon-wrapper" class="mx-auto flex items-center justify-center h-14 w-14 rounded-full mb-4 ring-8 transition-all bg-amber-50 text-amber-600 ring-amber-50/50">
            <div id="{{ $id }}-icon-trash" class="modal-confirm-icon hidden">
                <x-heroicon-o-trash class="w-7 h-7" />
            </div>
            <div id="{{ $id }}-icon-danger" class="modal-confirm-icon hidden">
                <x-heroicon-o-exclamation-circle class="w-7 h-7" />
            </div>
            <div id="{{ $id }}-icon-warning" class="modal-confirm-icon">
                <x-heroicon-o-exclamation-triangle class="w-7 h-7" />
            </div>
            <div id="{{ $id }}-icon-info" class="modal-confirm-icon hidden">
                <x-heroicon-o-information-circle class="w-7 h-7" />
            </div>
            <div id="{{ $id }}-icon-success" class="modal-confirm-icon hidden">
                <x-heroicon-o-check-circle class="w-7 h-7" />
            </div>
        </div>

        <!-- Konten Teks Modal -->
        <div id="{{ $id }}-body-content" class="transition-opacity duration-200">
            <!-- Judul Dialog -->
            <h3 id="{{ $id }}-title" class="text-lg font-bold text-gray-900 mb-2">
                {{ $title }}
            </h3>

            <!-- Pesan Deskripsi -->
            <p id="{{ $id }}-message" class="text-sm text-gray-500 mb-4 leading-relaxed">
                {{ $defaultMessage }}
            </p>

            <!-- Kotak Detail Opsional (Nama item yang dihapus atau catatan peringatan) -->
            <div id="{{ $id }}-detail-box" class="hidden mb-5 p-3 rounded-xl text-xs font-medium border text-gray-700 break-words flex items-center gap-2 justify-center bg-gray-50 border-gray-200">
                <span id="{{ $id }}-detail-label" class="shrink-0 text-gray-500">Info:</span>
                <span id="{{ $id }}-detail-text" class="font-bold text-gray-900 truncate max-w-[280px]"></span>
            </div>
        </div>

        <!-- Form Aksi Konfirmasi Terpadu -->
        <form id="{{ $id }}-form" method="POST" action="" data-confirmed="true">
            @csrf
            <input type="hidden" name="_method" id="{{ $id }}-method" value="POST">

            <div class="flex items-center justify-center gap-3">
                <button type="button"
                    id="{{ $id }}-btn-cancel"
                    class="btn-secondary w-1/2 py-2.5 px-4 text-xs sm:text-sm font-semibold rounded-xl text-gray-700 hover:bg-gray-100 transition-all">
                    {{ $cancelText }}
                </button>
                <button type="submit"
                    id="{{ $id }}-btn-confirm"
                    class="w-1/2 py-2.5 px-4 text-xs sm:text-sm font-semibold rounded-xl text-white transition-all flex items-center justify-center gap-2 focus:ring-2 focus:outline-none bg-amber-600 hover:bg-amber-700 shadow-md shadow-amber-500/20 focus:ring-amber-500">
                    <span id="{{ $id }}-btn-confirm-text">{{ $confirmText }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes modalProgressSlide {
        0% { transform: translateX(-100%); }
        50% { transform: translateX(50%); }
        100% { transform: translateX(200%); }
    }
    .modal-progress-running {
        animation: modalProgressSlide 1.3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }
</style>

<script>
    (function () {
        const modalId = '{{ $id }}';
        let modalEl, contentEl, formEl, titleEl, messageEl, bodyContentEl, detailBoxEl, detailLabelEl, detailTextEl;
        let iconWrapperEl, btnCancelEl, btnConfirmEl, btnConfirmTextEl, methodInputEl;
        let progressBarEl, progressIndicatorEl;
        let currentCallback = null;
        let currentConfirmText = '{{ $confirmText }}';
        let currentLoadingText = '';
        let isLoading = false;

        const themeStyles = {
            warning: {
                wrapper: ['bg-amber-50', 'text-amber-600', 'ring-amber-50/50'],
                btn: ['bg-amber-600', 'hover:bg-amber-700', 'shadow-amber-500/20', 'focus:ring-amber-500', 'text-white'],
                detailBox: ['bg-amber-50/60', 'border-amber-200'],
                detailLabel: ['text-amber-700'],
                progressBar: ['bg-amber-500']
            },
            danger: {
                wrapper: ['bg-red-50', 'text-red-600', 'ring-red-50/50'],
                btn: ['bg-red-600', 'hover:bg-red-700', 'shadow-red-500/20', 'focus:ring-red-500', 'text-white'],
                detailBox: ['bg-red-50/50', 'border-red-100'],
                detailLabel: ['text-red-600'],
                progressBar: ['bg-red-600']
            },
            info: {
                wrapper: ['bg-blue-50', 'text-blue-600', 'ring-blue-50/50'],
                btn: ['bg-blue-600', 'hover:bg-blue-700', 'shadow-blue-500/20', 'focus:ring-blue-500', 'text-white'],
                detailBox: ['bg-blue-50/60', 'border-blue-200'],
                detailLabel: ['text-blue-700'],
                progressBar: ['bg-blue-600']
            },
            success: {
                wrapper: ['bg-emerald-50', 'text-emerald-600', 'ring-emerald-50/50'],
                btn: ['bg-emerald-600', 'hover:bg-emerald-700', 'shadow-emerald-500/20', 'focus:ring-emerald-500', 'text-white'],
                detailBox: ['bg-emerald-50/60', 'border-emerald-200'],
                detailLabel: ['text-emerald-700'],
                progressBar: ['bg-emerald-600']
            }
        };

        function initElements() {
            modalEl = document.getElementById(modalId);
            if (!modalEl) return false;
            contentEl = modalEl.querySelector('.modal-confirm-content');
            formEl = document.getElementById(modalId + '-form');
            titleEl = document.getElementById(modalId + '-title');
            messageEl = document.getElementById(modalId + '-message');
            bodyContentEl = document.getElementById(modalId + '-body-content');
            detailBoxEl = document.getElementById(modalId + '-detail-box');
            detailLabelEl = document.getElementById(modalId + '-detail-label');
            detailTextEl = document.getElementById(modalId + '-detail-text');
            iconWrapperEl = document.getElementById(modalId + '-icon-wrapper');
            btnCancelEl = document.getElementById(modalId + '-btn-cancel');
            btnConfirmEl = document.getElementById(modalId + '-btn-confirm');
            btnConfirmTextEl = document.getElementById(modalId + '-btn-confirm-text');
            methodInputEl = document.getElementById(modalId + '-method');
            progressBarEl = document.getElementById(modalId + '-progress-bar');
            progressIndicatorEl = document.getElementById(modalId + '-progress-indicator');
            return true;
        }

        function applyTheme(type, iconType) {
            const allIcons = ['trash', 'danger', 'warning', 'info', 'success'];
            const targetType = themeStyles[type] ? type : 'warning';
            const targetIcon = iconType || (type === 'danger' ? 'danger' : targetType);

            // Toggle icon SVG
            allIcons.forEach(ic => {
                const iconEl = document.getElementById(modalId + '-icon-' + ic);
                if (iconEl) iconEl.classList.toggle('hidden', ic !== targetIcon);
            });

            // Reset class wrapper
            if (iconWrapperEl) {
                iconWrapperEl.className = 'mx-auto flex items-center justify-center h-14 w-14 rounded-full mb-4 ring-8 transition-all ' +
                    themeStyles[targetType].wrapper.join(' ');
            }

            // Reset tombol konfirmasi class
            if (btnConfirmEl) {
                btnConfirmEl.className = 'w-1/2 py-2.5 px-4 text-xs sm:text-sm font-semibold rounded-xl transition-all flex items-center justify-center gap-2 focus:ring-2 focus:outline-none shadow-md ' +
                    themeStyles[targetType].btn.join(' ');
            }

            // Reset detail box class
            if (detailBoxEl) {
                const isHidden = detailBoxEl.classList.contains('hidden');
                detailBoxEl.className = 'mb-5 p-3 rounded-xl text-xs font-medium border text-gray-700 break-words flex items-center gap-2 justify-center ' +
                    themeStyles[targetType].detailBox.join(' ') + (isHidden ? ' hidden' : '');
            }

            if (detailLabelEl) {
                detailLabelEl.className = 'shrink-0 font-semibold ' + themeStyles[targetType].detailLabel.join(' ');
            }

            // Reset progress bar class
            if (progressIndicatorEl) {
                progressIndicatorEl.className = 'h-full w-1/2 rounded-full modal-progress-running ' + themeStyles[targetType].progressBar.join(' ');
            }
        }

        function startLoading(customText = null) {
            isLoading = true;

            // 1. Kunci tombol Batal (disable + fade)
            if (btnCancelEl) {
                btnCancelEl.disabled = true;
                btnCancelEl.classList.add('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
            }

            // 2. Kunci tombol Konfirmasi, tampilkan spinner + teks kontekstual
            if (btnConfirmEl) {
                btnConfirmEl.disabled = true;
                btnConfirmEl.classList.add('opacity-80', 'cursor-not-allowed');
                const isDelete = (methodInputEl && methodInputEl.value === 'DELETE');
                const defaultText = isDelete ? 'Menghapus...' : 'Memproses...';
                const text = customText || currentLoadingText || defaultText;
                btnConfirmEl.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span><span>' + text + '</span>';
            }

            // 3. Tampilkan top progress bar
            if (progressBarEl) {
                progressBarEl.classList.remove('hidden');
            }

            // 4. Denyut halus pada icon
            if (iconWrapperEl) {
                iconWrapperEl.classList.add('animate-pulse');
            }

            // 5. Redupkan konten teks agar fokus ke proses
            if (bodyContentEl) {
                bodyContentEl.classList.add('opacity-50');
            }
        }

        function stopLoading() {
            isLoading = false;

            // 1. Aktifkan kembali tombol Batal
            if (btnCancelEl) {
                btnCancelEl.disabled = false;
                btnCancelEl.classList.remove('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
            }

            // 2. Aktifkan kembali tombol Konfirmasi
            if (btnConfirmEl) {
                btnConfirmEl.disabled = false;
                btnConfirmEl.classList.remove('opacity-80', 'cursor-not-allowed');
                btnConfirmEl.innerHTML = '<span id="' + modalId + '-btn-confirm-text">' + currentConfirmText + '</span>';
                btnConfirmTextEl = document.getElementById(modalId + '-btn-confirm-text');
            }

            // 3. Sembunyikan top progress bar
            if (progressBarEl) {
                progressBarEl.classList.add('hidden');
            }

            // 4. Hentikan denyut icon
            if (iconWrapperEl) {
                iconWrapperEl.classList.remove('animate-pulse');
            }

            // 5. Kembalikan opasitas konten
            if (bodyContentEl) {
                bodyContentEl.classList.remove('opacity-50');
            }
        }

        /**
         * Universal Action Confirmation (POST, PUT, PATCH, GET, JS callback)
         */
        window.confirmAction = function (options = {}) {
            if (!initElements()) return;

            // Pastikan state loading sebelumnya bersih
            stopLoading();

            const config = {
                title: options.title || '{{ $title }}',
                message: options.message || '{{ $defaultMessage }}',
                detail: options.detail || '',
                detailLabel: options.detailLabel || 'Info:',
                type: options.type || '{{ $type }}',
                icon: options.icon || null,
                confirmText: options.confirmText || '{{ $confirmText }}',
                loadingText: options.loadingText || '',
                cancelText: options.cancelText || '{{ $cancelText }}',
                action: options.action || '',
                formId: options.formId || '',
                method: (options.method || 'POST').toUpperCase(),
                onConfirm: typeof options.onConfirm === 'function' ? options.onConfirm : null
            };

            currentCallback = config.onConfirm;
            currentConfirmText = config.confirmText;
            currentLoadingText = config.loadingText;

            // Atur teks
            if (titleEl) titleEl.textContent = config.title;
            if (messageEl) messageEl.innerHTML = config.message;
            if (btnConfirmTextEl) btnConfirmTextEl.textContent = config.confirmText;
            if (btnCancelEl) btnCancelEl.textContent = config.cancelText;

            // Tema warna & icon
            applyTheme(config.type, config.icon);

            // Detail box
            if (config.detail && config.detail.trim() !== '') {
                detailLabelEl.textContent = config.detailLabel;
                detailTextEl.textContent = config.detail;
                detailBoxEl.classList.remove('hidden');
            } else {
                detailBoxEl.classList.add('hidden');
            }

            // Form konfigurasi
            if (formEl) {
                formEl.dataset.targetForm = config.formId;
                formEl.dataset.targetAction = config.action;
                formEl.dataset.targetMethod = config.method;

                if (config.action && !config.action.startsWith('#')) {
                    formEl.action = config.action;
                    if (methodInputEl) methodInputEl.value = config.method;
                } else {
                    formEl.action = '';
                }
            }

            // Tampilkan modal
            modalEl.classList.remove('hidden');
            setTimeout(() => {
                modalEl.classList.remove('opacity-0');
                if (contentEl) {
                    contentEl.classList.remove('scale-95', 'opacity-0');
                    contentEl.classList.add('scale-100', 'opacity-100');
                }
            }, 10);

            if (btnCancelEl) btnCancelEl.focus();
        };

        /**
         * Backward-compatible Delete Confirmation
         */
        window.confirmDelete = function (actionUrl, itemName = '', customMessage = '', customLoadingText = '') {
            window.confirmAction({
                action: actionUrl,
                title: 'Konfirmasi Hapus',
                message: customMessage || 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
                detail: itemName,
                detailLabel: 'Item:',
                type: 'danger',
                icon: 'trash',
                method: 'DELETE',
                confirmText: 'Ya, Hapus Data',
                loadingText: customLoadingText || 'Menghapus Data...',
                cancelText: 'Batal'
            });
        };

        window.closeConfirmModal = function () {
            if (!initElements()) return;
            if (isLoading) return; // Mencegah modal tertutup saat sedang loading

            if (contentEl) {
                contentEl.classList.remove('scale-100', 'opacity-100');
                contentEl.classList.add('scale-95', 'opacity-0');
            }
            modalEl.classList.add('opacity-0');
            setTimeout(() => {
                modalEl.classList.add('hidden');
                currentCallback = null;
                stopLoading();
            }, 200);
        };

        // Aliases for backward compatibility
        window.closeDeleteModal = window.closeConfirmModal;
        window.closeActionModal = window.closeConfirmModal;
        window.resetConfirmLoading = stopLoading;

        document.addEventListener('DOMContentLoaded', function () {
            initElements();

            if (btnCancelEl) {
                btnCancelEl.addEventListener('click', function () {
                    if (!isLoading) window.closeConfirmModal();
                });
            }

            if (formEl) {
                formEl.addEventListener('submit', function (e) {
                    // Mulai animasi loading penuh & kunci interaksi
                    startLoading();

                    // Jika ada custom JavaScript callback
                    if (currentCallback) {
                        e.preventDefault();
                        const cb = currentCallback;
                        cb();
                        return;
                    }

                    const targetFormId = formEl.dataset.targetForm;
                    const targetAction = formEl.dataset.targetAction;
                    const targetMethod = formEl.dataset.targetMethod;

                    // Jika targetnya adalah form lain di halaman
                    if (targetFormId) {
                        e.preventDefault();
                        const extForm = document.querySelector(targetFormId);
                        if (extForm) {
                            extForm.submit();
                        }
                        return;
                    }

                    // Jika targetnya navigasi GET URL
                    if (targetMethod === 'GET' && targetAction) {
                        e.preventDefault();
                        window.location.href = targetAction;
                        return;
                    }

                    // Form modal submit (POST/PATCH/DELETE) berjalan otomatis secara default
                });
            }

            // Tutup jika klik backdrop (Hanya jika TIDAK sedang loading)
            if (modalEl) {
                modalEl.addEventListener('click', function (e) {
                    if (e.target === modalEl && !isLoading) {
                        window.closeConfirmModal();
                    }
                });
            }

            // Tutup jika tekan tombol Escape (Hanya jika TIDAK sedang loading)
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && modalEl && !modalEl.classList.contains('hidden') && !isLoading) {
                    window.closeConfirmModal();
                }
            });

            // 1. Delegasi klik otomatis untuk elemen data-confirm-delete (Tabel & tombol hapus)
            document.addEventListener('click', function (e) {
                const trigger = e.target.closest('[data-confirm-delete]');
                if (trigger) {
                    e.preventDefault();
                    const actionUrl = trigger.getAttribute('data-confirm-delete') || trigger.getAttribute('href') || trigger.getAttribute('data-url');
                    const itemName = trigger.getAttribute('data-item-name') || '';
                    const message = trigger.getAttribute('data-confirm-message') || '';
                    const loadingText = trigger.getAttribute('data-confirm-loading-text') || 'Menghapus Data...';
                    if (actionUrl) {
                        window.confirmDelete(actionUrl, itemName, message, loadingText);
                    }
                }
            });

            // 2. Delegasi klik otomatis untuk elemen data-confirm-action (Aksi universal)
            document.addEventListener('click', function (e) {
                const trigger = e.target.closest('[data-confirm-action]');
                if (trigger) {
                    e.preventDefault();
                    const action = trigger.getAttribute('data-confirm-action') || trigger.getAttribute('href') || '';
                    const formId = trigger.getAttribute('data-confirm-form') || (action.startsWith('#') ? action : '');
                    const title = trigger.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
                    const message = trigger.getAttribute('data-confirm-message') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                    const detail = trigger.getAttribute('data-confirm-detail') || '';
                    const detailLabel = trigger.getAttribute('data-confirm-detail-label') || 'Info:';
                    const type = trigger.getAttribute('data-confirm-type') || 'warning';
                    const confirmText = trigger.getAttribute('data-confirm-btn-text') || 'Ya, Lanjutkan';
                    const loadingText = trigger.getAttribute('data-confirm-loading-text') || '';
                    const cancelText = trigger.getAttribute('data-confirm-cancel-text') || 'Batal';
                    const method = trigger.getAttribute('data-confirm-method') || 'POST';

                    window.confirmAction({
                        action: action,
                        formId: formId,
                        title: title,
                        message: message,
                        detail: detail,
                        detailLabel: detailLabel,
                        type: type,
                        confirmText: confirmText,
                        loadingText: loadingText,
                        cancelText: cancelText,
                        method: method
                    });
                }
            });
        });
    })();
</script>

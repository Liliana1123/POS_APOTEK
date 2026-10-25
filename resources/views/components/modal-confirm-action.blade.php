@props([
    'id' => 'modal-confirm-action',
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

    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 max-w-md w-full p-6 text-center transform transition-all duration-200 scale-95 opacity-0 modal-confirm-action-content">
        <!-- Icon Container dengan varian tema -->
        <div id="{{ $id }}-icon-wrapper" class="mx-auto flex items-center justify-center h-14 w-14 rounded-full mb-4 ring-8 transition-colors bg-amber-50 text-amber-600 ring-amber-50/50">
            <div id="{{ $id }}-icon-warning" class="modal-action-icon">
                <x-heroicon-o-exclamation-triangle class="w-7 h-7" />
            </div>
            <div id="{{ $id }}-icon-danger" class="modal-action-icon hidden">
                <x-heroicon-o-exclamation-circle class="w-7 h-7" />
            </div>
            <div id="{{ $id }}-icon-info" class="modal-action-icon hidden">
                <x-heroicon-o-information-circle class="w-7 h-7" />
            </div>
            <div id="{{ $id }}-icon-success" class="modal-action-icon hidden">
                <x-heroicon-o-check-circle class="w-7 h-7" />
            </div>
        </div>

        <!-- Judul Dialog -->
        <h3 id="{{ $id }}-title" class="text-lg font-bold text-gray-900 mb-2">
            {{ $title }}
        </h3>

        <!-- Pesan Deskripsi -->
        <p id="{{ $id }}-message" class="text-sm text-gray-500 mb-4 leading-relaxed">
            {{ $defaultMessage }}
        </p>

        <!-- Kotak Detail Opsional (misal nama item atau catatan peringatan) -->
        <div id="{{ $id }}-detail-box" class="hidden mb-5 p-3 rounded-xl text-xs font-medium border text-gray-700 break-words flex items-center gap-2 justify-center bg-gray-50 border-gray-200">
            <span id="{{ $id }}-detail-label" class="shrink-0 text-gray-500">Info:</span>
            <span id="{{ $id }}-detail-text" class="font-bold text-gray-900 truncate max-w-[280px]"></span>
        </div>

        <!-- Form Aksi Konfirmasi -->
        <form id="{{ $id }}-form" method="POST" action="" data-confirmed="true">
            @csrf
            <input type="hidden" name="_method" id="{{ $id }}-method" value="POST">

            <div class="flex items-center justify-center gap-3">
                <button type="button"
                    id="{{ $id }}-btn-cancel"
                    class="btn-secondary w-1/2 py-2.5 px-4 text-xs sm:text-sm font-semibold rounded-xl text-gray-700 hover:bg-gray-100 transition-colors">
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

<script>
    (function () {
        const modalId = '{{ $id }}';
        let modalEl, contentEl, formEl, titleEl, messageEl, detailBoxEl, detailLabelEl, detailTextEl;
        let iconWrapperEl, btnCancelEl, btnConfirmEl, btnConfirmTextEl, methodInputEl;
        let currentCallback = null;

        const themeStyles = {
            warning: {
                wrapper: ['bg-amber-50', 'text-amber-600', 'ring-amber-50/50'],
                btn: ['bg-amber-600', 'hover:bg-amber-700', 'shadow-amber-500/20', 'focus:ring-amber-500', 'text-white'],
                detailBox: ['bg-amber-50/60', 'border-amber-200'],
                detailLabel: ['text-amber-700']
            },
            danger: {
                wrapper: ['bg-red-50', 'text-red-600', 'ring-red-50/50'],
                btn: ['bg-red-600', 'hover:bg-red-700', 'shadow-red-500/20', 'focus:ring-red-500', 'text-white'],
                detailBox: ['bg-red-50/40', 'border-red-100'],
                detailLabel: ['text-red-500']
            },
            info: {
                wrapper: ['bg-blue-50', 'text-blue-600', 'ring-blue-50/50'],
                btn: ['bg-blue-600', 'hover:bg-blue-700', 'shadow-blue-500/20', 'focus:ring-blue-500', 'text-white'],
                detailBox: ['bg-blue-50/60', 'border-blue-200'],
                detailLabel: ['text-blue-700']
            },
            success: {
                wrapper: ['bg-emerald-50', 'text-emerald-600', 'ring-emerald-50/50'],
                btn: ['bg-emerald-600', 'hover:bg-emerald-700', 'shadow-emerald-500/20', 'focus:ring-emerald-500', 'text-white'],
                detailBox: ['bg-emerald-50/60', 'border-emerald-200'],
                detailLabel: ['text-emerald-700']
            }
        };

        function initElements() {
            modalEl = document.getElementById(modalId);
            if (!modalEl) return false;
            contentEl = modalEl.querySelector('.modal-confirm-action-content');
            formEl = document.getElementById(modalId + '-form');
            titleEl = document.getElementById(modalId + '-title');
            messageEl = document.getElementById(modalId + '-message');
            detailBoxEl = document.getElementById(modalId + '-detail-box');
            detailLabelEl = document.getElementById(modalId + '-detail-label');
            detailTextEl = document.getElementById(modalId + '-detail-text');
            iconWrapperEl = document.getElementById(modalId + '-icon-wrapper');
            btnCancelEl = document.getElementById(modalId + '-btn-cancel');
            btnConfirmEl = document.getElementById(modalId + '-btn-confirm');
            btnConfirmTextEl = document.getElementById(modalId + '-btn-confirm-text');
            methodInputEl = document.getElementById(modalId + '-method');
            return true;
        }

        function applyTheme(type) {
            const allThemes = ['warning', 'danger', 'info', 'success'];
            const targetType = themeStyles[type] ? type : 'warning';

            // Toggle icon SVG
            allThemes.forEach(t => {
                const icon = document.getElementById(modalId + '-icon-' + t);
                if (icon) icon.classList.toggle('hidden', t !== targetType);
            });

            // Reset class wrapper
            if (iconWrapperEl) {
                iconWrapperEl.className = 'mx-auto flex items-center justify-center h-14 w-14 rounded-full mb-4 ring-8 transition-colors ' +
                    themeStyles[targetType].wrapper.join(' ');
            }

            // Reset tombol konfirmasi class
            if (btnConfirmEl) {
                btnConfirmEl.className = 'w-1/2 py-2.5 px-4 text-xs sm:text-sm font-semibold rounded-xl transition-all flex items-center justify-center gap-2 focus:ring-2 focus:outline-none shadow-md ' +
                    themeStyles[targetType].btn.join(' ');
            }

            // Reset detail box class
            if (detailBoxEl) {
                detailBoxEl.className = 'mb-5 p-3 rounded-xl text-xs font-medium border text-gray-700 break-words flex items-center gap-2 justify-center ' +
                    themeStyles[targetType].detailBox.join(' ');
            }

            if (detailLabelEl) {
                detailLabelEl.className = 'shrink-0 font-semibold ' + themeStyles[targetType].detailLabel.join(' ');
            }
        }

        window.confirmAction = function (options = {}) {
            if (!initElements()) return;

            const config = {
                title: options.title || '{{ $title }}',
                message: options.message || '{{ $defaultMessage }}',
                detail: options.detail || '',
                detailLabel: options.detailLabel || 'Info:',
                type: options.type || '{{ $type }}',
                confirmText: options.confirmText || '{{ $confirmText }}',
                cancelText: options.cancelText || '{{ $cancelText }}',
                action: options.action || '',
                formId: options.formId || '',
                method: (options.method || 'POST').toUpperCase(),
                onConfirm: typeof options.onConfirm === 'function' ? options.onConfirm : null
            };

            currentCallback = config.onConfirm;

            // Atur teks
            if (titleEl) titleEl.textContent = config.title;
            if (messageEl) messageEl.innerHTML = config.message;
            if (btnConfirmTextEl) btnConfirmTextEl.textContent = config.confirmText;
            if (btnCancelEl) btnCancelEl.textContent = config.cancelText;

            // Detail box
            if (config.detail && config.detail.trim() !== '') {
                detailLabelEl.textContent = config.detailLabel;
                detailTextEl.textContent = config.detail;
                detailBoxEl.classList.remove('hidden');
            } else {
                detailBoxEl.classList.add('hidden');
            }

            // Tema warna & icon
            applyTheme(config.type);

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

        window.closeActionModal = function () {
            if (!initElements()) return;
            if (contentEl) {
                contentEl.classList.remove('scale-100', 'opacity-100');
                contentEl.classList.add('scale-95', 'opacity-0');
            }
            modalEl.classList.add('opacity-0');
            setTimeout(() => {
                modalEl.classList.add('hidden');
                currentCallback = null;
                // Reset tombol konfirmasi jika sebelumnya loading
                if (btnConfirmEl) {
                    btnConfirmEl.disabled = false;
                    btnConfirmEl.classList.remove('opacity-75', 'cursor-not-allowed');
                    if (btnConfirmTextEl) btnConfirmTextEl.textContent = '{{ $confirmText }}';
                }
            }, 200);
        };

        document.addEventListener('DOMContentLoaded', function () {
            initElements();

            if (btnCancelEl) {
                btnCancelEl.addEventListener('click', window.closeActionModal);
            }

            if (formEl) {
                formEl.addEventListener('submit', function (e) {
                    // Jika ada callback custom
                    if (currentCallback) {
                        e.preventDefault();
                        const cb = currentCallback;
                        window.closeActionModal();
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
                            window.closeActionModal();
                            extForm.submit();
                        }
                        return;
                    }

                    // Jika targetnya GET URL (navigasi)
                    if (targetMethod === 'GET' && targetAction) {
                        e.preventDefault();
                        window.location.href = targetAction;
                        return;
                    }

                    // Jika form modal ini yang disubmit (POST/PUT/dll)
                    if (btnConfirmEl) {
                        btnConfirmEl.disabled = true;
                        btnConfirmEl.classList.add('opacity-75', 'cursor-not-allowed');
                        btnConfirmEl.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span><span>Memproses...</span>';
                    }
                });
            }

            // Tutup jika klik backdrop
            if (modalEl) {
                modalEl.addEventListener('click', function (e) {
                    if (e.target === modalEl) {
                        window.closeActionModal();
                    }
                });
            }

            // Tutup jika tekan tombol Escape
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && modalEl && !modalEl.classList.contains('hidden')) {
                    window.closeActionModal();
                }
            });

            // Delegasi klik otomatis untuk elemen dengan data-confirm-action
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
                        cancelText: cancelText,
                        method: method
                    });
                }
            });
        });
    })();
</script>

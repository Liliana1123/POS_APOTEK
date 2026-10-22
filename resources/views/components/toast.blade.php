{{--
    Komponen Toast Floating Reusable (Top-Center Eye-Level Focus)
    Menangani session flash ('success', 'error', 'warning', 'info')
    serta menyediakan fungsi global JS window.showToast(message, type, duration)
--}}
<div id="global-toast-container" class="fixed top-6 left-1/2 -translate-x-1/2 z-[9999] pointer-events-none flex flex-col items-center gap-2 max-w-[90vw] sm:max-w-md w-full px-4">
    @php
        $flashTypes = [
            'success' => [
                'icon' => 'check-circle',
                'border' => 'border-emerald-500/40',
                'iconColor' => 'text-emerald-400',
                'badge' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20',
                'title' => 'Berhasil',
            ],
            'error' => [
                'icon' => 'x-circle',
                'border' => 'border-rose-500/40',
                'iconColor' => 'text-rose-400',
                'badge' => 'bg-rose-500/10 text-rose-300 border-rose-500/20',
                'title' => 'Gagal',
            ],
            'warning' => [
                'icon' => 'exclamation-triangle',
                'border' => 'border-amber-500/40',
                'iconColor' => 'text-amber-400',
                'badge' => 'bg-amber-500/10 text-amber-300 border-amber-500/20',
                'title' => 'Perhatian',
            ],
            'info' => [
                'icon' => 'information-circle',
                'border' => 'border-sky-500/40',
                'iconColor' => 'text-sky-400',
                'badge' => 'bg-sky-500/10 text-sky-300 border-sky-500/20',
                'title' => 'Informasi',
            ],
        ];
    @endphp

    @foreach ($flashTypes as $type => $config)
        @if (session($type))
            <div
                class="toast-pill pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-2xl bg-slate-900/95 backdrop-blur-md text-white text-xs shadow-2xl border {{ $config['border'] }} transition-all duration-300 transform translate-y-0 opacity-100 w-full sm:w-auto"
                role="status"
                data-toast-type="{{ $type }}"
            >
                @if ($type === 'success')
                    <x-heroicon-s-check-circle class="w-5 h-5 {{ $config['iconColor'] }} shrink-0" />
                @elseif ($type === 'error')
                    <x-heroicon-s-x-circle class="w-5 h-5 {{ $config['iconColor'] }} shrink-0" />
                @elseif ($type === 'warning')
                    <x-heroicon-s-exclamation-triangle class="w-5 h-5 {{ $config['iconColor'] }} shrink-0" />
                @else
                    <x-heroicon-s-information-circle class="w-5 h-5 {{ $config['iconColor'] }} shrink-0" />
                @endif

                <div class="flex flex-col min-w-0 pr-1">
                    <span class="font-medium text-slate-100 break-words leading-tight">{{ session($type) }}</span>
                </div>

                <button
                    type="button"
                    onclick="dismissToast(this.closest('.toast-pill'))"
                    class="ml-auto text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors"
                    aria-label="Tutup notifikasi"
                >
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </div>
        @endif
    @endforeach
</div>

<script>
    // Fungsi tutup toast dengan animasi slide-up halus
    function dismissToast(toastEl) {
        if (!toastEl) return;
        toastEl.classList.remove('translate-y-0', 'opacity-100');
        toastEl.classList.add('-translate-y-4', 'opacity-0');
        setTimeout(() => {
            if (toastEl && toastEl.parentElement) {
                toastEl.remove();
            }
        }, 300);
    }

    // Global helper agar JavaScript/AJAX bisa memanggil toast seragam
    window.showToast = function (message, type = 'success', duration = 3800) {
        const container = document.getElementById('global-toast-container');
        if (!container) return;

        const typeConfigs = {
            success: {
                border: 'border-emerald-500/40',
                iconColor: 'text-emerald-400',
                svg: '<svg class="w-5 h-5 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>'
            },
            error: {
                border: 'border-rose-500/40',
                iconColor: 'text-rose-400',
                svg: '<svg class="w-5 h-5 text-rose-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>'
            },
            warning: {
                border: 'border-amber-500/40',
                iconColor: 'text-amber-400',
                svg: '<svg class="w-5 h-5 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>'
            },
            info: {
                border: 'border-sky-500/40',
                iconColor: 'text-sky-400',
                svg: '<svg class="w-5 h-5 text-sky-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" /></svg>'
            }
        };

        const cfg = typeConfigs[type] || typeConfigs.info;
        const toastEl = document.createElement('div');
        toastEl.className = `toast-pill pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-2xl bg-slate-900/95 backdrop-blur-md text-white text-xs shadow-2xl border ${cfg.border} transition-all duration-300 transform -translate-y-4 opacity-0 w-full sm:w-auto`;
        toastEl.innerHTML = `
            ${cfg.svg}
            <div class="flex flex-col min-w-0 pr-1">
                <span class="font-medium text-slate-100 break-words leading-tight">${message}</span>
            </div>
            <button type="button" onclick="dismissToast(this.closest('.toast-pill'))" class="ml-auto text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors" aria-label="Tutup notifikasi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        `;

        container.appendChild(toastEl);

        // Animasi muncul (trigger reflow)
        requestAnimationFrame(() => {
            toastEl.classList.remove('-translate-y-4', 'opacity-0');
            toastEl.classList.add('translate-y-0', 'opacity-100');
        });

        // Auto-dismiss
        let timeoutId = setTimeout(() => dismissToast(toastEl), duration);
        toastEl.addEventListener('mouseenter', () => clearTimeout(timeoutId));
        toastEl.addEventListener('mouseleave', () => {
            timeoutId = setTimeout(() => dismissToast(toastEl), 2000);
        });
    };

    // Inisialisasi auto-dismiss untuk toast sesi bawaan dan pending toast
    function initToasts() {
        // 1. Cek pending toast dari sessionStorage (setelah reload AJAX modal)
        try {
            const pending = sessionStorage.getItem('pending_toast');
            if (pending) {
                sessionStorage.removeItem('pending_toast');
                const parsed = JSON.parse(pending);
                if (parsed && parsed.message) {
                    const alreadyRendered = Array.from(document.querySelectorAll('#global-toast-container .toast-pill span'))
                        .some(el => el.textContent.trim() === parsed.message.trim());
                    if (!alreadyRendered) {
                        window.showToast(parsed.message, parsed.type || 'success', 3800);
                    }
                }
            }
        } catch (e) {
            console.error('Error pending_toast:', e);
        }

        // 2. Set timer auto-dismiss untuk toast sesi Blade yang sudah ada di HTML
        const sessionToasts = document.querySelectorAll('#global-toast-container .toast-pill');
        sessionToasts.forEach(toast => {
            let timeoutId = setTimeout(() => dismissToast(toast), 3800);

            // Jeda timer jika cursor user sedang membaca pesan
            toast.addEventListener('mouseenter', () => clearTimeout(timeoutId));
            toast.addEventListener('mouseleave', () => {
                timeoutId = setTimeout(() => dismissToast(toast), 2000);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initToasts);
    } else {
        initToasts();
    }
</script>

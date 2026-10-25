{{--
    Komponen Toast Floating Reusable (Clean Light Card + Progress Bar - Top-Center Eye-Level Focus)
    Menangani session flash ('success', 'error', 'warning', 'info')
    serta menyediakan fungsi global JS window.showToast(message, type, duration)
--}}
<div id="global-toast-container" class="fixed top-6 left-1/2 -translate-x-1/2 z-[9999] pointer-events-none flex flex-col items-center gap-3 max-w-[92vw] sm:max-w-md w-full px-4">
    @php
        $flashTypes = [
            'success' => [
                'icon' => 'check-circle',
                'border' => 'border-emerald-200 shadow-emerald-500/10',
                'iconWrapper' => 'bg-emerald-100 text-emerald-600',
                'ring' => 'ring-emerald-50',
                'titleColor' => 'text-emerald-700',
                'barColor' => 'bg-emerald-500',
                'title' => 'Berhasil',
            ],
            'error' => [
                'icon' => 'x-circle',
                'border' => 'border-rose-200 shadow-rose-500/10',
                'iconWrapper' => 'bg-rose-100 text-rose-600',
                'ring' => 'ring-rose-50',
                'titleColor' => 'text-rose-700',
                'barColor' => 'bg-rose-500',
                'title' => 'Gagal',
            ],
            'warning' => [
                'icon' => 'exclamation-triangle',
                'border' => 'border-amber-200 shadow-amber-500/10',
                'iconWrapper' => 'bg-amber-100 text-amber-600',
                'ring' => 'ring-amber-50',
                'titleColor' => 'text-amber-700',
                'barColor' => 'bg-amber-500',
                'title' => 'Perhatian',
            ],
            'info' => [
                'icon' => 'information-circle',
                'border' => 'border-sky-200 shadow-sky-500/10',
                'iconWrapper' => 'bg-sky-100 text-sky-600',
                'ring' => 'ring-sky-50',
                'titleColor' => 'text-sky-700',
                'barColor' => 'bg-sky-500',
                'title' => 'Informasi',
            ],
        ];
    @endphp

    @foreach ($flashTypes as $type => $config)
        @if (session($type))
            <div
                class="toast-card pointer-events-auto w-full bg-white/95 backdrop-blur-md rounded-2xl border shadow-2xl overflow-hidden transform translate-y-0 opacity-100 scale-100 transition-all duration-300 {{ $config['border'] }}"
                role="status"
                data-toast-type="{{ $type }}"
            >
                <div class="p-3.5 sm:p-4 flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl {{ $config['iconWrapper'] }} flex items-center justify-center shrink-0 ring-4 {{ $config['ring'] }}">
                        @if ($type === 'success')
                            <x-heroicon-s-check-circle class="w-6 h-6" />
                        @elseif ($type === 'error')
                            <x-heroicon-s-x-circle class="w-6 h-6" />
                        @elseif ($type === 'warning')
                            <x-heroicon-s-exclamation-triangle class="w-6 h-6" />
                        @else
                            <x-heroicon-s-information-circle class="w-6 h-6" />
                        @endif
                    </div>

                    <div class="flex-1 min-w-0 pt-0.5">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-[11px] font-bold uppercase tracking-wider {{ $config['titleColor'] }}">{{ $config['title'] }}</span>
                            <span class="text-[10px] text-slate-400 font-medium">Baru saja</span>
                        </div>
                        <p class="text-xs font-semibold text-slate-700 leading-snug break-words">
                            {{ session($type) }}
                        </p>
                    </div>

                    <button
                        type="button"
                        onclick="dismissToast(this.closest('.toast-card'))"
                        class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition-colors shrink-0 -mr-1 -mt-1"
                        aria-label="Tutup notifikasi"
                    >
                        <x-heroicon-o-x-mark class="w-4 h-4" />
                    </button>
                </div>

                {{-- Progress Bar Timer --}}
                <div class="h-1 w-full bg-slate-100 overflow-hidden">
                    <div class="toast-progress-bar h-full {{ $config['barColor'] }}" style="animation: toastProgressBar 3800ms linear forwards;"></div>
                </div>
            </div>
        @endif
    @endforeach
</div>

<style>
    @keyframes toastProgressBar {
        from {
            width: 100%;
        }
        to {
            width: 0%;
        }
    }
    @keyframes toastSpringIn {
        0% {
            transform: translateY(-26px) scale(0.92);
            opacity: 0;
        }
        65% {
            transform: translateY(4px) scale(1.015);
            opacity: 1;
        }
        100% {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
    }
    .toast-card {
        animation: toastSpringIn 420ms cubic-bezier(0.16, 1, 0.3, 1) forwards;
        transition: all 350ms cubic-bezier(0.16, 1, 0.3, 1);
    }
    .toast-card:hover .toast-progress-bar {
        animation-play-state: paused !important;
    }
</style>

<script>
    // Fungsi tutup toast dengan animasi slide-up & scale-down halus
    function dismissToast(toastEl) {
        if (!toastEl) return;
        toastEl.classList.remove('translate-y-0', 'opacity-100', 'scale-100');
        toastEl.classList.add('-translate-y-4', 'opacity-0', 'scale-95');
        setTimeout(() => {
            if (toastEl && toastEl.parentElement) {
                toastEl.remove();
            }
        }, 320);
    }

    // Global helper agar JavaScript/AJAX bisa memanggil toast seragam
    window.showToast = function (message, type = 'success', duration = 3800) {
        const container = document.getElementById('global-toast-container');
        if (!container) return;

        const typeConfigs = {
            success: {
                border: 'border-emerald-200 shadow-emerald-500/10',
                iconWrapper: 'bg-emerald-100 text-emerald-600',
                ring: 'ring-emerald-50',
                titleColor: 'text-emerald-700',
                barColor: 'bg-emerald-500',
                title: 'Berhasil',
                svg: '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>'
            },
            error: {
                border: 'border-rose-200 shadow-rose-500/10',
                iconWrapper: 'bg-rose-100 text-rose-600',
                ring: 'ring-rose-50',
                titleColor: 'text-rose-700',
                barColor: 'bg-rose-500',
                title: 'Gagal',
                svg: '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>'
            },
            warning: {
                border: 'border-amber-200 shadow-amber-500/10',
                iconWrapper: 'bg-amber-100 text-amber-600',
                ring: 'ring-amber-50',
                titleColor: 'text-amber-700',
                barColor: 'bg-amber-500',
                title: 'Perhatian',
                svg: '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>'
            },
            info: {
                border: 'border-sky-200 shadow-sky-500/10',
                iconWrapper: 'bg-sky-100 text-sky-600',
                ring: 'ring-sky-50',
                titleColor: 'text-sky-700',
                barColor: 'bg-sky-500',
                title: 'Informasi',
                svg: '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" /></svg>'
            }
        };

        const cfg = typeConfigs[type] || typeConfigs.info;
        const toastEl = document.createElement('div');
        toastEl.className = `toast-card pointer-events-auto w-full bg-white/95 backdrop-blur-md rounded-2xl border shadow-2xl overflow-hidden transform -translate-y-6 opacity-0 scale-95 transition-all duration-300 ${cfg.border}`;
        toastEl.innerHTML = `
            <div class="p-3.5 sm:p-4 flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl ${cfg.iconWrapper} flex items-center justify-center shrink-0 ring-4 ${cfg.ring}">
                    ${cfg.svg}
                </div>
                <div class="flex-1 min-w-0 pt-0.5">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider ${cfg.titleColor}">${cfg.title}</span>
                        <span class="text-[10px] text-slate-400 font-medium">Baru saja</span>
                    </div>
                    <p class="text-xs font-semibold text-slate-700 leading-snug break-words">
                        ${message}
                    </p>
                </div>
                <button type="button" onclick="dismissToast(this.closest('.toast-card'))" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition-colors shrink-0 -mr-1 -mt-1" aria-label="Tutup notifikasi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="h-1 w-full bg-slate-100 overflow-hidden">
                <div class="toast-progress-bar h-full ${cfg.barColor}" style="animation: toastProgressBar ${duration}ms linear forwards;"></div>
            </div>
        `;

        container.appendChild(toastEl);

        // Animasi muncul dengan spring-feel
        requestAnimationFrame(() => {
            toastEl.classList.remove('-translate-y-6', 'opacity-0', 'scale-95');
            toastEl.classList.add('translate-y-0', 'opacity-100', 'scale-100');
        });

        // Auto-dismiss & pause on hover
        let remainingTime = duration;
        let startTime = Date.now();
        let timeoutId = setTimeout(() => dismissToast(toastEl), duration);

        toastEl.addEventListener('mouseenter', () => {
            clearTimeout(timeoutId);
            remainingTime -= (Date.now() - startTime);
        });

        toastEl.addEventListener('mouseleave', () => {
            startTime = Date.now();
            timeoutId = setTimeout(() => dismissToast(toastEl), Math.max(remainingTime, 1500));
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
                    const alreadyRendered = Array.from(document.querySelectorAll('#global-toast-container .toast-card p'))
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
        const sessionToasts = document.querySelectorAll('#global-toast-container .toast-card');
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

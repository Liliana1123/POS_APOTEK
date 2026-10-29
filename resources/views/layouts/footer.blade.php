<!-- Global Reusable Footer -->
<footer class="app-global-footer w-full bg-slate-100 border-t border-slate-200/90 py-5 px-4 text-center mt-auto shrink-0 print:hidden transition-colors">
    <div class="max-w-7xl mx-auto flex flex-col items-center justify-center gap-2">
        <!-- Logo Perusahaan -->
        <div class="flex items-center justify-center">
            @php
                $logoPath = null;
                if (file_exists(public_path('images/logo-kinaryatama.jpg'))) {
                    $logoPath = asset('images/logo-kinaryatama.jpg');
                } elseif (file_exists(public_path('images/company-logo.jpg'))) {
                    $logoPath = asset('images/company-logo.jpg');
                }
            @endphp

            @if ($logoPath)
                <img 
                    src="{{ $logoPath }}" 
                    alt="CV. Kinaryatama Raharja" 
                    class="h-9 sm:h-11 w-auto max-w-[180px] object-contain rounded shadow-2xs hover:opacity-95 transition-opacity"
                    loading="lazy"
                >
            @else
                <div class="h-9 px-3 flex items-center justify-center bg-white rounded border border-slate-200 text-xs font-bold text-slate-700 shadow-2xs">
                    CV. Kinaryatama Raharja
                </div>
            @endif
        </div>

        <!-- Informasi Hak Cipta & Versi -->
        <div class="flex flex-col items-center justify-center space-y-0.5 select-none">
            <p class="text-xs sm:text-[13px] font-semibold text-slate-700 tracking-wide">
                &copy; 2026 CV. Kinaryatama Raharja
            </p>
            <p class="text-[11px] sm:text-xs text-slate-500 font-medium">
                App.Version 26.07.A
            </p>
        </div>
    </div>
</footer>

<style>
    @media print {
        .app-global-footer,
        footer.app-global-footer {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            min-height: 0 !important;
            max-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            position: absolute !important;
            top: -9999px !important;
            left: -9999px !important;
        }
    }
</style>

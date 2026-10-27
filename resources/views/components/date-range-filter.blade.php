@props([
    'action',
    'periode' => 'bulan_ini',
    'dari' => null,
    'sampai' => null,
    'hasReset' => null,
    'resetUrl' => null,
    'queryParams' => [],
    'containerId' => 'customDateContainer',
    'btnId' => 'btnToggleCustom',
    'ajax' => false,
    'ajaxTarget' => '#dashboard-main-container',
])

@php
    $currentPeriode = $periode ?? request('periode', 'bulan_ini');
    $valDari = $dari ?? request('dari');
    $valSampai = $sampai ?? request('sampai');
    $showReset = $hasReset !== null ? $hasReset : (
        (request()->filled('periode') && request('periode') !== 'bulan_ini') ||
        request()->filled('dari') ||
        request()->filled('sampai')
    );
    $finalResetUrl = $resetUrl ?? $action;

    $presets = [
        'hari_ini' => 'Hari Ini',
        'minggu_ini' => 'Minggu Ini',
        'bulan_ini' => 'Bulan Ini',
        'tahun_ini' => 'Tahun Ini',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2.5']) }}
    @if($ajax)
        data-ajax-filter="true"
        data-ajax-target="{{ $ajaxTarget }}"
    @endif
>
    {{-- Tombol Preset Periode --}}
    <div class="inline-flex items-center rounded-lg border border-gray-200 bg-white p-1 shadow-2xs">
        @foreach ($presets as $key => $label)
            <a href="{{ $action }}?{{ http_build_query(array_merge($queryParams, ['periode' => $key])) }}"
                data-role="preset-link"
                data-periode="{{ $key }}"
                class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors {{ $currentPeriode === $key ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                {{ $label }}
            </a>
        @endforeach

        <button type="button" 
            onclick="toggleCustomDate('{{ $containerId }}')" 
            id="{{ $btnId }}"
            class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors {{ $currentPeriode === 'custom' ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
            Custom
        </button>
    </div>

    {{-- Form Rentang Tanggal Custom --}}
    <form method="GET" action="{{ $action }}" id="{{ $containerId }}" data-role="custom-date-form" class="{{ $currentPeriode === 'custom' ? 'flex' : 'hidden' }} items-center gap-1.5 bg-white p-1 rounded-lg border border-gray-200 shadow-2xs">
        <input type="hidden" name="periode" value="custom">
        @foreach ($queryParams as $qKey => $qVal)
            @if(!in_array($qKey, ['periode', 'dari', 'sampai']))
                <input type="hidden" name="{{ $qKey }}" value="{{ $qVal }}">
            @endif
        @endforeach
        <input type="date" name="dari" id="inputDari_{{ $containerId }}" value="{{ $valDari }}" max="{{ date('Y-m-d') }}" required class="text-xs border-gray-200 rounded px-2 py-1 focus:ring-1 focus:ring-blue-500">
        <span class="inline-flex items-center text-gray-400 shrink-0">
            <x-heroicon-m-minus class="w-3 h-3" />
        </span>
        <input type="date" name="sampai" id="inputSampai_{{ $containerId }}" value="{{ $valSampai }}" max="{{ date('Y-m-d') }}" required class="text-xs border-gray-200 rounded px-2 py-1 focus:ring-1 focus:ring-blue-500">
        <button type="submit" class="btn-primary !py-1 !px-2.5 !text-xs">Terapkan</button>
    </form>

    {{-- Tombol Reset --}}
    @if($showReset)
        <a href="{{ $finalResetUrl }}"
           data-role="reset-link"
           class="btn-secondary !py-1 !px-2.5 !text-xs flex items-center gap-1 text-gray-600 hover:text-red-600 hover:border-red-300 transition-colors shadow-2xs"
           title="Reset filter">
            <x-heroicon-o-arrow-path class="w-3.5 h-3.5 text-gray-500" />
            <span>Reset</span>
        </a>
    @endif
</div>

@once
<script>
    if (typeof window.toggleCustomDate !== 'function') {
        window.toggleCustomDate = function (id) {
            var containerId = id || 'customDateContainer';
            var container = document.getElementById(containerId);
            if (!container) return;
            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
                container.classList.add('flex');
                var dariInput = container.querySelector('input[name="dari"]');
                if (dariInput) dariInput.focus();
            } else {
                container.classList.add('hidden');
                container.classList.remove('flex');
            }
        };
    }

    // Validasi & Sinkronisasi Tanggal Dari & Sampai
    document.addEventListener('change', function(e) {
        if (e.target && e.target.name === 'dari') {
            var form = e.target.closest('form');
            if (!form) return;
            var sampai = form.querySelector('input[name="sampai"]');
            if (sampai) {
                sampai.min = e.target.value;
                if (sampai.value && sampai.value < e.target.value) {
                    sampai.value = e.target.value;
                }
            }
        }
    });

    // ==========================================
    // SEAMLESS AJAX LIVE FILTER ENGINE
    // ==========================================
    (function() {
        var abortController = null;

        function getTopProgressBar() {
            var pBar = document.getElementById('global-date-range-top-bar');
            if (!pBar) {
                pBar = document.createElement('div');
                pBar.id = 'global-date-range-top-bar';
                pBar.className = 'table-shimmer-progress !fixed !top-0 !left-0 !right-0 !h-[3px] !z-[99999] transition-opacity duration-200 hidden';
                pBar.innerHTML = '<div class="table-shimmer-progress-bar !w-1/2"></div>';
                document.body.appendChild(pBar);
            }
            return pBar;
        }

        function fetchDateFilterData(targetUrl, targetSelector, pushState) {
            if (abortController) {
                abortController.abort();
            }
            abortController = new AbortController();

            var targetEl = document.querySelector(targetSelector);
            var topBar = getTopProgressBar();
            topBar.classList.remove('hidden');
            topBar.style.opacity = '1';

            if (targetEl) {
                targetEl.style.transition = 'opacity 140ms ease';
                targetEl.style.opacity = '0.6';
                targetEl.style.pointerEvents = 'none';
            }

            fetch(targetUrl, {
                signal: abortController.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.text();
            })
            .then(function(html) {
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');
                var newTargetEl = doc.querySelector(targetSelector);

                if (newTargetEl && targetEl) {
                    targetEl.innerHTML = newTargetEl.innerHTML;
                }

                if (pushState !== false) {
                    window.history.pushState({ dateFilterUrl: targetUrl }, '', targetUrl);
                }

                document.dispatchEvent(new CustomEvent('date-range-filter:updated', { 
                    detail: { url: targetUrl, doc: doc } 
                }));
            })
            .catch(function(err) {
                if (err.name !== 'AbortError') {
                    console.error('Date filter AJAX error:', err);
                    window.location.href = targetUrl;
                }
            })
            .finally(function() {
                topBar.style.opacity = '0';
                setTimeout(function() { topBar.classList.add('hidden'); }, 200);
                if (targetEl) {
                    targetEl.style.opacity = '1';
                    targetEl.style.pointerEvents = '';
                }
            });
        }

        // Intercept Klik Preset Periode & Reset Link
        document.addEventListener('click', function(e) {
            var link = e.target.closest('[data-ajax-filter="true"] [data-role="preset-link"], [data-ajax-filter="true"] [data-role="reset-link"]');
            if (!link || !link.href) return;
            var filterWrapper = link.closest('[data-ajax-filter="true"]');
            if (!filterWrapper) return;

            e.preventDefault();

            // Respons Visual Seketika (0 ms) pada tombol yang diklik
            if (link.dataset.role === 'preset-link') {
                filterWrapper.querySelectorAll('[data-role="preset-link"]').forEach(function(btn) {
                    btn.className = 'px-2.5 py-1 text-xs font-semibold rounded-md transition-colors text-gray-600 hover:text-gray-900 hover:bg-gray-100';
                });
                var customBtn = filterWrapper.querySelector('button[id*="btnToggleCustom"]');
                if (customBtn) {
                    customBtn.className = 'px-2.5 py-1 text-xs font-semibold rounded-md transition-colors text-gray-600 hover:text-gray-900 hover:bg-gray-100';
                }
                link.className = 'px-2.5 py-1 text-xs font-semibold rounded-md transition-colors bg-blue-600 text-white shadow-xs';
            }

            var targetSelector = filterWrapper.dataset.ajaxTarget || '#dashboard-main-container';
            fetchDateFilterData(link.href, targetSelector, true);
        });

        // Intercept Submit Form Tanggal Custom
        document.addEventListener('submit', function(e) {
            var form = e.target.closest('[data-ajax-filter="true"] [data-role="custom-date-form"]');
            if (!form) return;
            var filterWrapper = form.closest('[data-ajax-filter="true"]');
            if (!filterWrapper) return;

            e.preventDefault();

            var dari = form.querySelector('input[name="dari"]');
            var sampai = form.querySelector('input[name="sampai"]');
            if (dari && sampai && dari.value && sampai.value && dari.value > sampai.value) {
                var temp = dari.value;
                dari.value = sampai.value;
                sampai.value = temp;
            }

            var action = form.getAttribute('action') || window.location.pathname;
            var formData = new FormData(form);
            var params = new URLSearchParams();
            for (var pair of formData.entries()) {
                if (pair[0] === '_token' || pair[0] === '_method') continue;
                if (typeof pair[1] === 'string' && pair[1].trim() !== '') {
                    params.append(pair[0], pair[1].trim());
                }
            }
            var targetUrl = action + (params.toString() ? ('?' + params.toString()) : '');
            var targetSelector = filterWrapper.dataset.ajaxTarget || '#dashboard-main-container';
            fetchDateFilterData(targetUrl, targetSelector, true);
        });

        // Browser Back / Forward Handling
        window.addEventListener('popstate', function() {
            var filterWrapper = document.querySelector('[data-ajax-filter="true"]');
            if (!filterWrapper) return;
            var targetSelector = filterWrapper.dataset.ajaxTarget || '#dashboard-main-container';
            fetchDateFilterData(window.location.href, targetSelector, false);
        });
    })();
</script>
@endonce

@props([
'action' => '',
'resetUrl' => null,
'hasReset' => false,
'grid' => false,
'gridCols' => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-4',
'actionCols' => null,
'submitLabel' => null,
'liveSearch' => false,
'tableTarget' => '.table-custom-container',
'paginationTarget' => '.pagination-container',
])

@php
$isFiltered = $hasReset || ($resetUrl && request()->query());
$label = $submitLabel ?? ($grid ? 'Filter' : 'Cari');
@endphp

<div class="card-base p-4 mb-6 no-print filter-card-web">
    <form method="GET"
        action="{{ $action }}"
        @if($liveSearch)
        data-live-filter="true"
        data-reset-url="{{ $resetUrl }}"
        data-table-target="{{ $tableTarget }}"
        data-pagination-target="{{ $paginationTarget }}"
        @endif
        {{ $attributes->merge(['class' => $grid ? 'space-y-0' : 'flex flex-wrap gap-2.5 items-center']) }}>
        @if($grid)
        <div class="grid {{ $gridCols }} gap-3 items-center">
            {{ $slot }}
            <div class="flex items-center gap-2 {{ $actionCols }}">
                <button type="submit" class="btn-primary h-9 px-4 inline-flex items-center justify-center text-xs font-semibold whitespace-nowrap box-border">
                    <span>{{ $label }}</span>
                </button>
                @if($resetUrl && ($isFiltered || $liveSearch))
                <a href="{{ $resetUrl }}"
                    data-role="clear-filter"
                    class="btn-secondary h-9 px-3.5 inline-flex items-center justify-center text-xs font-semibold whitespace-nowrap box-border {{ $isFiltered ? '' : 'hidden' }}"
                    title="Reset seluruh filter">
                    <span>Reset</span>
                </a>
                @endif
            </div>
        </div>
        @else
        {{ $slot }}
        <button type="submit" class="btn-primary h-9 px-4 inline-flex items-center justify-center text-xs font-semibold box-border">
            <span>{{ $label }}</span>
        </button>
        @if($resetUrl && ($hasReset || request()->filled('cari') || $liveSearch))
        <a href="{{ $resetUrl }}"
            data-role="clear-filter"
            class="btn-secondary h-9 px-3.5 inline-flex items-center justify-center text-xs font-semibold box-border {{ ($hasReset || request()->filled('cari')) ? '' : 'hidden' }}"
            title="Reset pencarian">
            <span>Reset</span>
        </a>
        @endif
        @endif
    </form>
</div>

@if($liveSearch)
@once('card-filter-live-search')
<script>
    (function() {
        'use strict';

        var debounceTimer = null;
        var abortController = null;

        function getFormQueryString(form) {
            var formData = new FormData(form);
            var params = new URLSearchParams();
            for (var pair of formData.entries()) {
                var key = pair[0];
                var val = pair[1];
                if (key === '_token' || key === '_method') continue;
                if (typeof val === 'string' && val.trim() !== '') {
                    params.append(key, val.trim());
                }
            }
            return params.toString();
        }

        function updateClearButtonState(form) {
            var clearBtn = form.querySelector('[data-role="clear-filter"]');
            if (!clearBtn) return;
            var hasValue = false;
            var inputs = form.querySelectorAll('input:not([type="hidden"]), select, textarea');
            inputs.forEach(function(input) {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    if (input.checked) hasValue = true;
                } else if (input.value && input.value.trim() !== '') {
                    hasValue = true;
                }
            });
            if (hasValue) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }

        function fetchFilteredData(form, targetUrl, pushState) {
            if (abortController) {
                abortController.abort();
            }
            abortController = new AbortController();

            function isPaginationElement(el) {
                if (!el) return false;
                if (el.matches && el.matches('.pagination-container, [data-pagination-container], nav[aria-label]')) return true;
                if (el.classList && el.classList.contains('pagination-container')) return true;
                return false;
            }

            var tableSel = form.dataset.tableTarget || '.table-custom-container';
            var pagSel = form.dataset.paginationTarget || '.pagination-container';
            var tableContainer = document.querySelector(tableSel);
            var pagContainer = document.querySelector(pagSel);
            if (!pagContainer && tableContainer && isPaginationElement(tableContainer.nextElementSibling)) {
                pagContainer = tableContainer.nextElementSibling;
            }

            if (tableContainer) {
                tableContainer.classList.add('is-filtering');
                tableContainer.style.pointerEvents = 'none';
                if (!tableContainer.querySelector('.table-shimmer-progress')) {
                    var pBar = document.createElement('div');
                    pBar.className = 'table-shimmer-progress';
                    pBar.innerHTML = '<div class="table-shimmer-progress-bar"></div>';
                    tableContainer.insertBefore(pBar, tableContainer.firstChild);
                }
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

                    var newTable = doc.querySelector(tableSel);
                    var newPag = doc.querySelector(pagSel);
                    if (!newPag && newTable && isPaginationElement(newTable.nextElementSibling)) {
                        newPag = newTable.nextElementSibling;
                    }

                    if (newTable && tableContainer) {
                        tableContainer.innerHTML = newTable.innerHTML;
                    }

                    var summarySelectors = ['.laporan-summary-cards', '.summary-container'];
                    summarySelectors.forEach(function(sel) {
                        var oldSum = document.querySelector(sel);
                        var newSum = doc.querySelector(sel);
                        if (oldSum && newSum) {
                            oldSum.innerHTML = newSum.innerHTML;
                        }
                    });

                    // Sinkronisasi alert/error validasi filter
                    var oldAlert = document.querySelector('.filter-alert-container');
                    var newAlert = doc.querySelector('.filter-alert-container');
                    if (oldAlert && newAlert) {
                        oldAlert.innerHTML = newAlert.innerHTML;
                        oldAlert.className = newAlert.className;
                    } else if (oldAlert && !newAlert) {
                        oldAlert.innerHTML = '';
                        oldAlert.classList.add('hidden');
                    }

                    // Jika sedang reset, kembalikan nilai input (misal tanggal awal/akhir ke default bulan berjalan)
                    var newForm = doc.querySelector('form[data-live-filter="true"]');
                    if (newForm && form.dataset.isResetting === 'true') {
                        newForm.querySelectorAll('input:not([type="hidden"]), select, textarea').forEach(function(newEl) {
                            var el = form.querySelector('[name="' + newEl.name + '"]');
                            if (el) {
                                if (newEl.type === 'checkbox' || newEl.type === 'radio') {
                                    el.checked = newEl.checked;
                                } else {
                                    el.value = newEl.value;
                                }
                            }
                        });
                        delete form.dataset.isResetting;
                    }

                    if (pagContainer && isPaginationElement(pagContainer)) {
                        if (newPag && newPag.innerHTML.trim() !== '') {
                            pagContainer.innerHTML = newPag.innerHTML;
                            pagContainer.classList.remove('hidden');
                        } else {
                            pagContainer.innerHTML = '';
                            pagContainer.classList.add('hidden');
                        }
                    } else if (newPag && isPaginationElement(newPag) && newPag.innerHTML.trim() !== '' && tableContainer) {
                        var freshPag = document.createElement('div');
                        freshPag.className = 'mt-4 pagination-container';
                        freshPag.innerHTML = newPag.innerHTML;
                        tableContainer.parentNode.insertBefore(freshPag, tableContainer.nextSibling);
                    }

                    if (pushState !== false) {
                        window.history.replaceState({
                            liveSearchUrl: targetUrl
                        }, '', targetUrl);
                    }

                    // Sinkronisasi link export jika ada di halaman
                    var exportLinks = document.querySelectorAll('a[data-sync-filter="true"], #btn-export-barang, #export-excel-btn');
                    exportLinks.forEach(function(link) {
                        try {
                            var currentSearch = new URL(targetUrl, window.location.origin).search;
                            var linkUrl = new URL(link.href, window.location.origin);
                            linkUrl.search = currentSearch;
                            link.href = linkUrl.pathname + linkUrl.search;
                        } catch (e) {}
                    });

                    document.dispatchEvent(new CustomEvent('live-filter:updated', {
                        detail: {
                            url: targetUrl
                        }
                    }));
                })
                .catch(function(err) {
                    if (err.name !== 'AbortError') {
                        console.error('Live search error:', err);
                    }
                })
                .finally(function() {
                    if (tableContainer) {
                        tableContainer.classList.remove('is-filtering');
                        tableContainer.style.pointerEvents = '';
                    }
                });
        }

        function triggerSearch(form, immediate) {
            clearTimeout(debounceTimer);
            updateClearButtonState(form);

            var action = form.getAttribute('action') || window.location.pathname;
            var qs = getFormQueryString(form);
            var url = action + (qs ? ('?' + qs) : '');

            if (immediate) {
                fetchFilteredData(form, url, true);
            } else {
                debounceTimer = setTimeout(function() {
                    fetchFilteredData(form, url, true);
                }, 300);
            }
        }

        // Input listener (debounced for text/search)
        document.addEventListener('input', function(e) {
            var form = e.target.closest('form[data-live-filter="true"]');
            if (!form) return;
            triggerSearch(form, false);
        });

        // Change listener (immediate for select / checkbox / radio / date)
        document.addEventListener('change', function(e) {
            var form = e.target.closest('form[data-live-filter="true"]');
            if (!form) return;
            if (e.target.tagName === 'SELECT' || e.target.type === 'checkbox' || e.target.type === 'radio' || e.target.type === 'date') {
                triggerSearch(form, true);
            }
        });

        // Form submit listener (prevent full reload, search immediately)
        document.addEventListener('submit', function(e) {
            var form = e.target.closest('form[data-live-filter="true"]');
            if (!form) return;
            e.preventDefault();
            triggerSearch(form, true);
        });

        // Clear button listener
        document.addEventListener('click', function(e) {
            var clearBtn = e.target.closest('form[data-live-filter="true"] [data-role="clear-filter"]');
            if (!clearBtn) return;
            var form = clearBtn.closest('form[data-live-filter="true"]');
            if (!form) return;

            e.preventDefault();
            // Reset all form fields
            form.querySelectorAll('input:not([type="hidden"]), textarea').forEach(function(el) {
                el.value = '';
            });
            form.querySelectorAll('select').forEach(function(el) {
                el.selectedIndex = 0;
            });
            form.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(function(el) {
                el.checked = false;
            });

            updateClearButtonState(form);
            form.dataset.isResetting = 'true';
            var resetUrl = form.dataset.resetUrl || form.getAttribute('action');
            fetchFilteredData(form, resetUrl, true);
        });

        // Pagination link clicks (smooth AJAX paging)
        document.addEventListener('click', function(e) {
            var form = document.querySelector('form[data-live-filter="true"]');
            if (!form) return;

            var pageLink = e.target.closest('.pagination-container a, nav[aria-label="Navigasi halaman"] a');
            if (!pageLink || !pageLink.href) return;

            if (pageLink.origin !== window.location.origin) return;

            e.preventDefault();
            fetchFilteredData(form, pageLink.href, true);
        });

        // Handle back/forward navigation
        window.addEventListener('popstate', function() {
            var form = document.querySelector('form[data-live-filter="true"]');
            if (!form) return;
            fetchFilteredData(form, window.location.href, false);
        });
    })();
</script>
@endonce
@endif
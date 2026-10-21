{{-- Modal Spotlight Global Quick Search (Ctrl + K) --}}
<div id="quick-search-modal"
     class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-sm p-4 sm:p-6 md:p-20 transition-all duration-200"
     role="dialog"
     aria-modal="true"
     aria-labelledby="quick-search-title">

    <div class="mx-auto max-w-2xl transform overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-900/10 transition-all">
        {{-- Search Input Header --}}
        <div class="relative flex items-center border-b border-slate-100 px-4 py-3 sm:px-6">
            <x-heroicon-o-magnifying-glass class="w-5 h-5 text-blue-600 shrink-0 mr-3" />
            <input type="text"
                   id="quick-search-input"
                   class="h-10 w-full border-0 bg-transparent text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-0 text-sm sm:text-base"
                   placeholder="Cari obat, nomor faktur, pelanggan, atau menu... (ketik min. 2 huruf)"
                   autocomplete="off"
                   spellcheck="false">

            <div class="flex items-center gap-2 ml-2 shrink-0">
                <span id="quick-search-spinner" class="hidden">
                    <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                <button type="button"
                        id="quick-search-close"
                        class="rounded-md p-1 text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors"
                        title="Tutup (Esc)">
                    <kbd class="text-[10px] font-mono bg-slate-100 border border-slate-300 px-1.5 py-0.5 rounded text-slate-500 font-bold">ESC</kbd>
                </button>
            </div>
        </div>

        {{-- Results List / Suggestions --}}
        <div id="quick-search-results" class="max-h-96 overflow-y-auto p-3 space-y-4">
            {{-- Default Suggestions when input is empty --}}
            <div id="quick-search-suggestions">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 py-1">Menu Pintas Cepat</p>
                <div class="mt-1 space-y-1">
                    <a href="{{ route('penjualan.create') }}" class="search-item flex items-center justify-between px-3 py-2 rounded-xl hover:bg-blue-50 text-slate-700 hover:text-blue-700 transition-colors group">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                                <x-heroicon-o-calculator class="w-4 h-4" />
                            </span>
                            <div>
                                <span class="text-xs font-bold block">Buka Kasir (POS)</span>
                                <span class="text-[11px] text-slate-400">Buat transaksi penjualan obat baru</span>
                            </div>
                        </div>
                        <x-heroicon-o-arrow-right class="w-4 h-4 text-slate-300 group-hover:text-blue-600 transition-colors" />
                    </a>

                    <a href="{{ route('laporan.stok', ['status_stok' => 'menipis']) }}" class="search-item flex items-center justify-between px-3 py-2 rounded-xl hover:bg-amber-50 text-slate-700 hover:text-amber-800 transition-colors group">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                <x-heroicon-o-archive-box class="w-4 h-4" />
                            </span>
                            <div>
                                <span class="text-xs font-bold block">Periksa Stok Menipis & Habis</span>
                                <span class="text-[11px] text-slate-400">Pantau obat yang perlu segera diorder</span>
                            </div>
                        </div>
                        <x-heroicon-o-arrow-right class="w-4 h-4 text-slate-300 group-hover:text-amber-600 transition-colors" />
                    </a>

                    <a href="{{ route('pelanggan.index') }}" class="search-item flex items-center justify-between px-3 py-2 rounded-xl hover:bg-emerald-50 text-slate-700 hover:text-emerald-800 transition-colors group">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                <x-heroicon-o-users class="w-4 h-4" />
                            </span>
                            <div>
                                <span class="text-xs font-bold block">Data Pelanggan & Member</span>
                                <span class="text-[11px] text-slate-400">Cek status poin dan saldo piutang member</span>
                            </div>
                        </div>
                        <x-heroicon-o-arrow-right class="w-4 h-4 text-slate-300 group-hover:text-emerald-600 transition-colors" />
                    </a>
                </div>
            </div>

            {{-- Dynamic Content Populated by JS --}}
            <div id="quick-search-dynamic" class="hidden space-y-4"></div>

            {{-- Not Found State --}}
            <div id="quick-search-empty" class="hidden py-8 text-center">
                <x-heroicon-o-face-frown class="w-8 h-8 text-slate-300 mx-auto mb-2" />
                <p class="text-xs font-bold text-slate-700">Tidak ada hasil ditemukan</p>
                <p class="text-[11px] text-slate-400 mt-1">Coba kata kunci lain seperti nama obat, merk dagang, atau nomor faktur.</p>
            </div>
        </div>

        {{-- Footer Keyboard Hint --}}
        <div class="flex items-center justify-between bg-slate-50 border-t border-slate-100 px-4 py-2 text-[11px] text-slate-400">
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-1">
                    <kbd class="font-mono bg-white border border-slate-200 px-1 py-0.5 rounded text-slate-500">↑</kbd>
                    <kbd class="font-mono bg-white border border-slate-200 px-1 py-0.5 rounded text-slate-500">↓</kbd>
                    <span>navigasi</span>
                </span>
                <span class="flex items-center gap-1">
                    <kbd class="font-mono bg-white border border-slate-200 px-1.5 py-0.5 rounded text-slate-500">Enter</kbd>
                    <span>buka</span>
                </span>
                <span class="flex items-center gap-1">
                    <kbd class="font-mono bg-white border border-slate-200 px-1.5 py-0.5 rounded text-slate-500">Esc</kbd>
                    <span>tutup</span>
                </span>
            </div>
            <span class="font-medium text-slate-500">Pencarian Global Apotek</span>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('quick-search-modal');
        const input = document.getElementById('quick-search-input');
        const trigger = document.getElementById('global-quick-search-trigger');
        const mobileTrigger = document.getElementById('mobile-quick-search-trigger');
        const closeBtn = document.getElementById('quick-search-close');
        const suggestions = document.getElementById('quick-search-suggestions');
        const dynamicContent = document.getElementById('quick-search-dynamic');
        const emptyState = document.getElementById('quick-search-empty');
        const spinner = document.getElementById('quick-search-spinner');

        let debounceTimer = null;
        let selectedIndex = -1;

        function openModal() {
            if (!modal) return;
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            setTimeout(() => {
                input.focus();
                input.select();
            }, 50);
        }

        function closeModal() {
            if (!modal) return;
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            input.value = '';
            resetView();
        }

        function resetView() {
            suggestions.classList.remove('hidden');
            dynamicContent.classList.add('hidden');
            dynamicContent.innerHTML = '';
            emptyState.classList.add('hidden');
            spinner.classList.add('hidden');
            selectedIndex = -1;
        }

        // Shortcut Ctrl + K or Cmd + K
        window.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                if (modal.classList.contains('hidden')) {
                    openModal();
                } else {
                    closeModal();
                }
            } else if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

        if (trigger) trigger.addEventListener('click', openModal);
        if (mobileTrigger) mobileTrigger.addEventListener('click', openModal);
        if (closeBtn) closeBtn.addEventListener('click', closeModal);

        // Click outside modal dialog to close
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Typing search with debounce
        input.addEventListener('input', function () {
            const query = input.value.trim();
            clearTimeout(debounceTimer);

            if (query.length < 2) {
                resetView();
                return;
            }

            spinner.classList.remove('hidden');

            debounceTimer = setTimeout(() => {
                fetch(`{{ route('quick-search') }}?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    spinner.classList.add('hidden');
                    renderResults(data, query);
                })
                .catch(() => {
                    spinner.classList.add('hidden');
                });
            }, 250);
        });

        function formatRupiah(num) {
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        }

        function renderResults(data, query) {
            suggestions.classList.add('hidden');
            dynamicContent.innerHTML = '';
            selectedIndex = -1;

            const hasMedicines = data.medicines && data.medicines.length > 0;
            const hasSales = data.sales && data.sales.length > 0;
            const hasPages = data.pages && data.pages.length > 0;

            if (!hasMedicines && !hasSales && !hasPages) {
                dynamicContent.classList.add('hidden');
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            dynamicContent.classList.remove('hidden');

            // 1. Medicines
            if (hasMedicines) {
                let html = `<div><p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 py-1">Obat & Produk</p><div class="mt-1 space-y-1">`;
                data.medicines.forEach(m => {
                    let stockBadge = '';
                    if (m.stok === 0) {
                        stockBadge = '<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-700">Habis</span>';
                    } else if (m.stok <= 10) {
                        stockBadge = `<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">${m.stok} unit</span>`;
                    } else {
                        stockBadge = `<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">${m.stok} unit</span>`;
                    }

                    html += `
                        <a href="${m.url}" class="search-item flex items-center justify-between px-3 py-2 rounded-xl hover:bg-blue-50 text-slate-700 hover:text-blue-900 transition-colors group">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg>
                                </span>
                                <div class="min-w-0 truncate">
                                    <span class="text-xs font-bold text-slate-900 group-hover:text-blue-700 block truncate">${m.nama}</span>
                                    <span class="text-[11px] text-slate-400 block">${m.kategori} · ${formatRupiah(m.harga_jual)}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                ${stockBadge}
                                <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                            </div>
                        </a>
                    `;
                });
                html += `</div></div>`;
                dynamicContent.innerHTML += html;
            }

            // 2. Sales / Invoices
            if (hasSales) {
                let html = `<div><p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 py-1">Transaksi & Faktur</p><div class="mt-1 space-y-1">`;
                data.sales.forEach(s => {
                    html += `
                        <a href="${s.url}" class="search-item flex items-center justify-between px-3 py-2 rounded-xl hover:bg-emerald-50 text-slate-700 hover:text-emerald-900 transition-colors group">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                </span>
                                <div class="min-w-0 truncate">
                                    <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-800 block truncate">${s.no_faktur}</span>
                                    <span class="text-[11px] text-slate-400 block">${s.pelanggan} · ${s.tanggal}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-xs font-black text-slate-800">${formatRupiah(s.total)}</span>
                                <svg class="w-4 h-4 text-slate-300 group-hover:text-emerald-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                            </div>
                        </a>
                    `;
                });
                html += `</div></div>`;
                dynamicContent.innerHTML += html;
            }

            // 3. Pages Navigation
            if (hasPages) {
                let html = `<div><p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 py-1">Halaman Menu</p><div class="mt-1 space-y-1">`;
                data.pages.forEach(p => {
                    html += `
                        <a href="${p.url}" class="search-item flex items-center justify-between px-3 py-2 rounded-xl hover:bg-slate-100 text-slate-700 hover:text-slate-900 transition-colors group">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                                </span>
                                <span class="text-xs font-bold text-slate-900">${p.title}</span>
                            </div>
                            <svg class="w-4 h-4 text-slate-300 group-hover:text-slate-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                        </a>
                    `;
                });
                html += `</div></div>`;
                dynamicContent.innerHTML += html;
            }
        }

        // Keyboard arrow navigation inside results
        input.addEventListener('keydown', function (e) {
            const items = modal.querySelectorAll('.search-item');
            if (items.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = (selectedIndex + 1) % items.length;
                updateSelection(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                updateSelection(items);
            } else if (e.key === 'Enter') {
                if (selectedIndex >= 0 && items[selectedIndex]) {
                    e.preventDefault();
                    items[selectedIndex].click();
                }
            }
        });

        function updateSelection(items) {
            items.forEach((item, idx) => {
                if (idx === selectedIndex) {
                    item.classList.add('bg-blue-50', 'ring-1', 'ring-blue-500');
                    item.scrollIntoView({ block: 'nearest' });
                } else {
                    item.classList.remove('bg-blue-50', 'ring-1', 'ring-blue-500');
                }
            });
        }
    });
</script>

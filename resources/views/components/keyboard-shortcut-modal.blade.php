<div id="modal-keyboard-shortcuts" class="modal-backdrop-custom hidden" aria-hidden="true" role="dialog" aria-labelledby="modal-shortcuts-title">
    <div class="modal-container-custom max-w-xl">
        {{-- Header --}}
        <div class="modal-header-custom bg-slate-50/80">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                    <x-heroicon-o-command-line class="w-5 h-5" />
                </div>
                <div>
                    <h2 id="modal-shortcuts-title" class="text-sm font-bold text-slate-900 tracking-tight">Pintasan Keyboard</h2>
                    <p class="text-[11px] text-slate-500">Akses cepat menu dan fungsi sistem untuk mempercepat pekerjaan kasir dan admin.</p>
                </div>
            </div>

            <button type="button" 
                    onclick="closeKeyboardShortcutsModal()" 
                    class="btn-secondary !p-1.5 modal-close-btn" 
                    title="Tutup (Esc)" 
                    aria-label="Tutup">
                <x-heroicon-o-x-mark class="w-4 h-4 pointer-events-none" />
            </button>
        </div>

        {{-- Body --}}
        <div class="modal-body-custom max-h-[70vh] overflow-y-auto space-y-4">
            {{-- Bagian 1: Navigasi Global --}}
            <div>
                <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Navigasi & Pencarian</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-200/70">
                        <span class="text-slate-700">Pencarian Cepat</span>
                        <div class="flex items-center gap-1">
                            <span class="kbd-key">Ctrl</span>
                            <span class="text-slate-400 text-[10px]">+</span>
                            <span class="kbd-key">K</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-200/70">
                        <span class="text-slate-700">Panduan Pintasan</span>
                        <div class="flex items-center gap-1">
                            <span class="kbd-key">?</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-200/70">
                        <span class="text-slate-700">Tutup Dialog / Batal</span>
                        <div class="flex items-center gap-1">
                            <span class="kbd-key">Esc</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-200/70">
                        <span class="text-slate-700">Pin / Lepas Sidebar</span>
                        <div class="flex items-center gap-1">
                            <span class="kbd-key">[</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian 2: Transaksi Kasir (POS) --}}
            <div>
                <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Point of Sale (Kasir)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    <div class="flex items-center justify-between p-2 rounded-lg bg-emerald-50/50 border border-emerald-200/70">
                        <span class="text-slate-700 font-medium">Scan Barcode / Cari Obat</span>
                        <div class="flex items-center gap-1">
                            <span class="kbd-key">F2</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-lg bg-emerald-50/50 border border-emerald-200/70">
                        <span class="text-slate-700 font-medium">Buka Pembayaran</span>
                        <div class="flex items-center gap-1">
                            <span class="kbd-key">F8</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-200/70">
                        <span class="text-slate-700">Pilih Member / Pelanggan</span>
                        <div class="flex items-center gap-1">
                            <span class="kbd-key">F4</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-200/70">
                        <span class="text-slate-700">Pilih Dokter Resep</span>
                        <div class="flex items-center gap-1">
                            <span class="kbd-key">F7</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian 3: Tabel & Data --}}
            <div>
                <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Aksi Cepat Data</h3>
                <div class="flex items-center justify-between p-2 rounded-lg bg-blue-50/50 border border-blue-200/70 text-xs">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-clipboard-document class="w-4 h-4 text-blue-600" />
                        <span class="text-slate-700">Salin No Faktur / Barcode / Kode Obat</span>
                    </div>
                    <span class="text-[11px] font-semibold text-blue-700 bg-white px-2 py-0.5 rounded border border-blue-200 shadow-xs">Klik 1x pada Kode</span>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="modal-footer-custom bg-slate-50/80 justify-between">
            <span class="text-[11px] text-slate-400">Tekan <span class="kbd-key !h-5 !min-w-5 text-[10px]">Esc</span> untuk menutup</span>
            <button type="button" onclick="closeKeyboardShortcutsModal()" class="btn-primary text-xs">
                Mengerti
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    window.openKeyboardShortcutsModal = function() {
        const modal = document.getElementById('modal-keyboard-shortcuts');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    };

    window.closeKeyboardShortcutsModal = function() {
        const modal = document.getElementById('modal-keyboard-shortcuts');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    };

    document.addEventListener('keydown', function(e) {
        // Jangan picu jika sedang mengetik di input, textarea, select, dsb.
        const target = e.target;
        const isEditing = target && (
            target.tagName === 'INPUT' || 
            target.tagName === 'TEXTAREA' || 
            target.tagName === 'SELECT' || 
            target.isContentEditable
        );

        if ((e.key === '?' && !isEditing) || ((e.ctrlKey || e.metaKey) && e.key === '/')) {
            e.preventDefault();
            const modal = document.getElementById('modal-keyboard-shortcuts');
            if (modal && !modal.classList.contains('hidden')) {
                window.closeKeyboardShortcutsModal();
            } else {
                window.openKeyboardShortcutsModal();
            }
        } else if (e.key === 'Escape') {
            const modal = document.getElementById('modal-keyboard-shortcuts');
            if (modal && !modal.classList.contains('hidden')) {
                window.closeKeyboardShortcutsModal();
            }
        }
    });

    // Close on click backdrop
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('modal-keyboard-shortcuts');
        if (modal && e.target === modal) {
            window.closeKeyboardShortcutsModal();
        }
    });
})();
</script>

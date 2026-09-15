<!-- Modal Detail Barang -->
<div id="modal-detail-barang-backdrop" class="fixed inset-0 bg-slate-900/40 z-50 hidden transition-opacity"></div>
<div id="modal-detail-barang" class="fixed inset-0 z-50 hidden flex items-start justify-center pt-[4vh] sm:pt-[6vh] px-4">
    <div class="w-full max-w-2xl bg-white rounded-xl shadow-2xl relative max-h-[90vh] flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 shrink-0 bg-slate-50/70">
            <div>
                <h3 class="text-sm font-bold text-gray-900" id="detail-title-nama">Detail Barang</h3>
                <p class="text-[11px] text-gray-500 font-sans">Informasi lengkap data barang & klasifikasi obat.</p>
            </div>
            <button type="button" id="btn-close-detail-modal" class="text-gray-400 hover:text-gray-600 transition-colors p-1" aria-label="Tutup">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <!-- Body -->
        <div class="px-6 py-5 overflow-y-auto flex-1 space-y-6">
            <!-- 1. IDENTITAS -->
            <div>
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2.5 pb-1 border-b">1. Identitas Barang</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-2.5 bg-gray-50/80 rounded-lg">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Kode Apotek</span>
                        <span class="font-semibold text-gray-800 text-xs mt-0.5 block" id="detail-kode-apotek">—</span>
                    </div>
                    <div class="p-2.5 bg-gray-50/80 rounded-lg">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Kode KFA</span>
                        <span class="font-mono font-semibold text-gray-800 mt-0.5 block" id="detail-kode-kfa">—</span>
                    </div>
                    <div class="p-2.5 bg-gray-50/80 rounded-lg">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Nama Barang</span>
                        <span class="font-semibold text-gray-900 mt-0.5 block" id="detail-nama">—</span>
                    </div>
                    <div class="p-2.5 bg-gray-50/80 rounded-lg">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Merk</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block" id="detail-merk">—</span>
                    </div>
                    <div class="p-2.5 bg-gray-50/80 rounded-lg sm:col-span-2">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Barcode</span>
                        <span class="font-mono text-gray-800 mt-0.5 block" id="detail-barcode">—</span>
                    </div>
                </div>
            </div>

            <!-- 2. DATA MASTER -->
            <div>
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2.5 pb-1 border-b">2. Data Master</h4>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="p-2.5 bg-gray-50/80 rounded-lg">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Kategori</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block" id="detail-kategori">—</span>
                    </div>
                    <div class="p-2.5 bg-gray-50/80 rounded-lg">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Satuan</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block" id="detail-satuan">—</span>
                    </div>
                    <div class="p-2.5 bg-gray-50/80 rounded-lg">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Pabrik</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block" id="detail-pabrik">—</span>
                    </div>
                    <div class="p-2.5 bg-gray-50/80 rounded-lg">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Supplier</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block" id="detail-supplier">—</span>
                    </div>
                </div>
            </div>

            <!-- 3. STOK & 4. LAINNYA -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2.5 pb-1 border-b">3. Inventori & Stok</h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between items-center p-2.5 bg-gray-50/80 rounded-lg">
                            <span class="text-gray-500 font-medium">Stok Saat Ini:</span>
                            <span class="font-bold text-gray-900 text-sm" id="detail-stok">0</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-gray-50/80 rounded-lg">
                            <span class="text-gray-500 font-medium">Stok Minimum:</span>
                            <span class="font-semibold text-gray-800" id="detail-stok-minimum">0</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-gray-50/80 rounded-lg">
                            <span class="text-gray-500 font-medium">Status Stok:</span>
                            <span id="detail-status-stok-badge" class="badge-success">Aman</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2.5 pb-1 border-b">4. Ketentuan & Status</h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between items-center p-2.5 bg-gray-50/80 rounded-lg">
                            <span class="text-gray-500 font-medium">Butuh Resep:</span>
                            <span class="font-semibold text-gray-800" id="detail-butuh-resep">Bebas</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-gray-50/80 rounded-lg">
                            <span class="text-gray-500 font-medium">Status Aktif:</span>
                            <span class="font-semibold text-gray-800" id="detail-status-aktif">Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between gap-2 px-6 py-3.5 border-t border-gray-100 shrink-0 bg-slate-50/70">
            <button type="button" id="btn-edit-from-detail" class="btn-primary py-1.5 px-4 flex items-center gap-1.5 text-xs">
                <x-heroicon-o-pencil-square class="w-4 h-4" />
                <span>Edit Barang</span>
            </button>
            <button type="button" id="btn-cancel-detail-modal" class="btn-secondary py-1.5 px-4 text-xs">
                Tutup
            </button>
        </div>
    </div>
</div>

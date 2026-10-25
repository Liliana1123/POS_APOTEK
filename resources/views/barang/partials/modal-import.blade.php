<!-- Modal Import Barang -->
<div id="modal-import-barang-backdrop" class="fixed inset-0 bg-slate-900/40 z-50 hidden transition-opacity"></div>
<div id="modal-import-barang" class="fixed inset-0 z-50 hidden flex items-start justify-center pt-[5vh] sm:pt-[10vh] px-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-2xl relative max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 shrink-0">
            <h3 class="text-sm font-bold text-gray-800">Import Data Master Barang</h3>
            <button type="button" id="btn-close-import-modal" class="text-gray-400 hover:text-gray-600 transition-colors p-1" aria-label="Tutup">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>
        
        <form action="{{ route('barang.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden" id="form-import-barang">
            @csrf
            <div class="px-5 py-4 overflow-y-auto flex-1 space-y-4">
                <!-- Info & Download Template Card -->
                <div class="p-3 bg-blue-50/70 border border-blue-100 rounded-lg text-xs space-y-2">
                    <div class="flex items-start gap-2 text-blue-800 font-medium">
                        <x-heroicon-o-information-circle class="w-4 h-4 shrink-0 mt-0.5 text-blue-600" />
                        <span>Format file yang didukung adalah <strong>CSV (.csv)</strong>.</span>
                    </div>
                    <p class="text-blue-600 text-[11px] leading-relaxed">
                        Pastikan kolom pada file CSV mencakup: <code class="bg-blue-100/80 px-1 py-0.5 rounded text-[10px] font-mono text-blue-900">nama, kode_kfa, merk, kategori, satuan, pabrik, barcode, stok_minimum, butuh_resep</code>.
                    </p>
                    <div class="pt-1">
                        <a href="{{ route('barang.import-template') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 hover:text-blue-900 underline">
                            <x-heroicon-o-arrow-down-tray class="w-3.5 h-3.5" />
                            <span>Unduh Template CSV</span>
                        </a>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Pilih File CSV <span class="text-red-500">*</span></label>
                    <div class="dropzone-modern p-5 text-center cursor-pointer group" onclick="document.getElementById('input-file-import-csv').click()">
                        <input type="file" id="input-file-import-csv" name="file" accept=".csv,text/csv,text/plain" required class="sr-only">
                        <div class="dropzone-default-content flex flex-col items-center justify-center space-y-1.5 transition-opacity">
                            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                            </div>
                            <div class="text-xs font-semibold text-slate-700">
                                <span class="text-blue-600 underline">Klik untuk unggah</span> atau seret file ke sini
                            </div>
                            <p class="text-[11px] text-slate-400">Format .csv (Maks. 10 MB)</p>
                        </div>
                        <div class="dropzone-preview hidden mt-2 text-xs font-bold text-emerald-700 bg-emerald-50 py-1 px-2.5 rounded-lg border border-emerald-200 inline-block"></div>
                    </div>
                </div>

                <div class="pt-1">
                    <label class="flex items-start text-xs font-medium text-gray-700 cursor-pointer">
                        <input type="checkbox" name="auto_create_master" value="1" checked class="mr-2.5 mt-0.5 rounded border-gray-300 focus:ring-blue-500 w-4 h-4 text-blue-600">
                        <div>
                            <span class="font-semibold text-gray-800">Otomatis daftarkan master baru</span>
                            <p class="text-[10px] text-gray-400 mt-0.5">Jika nama kategori, satuan, atau pabrik belum ada di sistem, buat otomatis.</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 px-5 py-3 border-t border-gray-100 shrink-0 bg-gray-50/50">
                <button type="button" id="btn-cancel-import-modal" class="btn-secondary py-1.5 px-4">Batal</button>
                <button type="submit" id="btn-submit-import" class="btn-primary py-1.5 px-5 flex items-center gap-2">
                    <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                    <span>Upload & Import</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Import Data Obat dari Excel/CSV ke Form Penerimaan -->
<div id="modal-import-obat-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[70] hidden transition-opacity duration-200"></div>

<div id="modal-import-obat" class="fixed inset-0 z-[70] hidden flex items-center justify-center p-3 sm:p-5 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="modal-import-title">
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl flex flex-col max-h-[92vh] overflow-hidden border border-gray-150 transition-all">

        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0 bg-slate-50/70">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 text-emerald-600" />
                </div>
                <div>
                    <h2 id="modal-import-title" class="text-base font-bold text-gray-800">Import Data Obat ke Faktur Penerimaan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Unggah file Excel atau CSV dari supplier untuk mengisi tabel Detail Barang Diterima secara instan.</p>
                </div>
            </div>
            <button type="button" id="btn-close-import-obat" class="btn-secondary !p-1.5 rounded-lg text-gray-400 hover:text-gray-600" aria-label="Tutup Modal" title="Tutup">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <!-- Stepper Progress Bar -->
        <div class="px-6 py-3 bg-gray-50/50 border-b border-gray-100 shrink-0">
            <div class="flex items-center justify-between max-w-xl mx-auto">
                <!-- Step 1 Indicator -->
                <div class="flex items-center gap-2" id="indicator-step-1">
                    <span class="step-badge w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold bg-blue-600 text-white">1</span>
                    <span class="step-label text-xs font-semibold text-blue-700">Unggah File</span>
                </div>
                <div class="flex-1 h-0.5 mx-3 bg-gray-200" id="line-step-1-2"></div>
                <!-- Step 2 Indicator -->
                <div class="flex items-center gap-2" id="indicator-step-2">
                    <span class="step-badge w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold bg-gray-200 text-gray-500">2</span>
                    <span class="step-label text-xs font-medium text-gray-400">Pemetaan Kolom</span>
                </div>
                <div class="flex-1 h-0.5 mx-3 bg-gray-200" id="line-step-2-3"></div>
                <!-- Step 3 Indicator -->
                <div class="flex items-center gap-2" id="indicator-step-3">
                    <span class="step-badge w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold bg-gray-200 text-gray-500">3</span>
                    <span class="step-label text-xs font-medium text-gray-400">Validasi & Pratinjau</span>
                </div>
            </div>
        </div>

        <!-- Modal Body Container (Multi-step) -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6">

            <!-- ========================================== -->
            <!-- STEP 1: UPLOAD FILE & TEMPLATE             -->
            <!-- ========================================== -->
            <div id="import-step-1" class="space-y-5">
                <!-- Info & Download Template Box -->
                <div class="p-4 bg-emerald-50/80 border border-emerald-200 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 text-emerald-900 font-semibold text-xs">
                            <x-heroicon-o-information-circle class="w-4 h-4 text-emerald-600 shrink-0" />
                            <span>Gunakan template resmi untuk format yang paling sesuai</span>
                        </div>
                        <p class="text-xs text-emerald-700 leading-relaxed">
                            Mendukung file spreadsheet <strong>.xlsx</strong>, <strong>.xls</strong>, dan <strong>.csv</strong>. Maksimal 10MB.
                        </p>
                    </div>
                    <a href="{{ route('penerimaan.import-template') }}" class="btn-secondary !bg-white !text-emerald-700 !border-emerald-300 hover:!bg-emerald-50 text-xs font-semibold flex items-center gap-2 shrink-0 py-2 px-3.5 shadow-2xs">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4 text-emerald-600" />
                        <span>Unduh Template Excel</span>
                    </a>
                </div>

                <!-- Dropzone Area -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Pilih File Spreadsheet / CSV <span class="text-red-500">*</span></label>
                    <div id="dropzone-import-obat" class="dropzone-modern p-8 text-center cursor-pointer group flex flex-col items-center justify-center min-h-[180px]">
                        <input type="file" id="input-file-import-obat" accept=".xlsx,.xls,.csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv" class="sr-only">
                        
                        <div id="dropzone-prompt" class="dropzone-default-content flex flex-col items-center justify-center space-y-2">
                            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <x-heroicon-o-document-arrow-up class="w-6 h-6" />
                            </div>
                            <div class="text-sm font-semibold text-gray-700">
                                <span class="text-emerald-700 underline font-bold">Klik untuk memilih file</span> atau seret file ke sini
                            </div>
                            <p class="text-xs text-gray-400">Format yang didukung: XLSX, XLS, CSV (Maksimal 10MB)</p>
                        </div>

                        <!-- File Selected Indicator -->
                        <div id="dropzone-selected-file" class="hidden flex flex-col items-center justify-center space-y-2">
                            <div class="w-12 h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center shadow-md">
                                <x-heroicon-o-check-circle class="w-6 h-6" />
                            </div>
                            <div class="text-center">
                                <p id="selected-file-name" class="text-sm font-bold text-gray-800 break-all"></p>
                                <p id="selected-file-size" class="text-xs text-gray-500 mt-0.5"></p>
                            </div>
                            <button type="button" id="btn-ganti-file-import" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 underline mt-1">
                                Ganti file lain
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 1 Error Alert -->
                <div id="import-step-1-error" class="hidden p-3.5 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs flex items-start gap-2.5">
                    <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-red-500 shrink-0 mt-0.5" />
                    <span id="import-step-1-error-text" class="leading-relaxed"></span>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- STEP 2: MAPPING KOLOM                      -->
            <!-- ========================================== -->
            <div id="import-step-2" class="hidden space-y-5">
                <div class="p-3.5 bg-blue-50/70 border border-blue-100 rounded-xl text-xs text-blue-800 flex items-start gap-2.5">
                    <x-heroicon-o-information-circle class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" />
                    <div class="leading-relaxed">
                        Sistem telah mendeteksi kolom secara otomatis berdasarkan kemiripan nama. Periksa kembali dan pastikan kolom target sesuai dengan file Excel/CSV Anda.
                    </div>
                </div>

                <div class="border border-gray-200 rounded-xl overflow-hidden shadow-2xs">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="px-4 py-3 w-1/3">Kolom Sistem POS</th>
                                <th class="px-4 py-3 w-1/4">Status</th>
                                <th class="px-4 py-3 w-5/12">Kolom dari File Anda</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 text-gray-700">
                            <!-- Nama Barang -->
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-3">
                                    <span class="font-bold text-gray-800">Nama Barang</span>
                                    <p class="text-[11px] text-gray-400">Pencocokan ke master obat</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Wajib / Barcode</span>
                                </td>
                                <td class="px-4 py-3">
                                    <select id="map-nama-barang" class="form-input text-xs mapping-select w-full"></select>
                                </td>
                            </tr>
                            <!-- Barcode -->
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-3">
                                    <span class="font-bold text-gray-800">Barcode</span>
                                    <p class="text-[11px] text-gray-400">Kode barcode / KFA obat</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600">Opsional</span>
                                </td>
                                <td class="px-4 py-3">
                                    <select id="map-barcode" class="form-input text-xs mapping-select w-full"></select>
                                </td>
                            </tr>
                            <!-- No. Batch -->
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-3">
                                    <span class="font-bold text-gray-800">No. Batch</span>
                                    <p class="text-[11px] text-gray-400">Nomor batch pabrik</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800">Wajib</span>
                                </td>
                                <td class="px-4 py-3">
                                    <select id="map-no-batch" class="form-input text-xs mapping-select w-full"></select>
                                </td>
                            </tr>
                            <!-- Expired Date -->
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-3">
                                    <span class="font-bold text-gray-800">Expired Date</span>
                                    <p class="text-[11px] text-gray-400">Tanggal kadaluarsa</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800">Wajib</span>
                                </td>
                                <td class="px-4 py-3">
                                    <select id="map-expired-date" class="form-input text-xs mapping-select w-full"></select>
                                </td>
                            </tr>
                            <!-- Harga Beli -->
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-3">
                                    <span class="font-bold text-gray-800">Harga Beli</span>
                                    <p class="text-[11px] text-gray-400">Harga per unit beli</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800">Wajib</span>
                                </td>
                                <td class="px-4 py-3">
                                    <select id="map-harga-beli" class="form-input text-xs mapping-select w-full"></select>
                                </td>
                            </tr>
                            <!-- Harga Jual -->
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-3">
                                    <span class="font-bold text-gray-800">Harga Jual</span>
                                    <p class="text-[11px] text-gray-400">Harga jual ke pelanggan</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600">Opsional</span>
                                </td>
                                <td class="px-4 py-3">
                                    <select id="map-harga-jual" class="form-input text-xs mapping-select w-full"></select>
                                </td>
                            </tr>
                            <!-- Jumlah Dipesan -->
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-3">
                                    <span class="font-bold text-gray-800">Jumlah Dipesan</span>
                                    <p class="text-[11px] text-gray-400">Kuantitas order faktur</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800">Wajib</span>
                                </td>
                                <td class="px-4 py-3">
                                    <select id="map-jumlah-dipesan" class="form-input text-xs mapping-select w-full"></select>
                                </td>
                            </tr>
                            <!-- Jumlah Diterima -->
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-3">
                                    <span class="font-bold text-gray-800">Jumlah Diterima</span>
                                    <p class="text-[11px] text-gray-400">Kuantitas fisik diterima</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800">Wajib</span>
                                </td>
                                <td class="px-4 py-3">
                                    <select id="map-jumlah-diterima" class="form-input text-xs mapping-select w-full"></select>
                                </td>
                            </tr>
                            <!-- No. Rak -->
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-4 py-3">
                                    <span class="font-bold text-gray-800">No. Rak</span>
                                    <p class="text-[11px] text-gray-400">Lokasi penyimpanan rak</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600">Opsional (default A-01)</span>
                                </td>
                                <td class="px-4 py-3">
                                    <select id="map-no-rak" class="form-input text-xs mapping-select w-full"></select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Step 2 Error Alert -->
                <div id="import-step-2-error" class="hidden p-3.5 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs flex items-start gap-2.5">
                    <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-red-500 shrink-0 mt-0.5" />
                    <span id="import-step-2-error-text" class="leading-relaxed"></span>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- STEP 3: PREVIEW & VALIDASI                 -->
            <!-- ========================================== -->
            <div id="import-step-3" class="hidden space-y-5">
                <!-- Summary Counters Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-center">
                        <span class="block text-xl font-bold text-gray-800 font-mono" id="summary-total-rows">0</span>
                        <span class="text-[11px] text-gray-500 font-medium">Total Baris File</span>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-center">
                        <span class="block text-xl font-bold text-emerald-700 font-mono" id="summary-valid-rows">0</span>
                        <span class="text-[11px] text-emerald-700 font-semibold">Siap Diimpor</span>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-center">
                        <span class="block text-xl font-bold text-amber-700 font-mono" id="summary-warning-rows">0</span>
                        <span class="text-[11px] text-amber-700 font-semibold">Perlu Dilengkapi</span>
                    </div>
                    <div class="p-3 bg-red-50 rounded-xl border border-red-200 text-center">
                        <span class="block text-xl font-bold text-red-700 font-mono" id="summary-invalid-rows">0</span>
                        <span class="text-[11px] text-red-700 font-semibold">Tidak Valid / Gagal</span>
                    </div>
                </div>

                <!-- Preview Filter & Options Toolbar -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs bg-gray-50/70 p-3 rounded-xl border border-gray-150">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-gray-600">Filter Tampilan:</span>
                        <div class="inline-flex rounded-lg border border-gray-200 bg-white p-0.5" role="group">
                            <button type="button" class="preview-filter-btn px-2.5 py-1 text-xs font-semibold rounded-md transition-colors bg-blue-600 text-white" data-filter="all">Semua</button>
                            <button type="button" class="preview-filter-btn px-2.5 py-1 text-xs font-medium text-gray-600 hover:text-gray-900 rounded-md transition-colors" data-filter="ready">Hanya Siap</button>
                            <button type="button" class="preview-filter-btn px-2.5 py-1 text-xs font-medium text-gray-600 hover:text-gray-900 rounded-md transition-colors" data-filter="issues">Bermasalah</button>
                        </div>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer font-medium text-gray-700">
                        <input type="checkbox" id="chk-only-ready-import" checked class="w-4 h-4 rounded text-emerald-600 border-gray-300 focus:ring-emerald-500">
                        <span>Hanya masukkan baris yang siap / valid ke form</span>
                    </label>
                </div>

                <!-- Preview Table Container -->
                <div class="border border-gray-200 rounded-xl overflow-hidden shadow-2xs">
                    <div class="overflow-x-auto max-h-[340px] overflow-y-auto">
                        <table class="w-full text-xs text-left min-w-[750px]">
                            <thead class="bg-gray-100 border-b border-gray-200 text-gray-600 font-bold sticky top-0 uppercase tracking-wider text-[10px] z-10">
                                <tr>
                                    <th class="px-3 py-2.5 text-center w-10">No</th>
                                    <th class="px-3 py-2.5 text-center w-24">Status</th>
                                    <th class="px-3 py-2.5">Nama Obat Master</th>
                                    <th class="px-3 py-2.5 text-center">Batch</th>
                                    <th class="px-3 py-2.5 text-center">Expired</th>
                                    <th class="px-3 py-2.5 text-right">Harga Beli</th>
                                    <th class="px-3 py-2.5 text-right">Harga Jual</th>
                                    <th class="px-3 py-2.5 text-center">Diterima</th>
                                    <th class="px-3 py-2.5 text-center">Satuan</th>
                                    <th class="px-3 py-2.5">Keterangan / Validasi</th>
                                </tr>
                            </thead>
                            <tbody id="import-preview-rows" class="divide-y divide-gray-150 text-gray-700"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Safety Note Notice -->
                <div class="p-3 bg-amber-50/80 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-start gap-2">
                    <x-heroicon-o-information-circle class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                    <p class="leading-relaxed">
                        <strong>Catatan Keamanan:</strong> Menekan tombol <strong>"Masukkan ke Form"</strong> tidak langsung menyimpan transaksi ataupun mengubah stok inventaris di database. Seluruh data obat akan dimasukkan ke tabel penerimaan pada form agar dapat Anda tinjau dan edit terlebih dahulu.
                    </p>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3.5 border-t border-gray-100 bg-gray-50 flex items-center justify-between shrink-0">
            <!-- Left action -->
            <div>
                <button type="button" id="btn-import-prev" class="hidden btn-secondary text-xs flex items-center gap-1.5 py-1.5 px-3">
                    <x-heroicon-o-arrow-left class="w-3.5 h-3.5" />
                    <span id="btn-import-prev-label">Kembali</span>
                </button>
            </div>

            <!-- Right action -->
            <div class="flex items-center gap-2">
                <button type="button" id="btn-import-cancel" class="btn-secondary text-xs py-1.5 px-3.5">
                    Batal
                </button>
                <button type="button" id="btn-import-next" class="btn-primary text-xs flex items-center gap-1.5 py-1.5 px-4">
                    <span id="btn-import-next-label">Lanjutkan ke Pemetaan</span>
                    <x-heroicon-o-arrow-right class="w-3.5 h-3.5" id="btn-import-next-icon" />
                    <svg id="btn-import-spinner" class="hidden animate-spin w-3.5 h-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>

    </div>
</div>


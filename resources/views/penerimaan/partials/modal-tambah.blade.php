<!-- Modal Tambah Faktur Penerimaan Baru -->
<x-modal
    id="modal-tambah-penerimaan"
    title="Faktur Penerimaan Barang Baru"
    subtitle="Catat faktur masuk obat dari supplier beserta detail expired date batch."
    close-id="btn-tutup-tambah"
    style="max-height: calc(100vh - 60px);">

    <form action="{{ route('penerimaan.store') }}" method="POST" id="form-tambah-penerimaan" class="space-y-6">
        @csrf

        <!-- Form Header Card -->
        <div class="card-base p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-start">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">No. Faktur <span class="text-red-500 font-bold">*</span></label>
                <input type="text" name="no_faktur" id="no_faktur_tambah" value="{{ old('_method') ? '' : old('no_faktur') }}" required
                    class="form-input font-mono font-semibold" placeholder="Nomor faktur masuk...">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">
                    Tanggal Faktur <span class="text-red-500 font-bold">*</span>
                </label>
                <input
                    type="date"
                    name="tanggal_faktur"
                    id="tanggal_faktur_tambah"
                    value="{{ old('_method') ? now()->format('Y-m-d') : old('tanggal_faktur', now()->format('Y-m-d')) }}"
                    required
                    class="form-input"
                >
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Tanggal Terima <span class="text-red-500 font-bold">*</span></label>
                <input type="date" name="tanggal" id="tanggal_tambah" value="{{ old('_method') ? now()->format('Y-m-d') : old('tanggal', now()->format('Y-m-d')) }}" required
                    class="form-input">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Supplier <span class="text-red-500 font-bold">*</span></label>
                <select name="supplier_id" id="supplier_id_tambah" required class="form-input">
                    <option value="">Pilih Supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" data-telepon="{{ $supplier->telepon }}" @selected((!old('_method') && old('supplier_id') == $supplier->id))>{{ $supplier->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">No. Telepon Supplier</label>
                <input type="text" id="telepon_supplier_tambah" class="form-input bg-gray-50" readonly placeholder="Otomatis dari master supplier">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Keterangan</label>
                <input type="text" name="keterangan" id="keterangan_tambah" value="{{ old('_method') ? '' : old('keterangan') }}" class="form-input" placeholder="Keterangan penerimaan (opsional)">
            </div>
        </div>

        <!-- Details Card -->
        <div class="card-base p-6">
            <div class="flex justify-between items-center mb-4 pb-2 border-b">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">Detail Barang Diterima</h3>
                <button type="button" id="btn-tambah-item-tambah" class="btn-secondary py-1 px-3 text-xs font-semibold flex items-center gap-1.5 cursor-pointer">
                    <x-heroicon-o-plus class="w-3.5 h-3.5 shrink-0" />
                    <span>Tambah Item Barang</span>
                </button>
            </div>

            <div class="table-custom-container">
                <div class="overflow-x-auto overflow-y-auto max-h-[430px]">
                    <table class="penerimaan-detail-table mb-2">
                        <thead class="table-custom-header">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-left">Barang <span class="text-red-500 font-bold">*</span></th>
                                <th scope="col" class="px-3 py-2 text-center">Barcode</th>
                                <th scope="col" class="px-3 py-2 text-center">No. Batch <span class="text-red-500 font-bold">*</span></th>
                                <th scope="col" class="px-3 py-2 text-center">Expired Date <span class="text-red-500 font-bold">*</span></th>
                                <th scope="col" class="px-3 py-2 text-right">Harga Beli <span class="text-red-500 font-bold">*</span></th>
                                <th scope="col" class="px-3 py-2 text-right">Harga Jual <span class="text-red-500 font-bold">*</span></th>
                                <th scope="col" class="px-3 py-2 text-center">No. Rak <span class="text-red-500 font-bold">*</span></th>
                                <th scope="col" class="px-3 py-2 text-right">Jumlah Dipesan <span class="text-red-500 font-bold">*</span></th>
                                <th scope="col" class="px-3 py-2 text-right">Jumlah Diterima <span class="text-red-500 font-bold">*</span></th>
                                <th scope="col" class="px-3 py-2 text-center">Satuan</th>
                                <th scope="col" class="px-3 py-2 text-right">Subtotal</th>
                                <th scope="col" class="px-3 py-2 text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="item-rows-tambah" class="table-custom-body divide-y divide-gray-150"></tbody>
                    </table>
                </div>
            </div>

            <p class="text-xs text-gray-400 text-center py-4" id="empty-hint-tambah">Belum ada baris. Klik "+ Tambah Item Barang" untuk mulai input.</p>
            <div class="mt-4 flex flex-col sm:flex-row justify-end gap-4 text-sm font-semibold">
                <span>Total Belanja:</span>
                <span id="total-faktur-tambah" class="text-blue-700 font-mono">Rp 0</span>
            </div>
            <div class="mt-2 flex flex-col sm:flex-row justify-end gap-4 text-sm font-semibold items-center">
                <label for="ppn_tambah">PPN (11%)</label>
                <input type="text" id="ppn_tambah" value="Rp 0" readonly class="form-input w-full sm:w-40 text-right font-mono bg-gray-50">
            </div>

            <div class="mt-2 flex flex-col sm:flex-row justify-end gap-4 text-sm font-semibold">
                <span>Total Tagihan:</span>
                <span id="total-tagihan-tambah" class="text-blue-700 font-mono">Rp 0</span>
            </div>
        </div>

        <div class="card-base p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Pembayaran Saat Penerimaan</label>
                <input type="number" name="pembayaran_pertama" id="pembayaran_pertama_tambah" value="{{ old('_method') ? 0 : old('pembayaran_pertama', 0) }}" min="0" step="0.01" class="form-input text-right font-mono" data-terbilang="true">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Jatuh Tempo</label>
                <input type="date" name="jatuh_tempo" id="jatuh_tempo_tambah" value="{{ old('_method') ? '' : old('jatuh_tempo') }}" class="form-input">
            </div>
            <div class="text-xs text-gray-500">Pembayaran pertama dicatat sebagai histori dan tidak menimpa pembayaran sebelumnya.</div>
        </div>

        <div class="flex justify-end items-center gap-2.5 pt-4">
            <button type="button" id="btn-batal-tambah" class="btn-secondary">Batal</button>
            <button type="submit" id="btn-simpan-tambah" class="btn-primary">Simpan Faktur</button>
        </div>
    </form>
</x-modal>

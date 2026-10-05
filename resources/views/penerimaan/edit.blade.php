<form
    action="{{ route('penerimaan.update', $penerimaan) }}"
    method="POST"
    id="form-edit-penerimaan"
    class="space-y-6"
>
    @csrf
    @method('PUT')

    <!-- Form Header Card -->
    <div class="card-base p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-start">

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">
                No. Faktur <span class="text-red-500 font-bold">*</span>
            </label>

            <input
                type="text"
                name="no_faktur"
                value="{{ old('no_faktur', $penerimaan->no_faktur) }}"
                required
                class="form-input font-mono font-semibold"
                placeholder="Nomor faktur masuk..."
            >
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">
                Tanggal Faktur <span class="text-red-500 font-bold">*</span>
            </label>

            <input
                type="date"
                name="tanggal_faktur"
                value="{{ old('tanggal_faktur', $penerimaan->tanggal_faktur?->format('Y-m-d')) }}"
                required
                class="form-input"
            >
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">
                Tanggal Terima <span class="text-red-500 font-bold">*</span>
            </label>

            <input
                type="date"
                name="tanggal"
                id="tanggal_edit"
                value="{{ old('tanggal', $penerimaan->tanggal?->format('Y-m-d')) }}"
                required
                class="form-input"
            >
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">
                Supplier <span class="text-red-500 font-bold">*</span>
            </label>

            <select
                name="supplier_id"
                id="supplier_id_edit"
                required
                class="form-input"
            >
                <option value="">Pilih Supplier</option>

                @foreach ($suppliers as $supplier)
                    <option
                        value="{{ $supplier->id }}"
                        data-telepon="{{ $supplier->telepon }}"
                        @selected(old('supplier_id', $penerimaan->supplier_id) == $supplier->id)
                    >
                        {{ $supplier->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">
                No. Telepon Supplier
            </label>

            <input
                type="text"
                id="telepon_supplier_edit"
                value="{{ $penerimaan->telepon_supplier }}"
                class="form-input bg-gray-50"
                readonly
                placeholder="Otomatis dari master supplier"
            >
        </div>

        <div class="sm:col-span-3">
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">
                Keterangan
            </label>

            <input
                type="text"
                name="keterangan"
                value="{{ old('keterangan', $penerimaan->keterangan) }}"
                class="form-input"
                placeholder="Keterangan penerimaan (opsional)"
            >
        </div>

    </div>

    <!-- Details Card -->
    <div class="card-base p-6">

        <div class="flex justify-between items-center mb-4 pb-2 border-b">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">
                Detail Barang Diterima
            </h3>

            <button
                type="button"
                id="btn-tambah-item-edit"
                class="btn-secondary py-1 px-3 text-xs font-semibold"
            >
                + Tambah Item Barang
            </button>
        </div>

        <div class="table-custom-container">
            <div class="overflow-x-auto overflow-y-auto max-h-[430px]">

                <table class="penerimaan-detail-table mb-2">
                    <thead class="table-custom-header">
                        <tr>
                            <th scope="col" class="px-3 py-2 text-left">
                                Barang <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 text-center">
                                Barcode
                            </th>

                            <th scope="col" class="px-3 py-2 text-center">
                                No. Batch <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 text-center">
                                Expired Date <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 text-right">
                                Harga Beli <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 text-right">
                                Harga Jual <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 text-center">
                                No. Rak <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 text-right">
                                Jumlah Dipesan <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 text-right">
                                Jumlah Diterima <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 text-center">
                                Satuan
                            </th>

                            <th scope="col" class="px-3 py-2 text-right">
                                Subtotal
                            </th>

                            <th scope="col" class="px-3 py-2 text-center"></th>
                        </tr>
                    </thead>

                    <tbody id="item-rows-edit" class="table-custom-body divide-y divide-gray-150">
                        @php
                            $batchCountPerBarang = [];
                            $batchIndexPerBarang = [];
                            foreach ($penerimaan->detail as $d) {
                                $batchCountPerBarang[$d->barang_id] = ($batchCountPerBarang[$d->barang_id] ?? 0) + 1;
                            }
                        @endphp

                        @foreach ($penerimaan->detail as $index => $item)
                            @php
                                $bId = $item->barang_id;
                                $batchIndexPerBarang[$bId] = ($batchIndexPerBarang[$bId] ?? 0) + 1;
                                $isPrimaryBatch = ($batchIndexPerBarang[$bId] === 1);
                                $dp = $penerimaan->detailPesanan->firstWhere('barang_id', $bId);

                                $isSold = $item->detailPenjualan->isNotEmpty();
                                $isDamaged = $item->rusak->isNotEmpty();
                                $isLocked = $isSold || $isDamaged;

                                $lockReason = '';
                                if ($isSold) {
                                    $lockReason = 'Terkunci: Obat ini sudah tercatat di transaksi kasir';
                                } elseif ($isDamaged) {
                                    $lockReason = 'Terkunci: Obat ini sudah tercatat di data barang rusak';
                                }
                            @endphp

                            <tr class="item-row hover:bg-gray-50 transition-colors">
                                {{-- Hidden Detail ID to recognize existing row in update() --}}
                                <input
                                    type="hidden"
                                    name="items[{{ $index }}][detail_id]"
                                    value="{{ $item->id }}"
                                >

                                <td class="px-3 py-2">
                                    <select
                                        name="items[{{ $index }}][barang_id]"
                                        required
                                        class="form-input py-1 px-2 barang-select {{ $isLocked ? 'bg-gray-100 text-gray-700 cursor-not-allowed' : '' }}"
                                        @if($isLocked) disabled title="{{ $lockReason }}" @endif
                                    >
                                        <option value="">Pilih barang</option>

                                        @foreach ($barangs as $barang)
                                            <option
                                                value="{{ $barang->id }}"
                                                data-satuan="{{ $barang->satuan->nama ?? '' }}"
                                                data-barcode="{{ $barang->barcode }}"
                                                @selected($item->barang_id == $barang->id)
                                            >
                                                {{ $barang->nama }}{{ $barang->pabrik ? ' (' . $barang->pabrik->nama . ')' : '' }}{{ $barang->barcode ? ' — ' . $barang->barcode : '' }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @if ($isLocked)
                                        <input
                                            type="hidden"
                                            name="items[{{ $index }}][barang_id]"
                                            value="{{ $item->barang_id }}"
                                        >
                                    @endif
                                </td>

                                <td class="px-3 py-2">
                                    <input
                                        type="text"
                                        class="form-input py-1 px-2 barcode-field bg-gray-50"
                                        value="{{ $item->barang->barcode }}"
                                        readonly
                                    >
                                </td>

                                <td class="px-3 py-2">
                                    <input
                                        type="text"
                                        name="items[{{ $index }}][no_batch]"
                                        value="{{ $item->no_batch }}"
                                        required
                                        class="form-input py-1 px-2 font-mono {{ $isLocked ? 'bg-gray-100 text-gray-700 cursor-not-allowed' : '' }}"
                                        placeholder="Batch..."
                                        @readonly($isLocked)
                                        @if($isLocked) title="{{ $lockReason }}" @endif
                                    >
                                </td>

                                <td class="px-3 py-2">
                                    <input
                                        type="date"
                                        name="items[{{ $index }}][expired_date]"
                                        value="{{ $item->expired_date?->format('Y-m-d') }}"
                                        required
                                        class="form-input py-1 px-2 expired-field"
                                        style="min-width: 130px;"
                                    >
                                </td>

                                <td class="px-3 py-2">
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="items[{{ $index }}][harga_beli]"
                                        value="{{ $item->harga_beli }}"
                                        required
                                        class="form-input py-1 px-2 text-right font-mono harga-beli"
                                        placeholder="0"
                                        style="min-width: 120px;"
                                    >
                                </td>

                                <td class="px-3 py-2">
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="items[{{ $index }}][harga_jual]"
                                        value="{{ $item->harga_jual }}"
                                        required
                                        class="form-input py-1 px-2 text-right font-mono harga-jual"
                                        placeholder="0"
                                        style="min-width: 120px;"
                                    >
                                </td>

                                <td class="px-3 py-2">
                                    <input
                                        type="text"
                                        name="items[{{ $index }}][no_rak]"
                                        value="{{ $item->no_rak }}"
                                        required
                                        class="form-input py-1 px-2 font-mono"
                                        placeholder="A-01"
                                    >
                                </td>

                                <td class="px-3 py-2">
                                    @if ($isPrimaryBatch)
                                        <input
                                            type="number"
                                            min="0"
                                            name="items[{{ $index }}][jumlah_dipesan]"
                                            value="{{ old("items.$index.jumlah_dipesan", $dp ? $dp->jumlah_dipesan : $item->jumlah) }}"
                                            required
                                            class="form-input py-1 px-2 text-right font-mono jumlah-dipesan-field"
                                            placeholder="1"
                                        >
                                    @else
                                        <input
                                            type="hidden"
                                            name="items[{{ $index }}][jumlah_dipesan]"
                                            value="0"
                                        >
                                        <span class="text-[11px] text-gray-400 italic block text-right pt-1" title="Mengikuti jumlah dipesan baris utama">
                                            (Ikut induk)
                                        </span>
                                    @endif
                                </td>

                                <td class="px-3 py-2">
                                    <input
                                        type="number"
                                        min="0"
                                        name="items[{{ $index }}][jumlah_diterima]"
                                        value="{{ old("items.$index.jumlah_diterima", $item->jumlah) }}"
                                        required
                                        class="form-input py-1 px-2 text-right font-mono jumlah-diterima-field jumlah-field {{ $isLocked ? 'bg-gray-100 text-gray-700 cursor-not-allowed' : '' }}"
                                        placeholder="0"
                                        @readonly($isLocked)
                                        @if($isLocked) title="{{ $lockReason }}" @endif
                                    >
                                </td>

                                <td class="px-3 py-2">
                                    <input
                                        type="text"
                                        class="form-input py-1 px-2 satuan-field bg-gray-50"
                                        value="{{ $item->barang->satuan->nama ?? '' }}"
                                        readonly
                                    >
                                </td>

                                <td class="px-3 py-2 text-right font-mono font-semibold subtotal-field">
                                    Rp 0
                                </td>

                                <td class="px-3 py-2 text-center">
                                    @if ($isLocked)
                                        <button
                                            type="button"
                                            class="text-gray-300 p-1 cursor-not-allowed"
                                            disabled
                                            title="{{ $lockReason }}"
                                        >
                                            <x-heroicon-o-trash class="w-4 h-4 opacity-30" />
                                        </button>
                                    @else
                                        <button
                                            type="button"
                                            class="text-red-500 hover:text-red-700 p-1 btn-hapus-row-edit"
                                            aria-label="Hapus baris"
                                            title="Hapus baris"
                                        >
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    @endif
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>
        </div>

        <p
            class="text-xs text-gray-400 text-center py-4"
            id="empty-hint-edit"
            style="{{ $penerimaan->detail->count() > 0 ? 'display:none;' : '' }}"
        >
            Belum ada baris. Klik "+ Tambah Item Barang" untuk mulai input.
        </p>

        <div class="mt-4 flex flex-col sm:flex-row justify-end gap-4 text-sm font-semibold">
            <span>Total Belanja:</span>
            <span id="total-faktur-edit" class="text-blue-700 font-mono">
                Rp 0
            </span>
        </div>

        <div class="mt-2 flex flex-col sm:flex-row justify-end gap-4 text-sm font-semibold items-center">
            <label for="ppn_edit">PPN (11%)</label>
            <input
                type="text"
                id="ppn_edit"
                value="Rp 0"
                readonly
                class="form-input w-full sm:w-40 text-right font-mono bg-gray-50"
            >
        </div>

        <div class="mt-2 flex flex-col sm:flex-row justify-end gap-4 text-sm font-semibold">
            <span>Total Tagihan:</span>
            <span id="total-tagihan-edit" class="text-blue-700 font-mono">
                Rp 0
            </span>
        </div>

    </div>

    @php
        $pembayaranPertamaRecord = $penerimaan->pembayaran->firstWhere('keterangan', 'Pembayaran pertama') ?? $penerimaan->pembayaran->first();
        $pembayaranPertamaNilai = $pembayaranPertamaRecord ? (float) $pembayaranPertamaRecord->jumlah : 0;
    @endphp

    <!-- Card Pembayaran & Jatuh Tempo -->
    <div class="card-base p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">
                Pembayaran Saat Penerimaan
            </label>
            <input
                type="number"
                name="pembayaran_pertama"
                id="pembayaran_pertama_edit"
                value="{{ old('pembayaran_pertama', $pembayaranPertamaNilai) }}"
                min="0"
                step="0.01"
                class="form-input text-right font-mono"
            >
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">
                Jatuh Tempo
            </label>
            <input
                type="date"
                name="jatuh_tempo"
                id="jatuh_tempo_edit"
                value="{{ old('jatuh_tempo', $penerimaan->jatuh_tempo?->format('Y-m-d')) }}"
                class="form-input"
            >
        </div>

        <div class="text-xs text-gray-500 italic border-l-2 border-blue-500 pl-2">
            Jika pembayaran belum lunas, tanggal jatuh tempo wajib diisi. Pembayaran pertama akan diperbarui sesuai input.
        </div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="flex gap-2 pt-2">
        <button
            type="submit"
            id="btn-simpan-edit"
            class="btn-primary"
        >
            Simpan Perubahan
        </button>

        <button
            type="button"
            id="btn-batal-edit"
            class="btn-secondary"
        >
            Batal
        </button>
    </div>

</form>

{{-- TEMPLATE BARIS BARU UNTUK EDIT --}}
<template id="row-template-edit">

    <tr class="item-row hover:bg-gray-50 transition-colors">

        <td class="px-3 py-2">
            <select
                name="items[__i__][barang_id]"
                required
                class="form-input py-1 px-2 barang-select"
            >
                <option value="">Pilih barang</option>

                @foreach ($barangs as $barang)
                    <option
                        value="{{ $barang->id }}"
                        data-satuan="{{ $barang->satuan->nama ?? '' }}"
                        data-barcode="{{ $barang->barcode }}"
                    >
                        {{ $barang->nama }}{{ $barang->pabrik ? ' (' . $barang->pabrik->nama . ')' : '' }}{{ $barang->barcode ? ' — ' . $barang->barcode : '' }}
                    </option>
                @endforeach
            </select>
        </td>

        <td class="px-3 py-2">
            <input
                type="text"
                class="form-input py-1 px-2 barcode-field bg-gray-50"
                readonly
            >
        </td>

        <td class="px-3 py-2">
            <input
                type="text"
                name="items[__i__][no_batch]"
                required
                class="form-input py-1 px-2 font-mono"
                placeholder="Batch..."
            >
        </td>

        <td class="px-3 py-2">
            <input
                type="date"
                name="items[__i__][expired_date]"
                required
                class="form-input py-1 px-2 expired-field"
                style="min-width: 130px;"
            >
        </td>

        <td class="px-3 py-2">
            <input
                type="number"
                step="0.01"
                min="0"
                name="items[__i__][harga_beli]"
                required
                class="form-input py-1 px-2 text-right font-mono harga-beli"
                placeholder="0"
                style="min-width: 120px;"
            >
        </td>

        <td class="px-3 py-2">
            <input
                type="number"
                step="0.01"
                min="0"
                name="items[__i__][harga_jual]"
                required
                class="form-input py-1 px-2 text-right font-mono harga-jual"
                placeholder="0"
                style="min-width: 120px;"
            >
        </td>

        <td class="px-3 py-2">
            <input
                type="text"
                name="items[__i__][no_rak]"
                required
                class="form-input py-1 px-2 font-mono"
                placeholder="A-01"
            >
        </td>

        <td class="px-3 py-2">
            <input
                type="number"
                min="0"
                name="items[__i__][jumlah_dipesan]"
                required
                class="form-input py-1 px-2 text-right font-mono jumlah-dipesan-field"
                placeholder="1"
            >
        </td>

        <td class="px-3 py-2">
            <input
                type="number"
                min="0"
                name="items[__i__][jumlah_diterima]"
                required
                class="form-input py-1 px-2 text-right font-mono jumlah-diterima-field jumlah-field"
                placeholder="0"
            >
        </td>

        <td class="px-3 py-2">
            <input
                type="text"
                class="form-input py-1 px-2 satuan-field bg-gray-50"
                readonly
            >
        </td>

        <td class="px-3 py-2 text-right font-mono font-semibold subtotal-field">
            Rp 0
        </td>

        <td class="px-3 py-2 text-center">
            <button
                type="button"
                class="text-red-500 hover:text-red-700 p-1 btn-hapus-row-edit"
                aria-label="Hapus baris"
                title="Hapus baris"
            >
                <x-heroicon-o-trash class="w-4 h-4" />
            </button>
        </td>

    </tr>

</template>

<script>
function initEditPenerimaanForm() {
    let rowIndex = {{ $penerimaan->detail->count() + 10 }};

    const form = document.getElementById('form-edit-penerimaan');
    const tbody = document.getElementById('item-rows-edit');
    const template = document.getElementById('row-template-edit');
    const emptyHint = document.getElementById('empty-hint-edit');
    const totalFaktur = document.getElementById('total-faktur-edit');
    const ppnInput = document.getElementById('ppn_edit');
    const totalTagihan = document.getElementById('total-tagihan-edit');
    const btnTambah = document.getElementById('btn-tambah-item-edit');
    const btnBatal = document.getElementById('btn-batal-edit');
    const supplierSelect = document.getElementById('supplier_id_edit');
    const teleponSupplier = document.getElementById('telepon_supplier_edit');

    if (!form || !tbody) return;

    function formatRupiah(value) {
        return 'Rp ' + Math.round(value).toLocaleString('id-ID');
    }

    function updateTotal() {
        let total = 0;

        tbody.querySelectorAll('tr').forEach(function (row) {
            const harga = parseFloat(row.querySelector('.harga-beli')?.value) || 0;
            const jumlah = parseInt(row.querySelector('.jumlah-field')?.value, 10) || 0;
            const subtotal = harga * jumlah;

            total += subtotal;

            const subtotalField = row.querySelector('.subtotal-field');
            if (subtotalField) {
                subtotalField.textContent = formatRupiah(subtotal);
            }
        });

        const nilaiPpn = total * 0.11;

        if (totalFaktur) {
            totalFaktur.textContent = formatRupiah(total);
        }

        if (ppnInput) {
            if (ppnInput.tagName === 'INPUT') {
                ppnInput.value = formatRupiah(nilaiPpn);
            } else {
                ppnInput.textContent = formatRupiah(nilaiPpn);
            }
        }

        if (totalTagihan) {
            totalTagihan.textContent = formatRupiah(total + nilaiPpn);
        }
    }

    function tambahBaris() {
        if (!template || !tbody) return;

        const html = template.innerHTML.replaceAll('__i__', rowIndex);
        const tempTr = document.createElement('tbody');
        tempTr.innerHTML = html;

        tbody.appendChild(tempTr.firstElementChild);
        rowIndex++;

        if (emptyHint) {
            emptyHint.style.display = 'none';
        }

        updateTotal();
    }

    if (btnTambah) {
        btnTambah.addEventListener('click', tambahBaris);
    }

    // Batal edit
    if (btnBatal) {
        btnBatal.addEventListener('click', function () {
            const modal = document.getElementById('modal-edit-penerimaan');
            if (modal) {
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
            }
            const content = document.getElementById('edit-penerimaan-content');
            if (content) {
                content.innerHTML = '';
            }
        });
    }

    // Hapus baris
    tbody.addEventListener('click', function (e) {
        const button = e.target.closest('.btn-hapus-row-edit');
        if (!button) return;

        button.closest('tr').remove();

        if (tbody.children.length === 0 && emptyHint) {
            emptyHint.style.display = 'block';
        }

        updateTotal();
    });

    // Pilihan barang berubah
    tbody.addEventListener('change', function (e) {
        if (!e.target.classList.contains('barang-select')) return;

        const option = e.target.selectedOptions[0];
        const row = e.target.closest('tr');
        if (!row) return;

        const barcodeField = row.querySelector('.barcode-field');
        const satuanField = row.querySelector('.satuan-field');

        if (barcodeField) barcodeField.value = option?.dataset.barcode || '';
        if (satuanField) satuanField.value = option?.dataset.satuan || '';
    });

    // Input kalkulasi total
    tbody.addEventListener('input', updateTotal);

    // Supplier berubah
    if (supplierSelect) {
        supplierSelect.addEventListener('change', function () {
            if (teleponSupplier) {
                teleponSupplier.value = this.selectedOptions[0]?.dataset.telepon || '';
            }
        });
    }

    // Helper Validasi Field
    function setFieldError(input, message) {
        if (!input) return;
        input.classList.remove('border-gray-300');
        input.classList.add('!border-red-500', '!ring-1', '!ring-red-500', 'bg-red-50/30');

        const container = input.closest('td') || input.parentElement;
        if (!container) return;

        let errEl = container.querySelector('.field-error-msg');
        if (!errEl) {
            errEl = document.createElement('span');
            errEl.className = 'field-error-msg text-[11px] text-red-600 font-semibold block mt-1 leading-tight whitespace-normal';
            container.appendChild(errEl);
        }
        errEl.textContent = message;
    }

    function clearFieldError(input) {
        if (!input) return;
        input.classList.remove('!border-red-500', '!ring-1', '!ring-red-500', 'bg-red-50/30');
        input.classList.add('border-gray-300');

        const container = input.closest('td') || input.parentElement;
        if (!container) return;

        const errEl = container.querySelector('.field-error-msg');
        if (errEl) {
            errEl.remove();
        }
    }

    function clearAllErrors() {
        form.querySelectorAll('.field-error-msg').forEach(el => el.remove());
        form.querySelectorAll('.!border-red-500').forEach(input => {
            input.classList.remove('!border-red-500', '!ring-1', '!ring-red-500', 'bg-red-50/30');
            input.classList.add('border-gray-300');
        });
        const generalBox = document.getElementById('edit-general-error');
        if (generalBox) {
            generalBox.classList.add('hidden');
            generalBox.textContent = '';
        }
    }

    function validateRowExpired(row, tanggalPenerimaan) {
        if (!row) return true;
        const input = row.querySelector('[name$="[expired_date]"]');
        if (!input) return true;

        if (input.value && tanggalPenerimaan) {
            if (input.value < tanggalPenerimaan) {
                setFieldError(input, 'Tanggal expired tidak boleh sebelum tanggal penerimaan.');
                return false;
            }
        }
        clearFieldError(input);
        return true;
    }

    function validateRowHarga(row) {
        if (!row) return true;
        const hbInput = row.querySelector('.harga-beli') || row.querySelector('[name$="[harga_beli]"]');
        const hjInput = row.querySelector('.harga-jual') || row.querySelector('[name$="[harga_jual]"]');
        if (!hbInput || !hjInput) return true;

        const hb = parseFloat(hbInput.value);
        const hj = parseFloat(hjInput.value);

        if (!isNaN(hb) && !isNaN(hj)) {
            if (hj < hb) {
                setFieldError(hjInput, 'Harga jual tidak boleh lebih kecil dari harga beli.');
                return false;
            }
        }
        clearFieldError(hjInput);
        return true;
    }

    function validateFullForm() {
        clearAllErrors();
        let isValid = true;
        let firstErrorField = null;

        const tglPenerimaan = document.getElementById('tanggal_edit')?.value || form.querySelector('[name="tanggal"]')?.value || '';

        tbody.querySelectorAll('tr').forEach(row => {
            // Validasi Expired
            const expInput = row.querySelector('[name$="[expired_date]"]');
            if (expInput) {
                if (!expInput.value) {
                    setFieldError(expInput, 'Tanggal expired wajib diisi.');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = expInput;
                } else if (tglPenerimaan && expInput.value < tglPenerimaan) {
                    setFieldError(expInput, 'Tanggal expired tidak boleh sebelum tanggal penerimaan.');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = expInput;
                }
            }

            // Validasi Harga
            const hbInput = row.querySelector('.harga-beli') || row.querySelector('[name$="[harga_beli]"]');
            const hjInput = row.querySelector('.harga-jual') || row.querySelector('[name$="[harga_jual]"]');
            if (hbInput && hjInput) {
                const hb = parseFloat(hbInput.value);
                const hj = parseFloat(hjInput.value);

                if (isNaN(hj) || hj < 0) {
                    setFieldError(hjInput, 'Harga jual wajib diisi.');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = hjInput;
                } else if (!isNaN(hb) && hj < hb) {
                    setFieldError(hjInput, 'Harga jual tidak boleh lebih kecil dari harga beli.');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = hjInput;
                }
            }
        });

        if (!isValid && firstErrorField) {
            firstErrorField.focus();
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        return isValid;
    }

    function applyServerErrors(errors) {
        clearAllErrors();
        let firstErrorField = null;
        let unmappedMessages = [];

        for (const key in errors) {
            const msgs = Array.isArray(errors[key]) ? errors[key] : [errors[key]];
            const msg = msgs[0];

            // Cek items.0.expired_date atau items.0.harga_jual
            const match = key.match(/^items\.(\d+)\.([a-z_]+)$/);
            if (match) {
                const rowIndex = parseInt(match[1], 10);
                const fieldName = match[2];
                const rows = tbody.querySelectorAll('tr');
                const row = rows[rowIndex];
                if (row) {
                    const input = row.querySelector(`[name$="[${fieldName}]"]`);
                    if (input) {
                        setFieldError(input, msg);
                        if (!firstErrorField) firstErrorField = input;
                        continue;
                    }
                }
            }

            // Cek top level field (no_faktur, tanggal, supplier_id, dll)
            const topInput = form.querySelector(`[name="${key}"]`);
            if (topInput) {
                setFieldError(topInput, msg);
                if (!firstErrorField) firstErrorField = topInput;
                continue;
            }

            unmappedMessages.push(msg);
        }

        const generalBox = document.getElementById('edit-general-error');
        if (generalBox && unmappedMessages.length > 0) {
            generalBox.textContent = unmappedMessages.join(', ');
            generalBox.classList.remove('hidden');
        }

        if (firstErrorField) {
            firstErrorField.focus();
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    // Realtime input listener untuk validasi instan
    tbody.addEventListener('input', function (e) {
        const target = e.target;
        const row = target.closest('tr');
        if (!row) return;

        if (target.name && target.name.includes('[expired_date]')) {
            const tgl = document.getElementById('tanggal_edit')?.value || form.querySelector('[name="tanggal"]')?.value;
            validateRowExpired(row, tgl);
        } else if (target.name && (target.name.includes('[harga_beli]') || target.name.includes('[harga_jual]'))) {
            validateRowHarga(row);
        }
    });

    tbody.addEventListener('change', function (e) {
        const target = e.target;
        const row = target.closest('tr');
        if (!row) return;

        if (target.name && target.name.includes('[expired_date]')) {
            const tgl = document.getElementById('tanggal_edit')?.value || form.querySelector('[name="tanggal"]')?.value;
            validateRowExpired(row, tgl);
        } else if (target.name && (target.name.includes('[harga_beli]') || target.name.includes('[harga_jual]'))) {
            validateRowHarga(row);
        }
    });

    const tglEditInput = document.getElementById('tanggal_edit') || form.querySelector('[name="tanggal"]');
    if (tglEditInput) {
        tglEditInput.addEventListener('change', function () {
            const tgl = this.value;
            tbody.querySelectorAll('tr').forEach(row => {
                validateRowExpired(row, tgl);
            });
        });
    }

    // Submit Edit Form via AJAX
    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (tbody.children.length === 0) {
            alert('Tambahkan minimal 1 baris barang.');
            return;
        }

        // Jalankan validasi frontend menyeluruh sebelum submit
        if (!validateFullForm()) {
            return;
        }

        const submitBtn = document.getElementById('btn-simpan-edit');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan Perubahan...';
        }

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const result = await response.json().catch(() => null);

            if (!response.ok) {
                if (result && result.errors) {
                    applyServerErrors(result.errors);
                } else if (result && result.message) {
                    const generalBox = document.getElementById('edit-general-error');
                    if (generalBox) {
                        generalBox.textContent = result.message;
                        generalBox.classList.remove('hidden');
                    } else {
                        alert(result.message);
                    }
                } else {
                    alert('Terjadi kesalahan saat menyimpan perubahan faktur.');
                }

                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Simpan Perubahan';
                }
                return;
            }

            // Sukses disimpan
            const modalEdit = document.getElementById('modal-edit-penerimaan');
            if (modalEdit) {
                modalEdit.classList.add('hidden');
                modalEdit.setAttribute('aria-hidden', 'true');
            }
            const editContent = document.getElementById('edit-penerimaan-content');
            if (editContent) editContent.innerHTML = '';

            // Refresh halaman agar tabel daftar penerimaan menampilkan data terbaru
            window.location.reload();

        } catch (err) {
            console.error('Error saat submit edit penerimaan:', err);
            alert('Terjadi kesalahan jaringan atau sistem saat menyimpan perubahan.');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Simpan Perubahan';
            }
        }
    });

    // Hitung total saat modal pertama kali dibuka
    updateTotal();
}
</script>

@if ($errors->any())
    <div class="alert-danger p-4 mb-6">
        <strong class="block text-xs font-bold mb-1.5">
            Perbaiki kesalahan berikut sebelum menyimpan faktur:
        </strong>

        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    action="{{ route('penerimaan.update', $penerimaan) }}"
    method="POST"
    id="form-penerimaan"
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
                id="supplier_id"
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
                id="telepon_supplier"
                value="{{ $penerimaan->telepon_supplier }}"
                class="form-input bg-gray-50"
                readonly
                placeholder="Otomatis dari master supplier"
            >
        </div>

        <div class="sm:col-span-2">
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

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">
                Jatuh Tempo
            </label>

            <input
                type="date"
                name="jatuh_tempo"
                value="{{ old('jatuh_tempo', $penerimaan->jatuh_tempo?->format('Y-m-d')) }}"
                class="form-input"
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
                id="btn-tambah-item"
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

                    <tbody id="item-rows" class="table-custom-body divide-y divide-gray-150">
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
                                $hasRiwayat = $penerimaan->riwayatPenerimaan->where('detail_penerimaan_id', $item->id)->isNotEmpty();
                                $isLocked = $isSold || $isDamaged || $hasRiwayat;

                                $lockReason = '';
                                if ($isSold) {
                                    $lockReason = 'Terkunci: Obat ini sudah tercatat di transaksi kasir';
                                } elseif ($isDamaged) {
                                    $lockReason = 'Terkunci: Obat ini sudah tercatat di data barang rusak';
                                } elseif ($hasRiwayat) {
                                    $lockReason = 'Terkunci: Baris ini memiliki riwayat susulan';
                                }
                            @endphp

                            <tr class="item-row hover:bg-gray-50 transition-colors">
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
                                                {{ $barang->nama }}{{ $barang->barcode ? ' — ' . $barang->barcode : '' }}
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
                                        class="form-input py-1 px-2"
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
                                        class="form-input py-1 px-2 text-right font-mono"
                                        placeholder="0"
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
                                            min="1"
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
                                            class="text-red-500 hover:text-red-700 p-1 btn-hapus-row"
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
            id="empty-hint"
            style="{{ $penerimaan->detail->count() > 0 ? 'display:none;' : '' }}"
        >
            Belum ada baris. Klik "+ Tambah Item Barang" untuk mulai input.
        </p>

        <div class="mt-4 flex flex-col sm:flex-row justify-end gap-4 text-sm font-semibold">
            <span>Total Belanja:</span>
            <span id="total-faktur" class="text-blue-700 font-mono">
                Rp 0
            </span>
        </div>

        <div class="mt-2 flex flex-col sm:flex-row justify-end gap-4 text-sm font-semibold items-center">
            <label for="ppn">PPN (11%)</label>
            <input
                type="text"
                id="ppn"
                value="Rp 0"
                readonly
                class="form-input w-full sm:w-40 text-right font-mono bg-gray-50"
            >
        </div>

        <div class="mt-2 flex flex-col sm:flex-row justify-end gap-4 text-sm font-semibold">
            <span>Total Tagihan:</span>
            <span id="total-tagihan" class="text-blue-700 font-mono">
                Rp 0
            </span>
        </div>

    </div>

    {{-- ACTION (Mirip dengan Create) --}}
    <div class="flex gap-2 pt-2">
        <button
            type="submit"
            class="btn-primary"
        >
            Simpan Perubahan
        </button>

        <button
            type="button"
            id="btn-batal-edit"
            class="btn-secondary"
            onclick="document.getElementById('modal-edit-penerimaan').classList.add('hidden')"
        >
            Batal
        </button>
    </div>

</form>

{{-- TEMPLATE BARIS BARU --}}
<template id="row-template">

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
                        {{ $barang->nama }}
                        {{ $barang->barcode ? ' — ' . $barang->barcode : '' }}
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
                class="form-input py-1 px-2"
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
            >
        </td>

        <td class="px-3 py-2">
            <input
                type="number"
                step="0.01"
                min="0"
                name="items[__i__][harga_jual]"
                required
                class="form-input py-1 px-2 text-right font-mono"
                placeholder="0"
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
                min="1"
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
                class="text-red-500 hover:text-red-700 p-1 btn-hapus-row"
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

    let rowIndex = {{ $penerimaan->detail->count() }};

    const tbody = document.getElementById('item-rows');
    const template = document.getElementById('row-template');
    const emptyHint = document.getElementById('empty-hint');
    const totalFaktur = document.getElementById('total-faktur');
    const ppnInput = document.getElementById('ppn');
    const totalTagihan = document.getElementById('total-tagihan');

    function formatRupiah(value) {
        return 'Rp ' + Math.round(value).toLocaleString('id-ID');
    }

    function updateTotal() {
        let total = 0;

        tbody.querySelectorAll('tr').forEach(function (row) {

            const harga = parseFloat(
                row.querySelector('.harga-beli')?.value
            ) || 0;

            const jumlah = parseInt(
                row.querySelector('.jumlah-field')?.value,
                10
            ) || 0;

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

        const html = template.innerHTML.replaceAll(
            '__i__',
            rowIndex
        );

        const tempTr = document.createElement('tbody');

        tempTr.innerHTML = html;

        tbody.appendChild(tempTr.firstElementChild);

        rowIndex++;

        emptyHint.style.display = 'none';

        updateTotal();
    }

    document.getElementById('btn-tambah-item')
        ?.addEventListener('click', tambahBaris);

    tbody.addEventListener('click', function (e) {

        const button = e.target.closest('.btn-hapus-row');

        if (!button) {
            return;
        }

        button.closest('tr').remove();

        if (tbody.children.length === 0) {
            emptyHint.style.display = 'block';
        }

        updateTotal();
    });

    tbody.addEventListener('change', function (e) {

        if (!e.target.classList.contains('barang-select')) {
            return;
        }

        const option = e.target.selectedOptions[0];
        const row = e.target.closest('tr');

        row.querySelector('.barcode-field').value =
            option?.dataset.barcode || '';

        row.querySelector('.satuan-field').value =
            option?.dataset.satuan || '';
    });

    tbody.addEventListener('input', updateTotal);

    document.getElementById('supplier_id')
        ?.addEventListener('change', function () {

            document.getElementById('telepon_supplier').value =
                this.selectedOptions[0]?.dataset.telepon || '';
        });

    document.getElementById('form-penerimaan')
        ?.addEventListener('submit', function (e) {

            if (tbody.children.length === 0) {
                e.preventDefault();

                alert('Tambahkan minimal 1 baris barang.');
            }
        });

    updateTotal();
};
</script>

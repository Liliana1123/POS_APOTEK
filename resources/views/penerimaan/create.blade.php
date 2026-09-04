@extends('layouts.app')
@section('title', 'Faktur Penerimaan Baru')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1>Faktur Penerimaan Barang Baru</h1>
        <p class="text-caption mt-1">Catat faktur masuk obat dari supplier beserta detail expired date batch.</p>
    </div>
    <a href="{{ route('penerimaan.index') }}" class="btn-secondary py-2 px-4">
        &larr; Kembali
    </a>
</div>

@if ($errors->any())
    <div class="alert-danger p-4 mb-6">
        <strong class="block text-xs font-bold mb-1.5">Perbaiki kesalahan berikut sebelum menyimpan faktur:</strong>
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('penerimaan.store') }}" method="POST" id="form-penerimaan" class="space-y-6">
    @csrf

    <!-- Form Header Card -->
    <div class="card-base p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-start">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">No. Faktur <span class="text-red-500 font-bold">*</span></label>
            <input type="text" name="no_faktur" value="{{ old('no_faktur') }}" required
                class="form-input font-mono font-semibold" placeholder="Nomor faktur masuk...">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Tanggal Penerimaan <span class="text-red-500 font-bold">*</span></label>
            <input type="date" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required
                class="form-input">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Supplier <span class="text-red-500 font-bold">*</span></label>
            <select name="supplier_id" id="supplier_id" required class="form-input">
                <option value="">Pilih Supplier</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" data-telepon="{{ $supplier->telepon }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">No. Telepon Supplier</label>
            <input type="text" id="telepon_supplier" class="form-input bg-gray-50" readonly placeholder="Otomatis dari master supplier">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Keterangan</label>
            <input type="text" name="keterangan" value="{{ old('keterangan') }}" class="form-input" placeholder="Keterangan penerimaan (opsional)">
        </div>
    </div>

    <!-- Details Card -->
    <div class="card-base p-6">
        <div class="flex justify-between items-center mb-4 pb-2 border-b">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">Detail Barang Diterima</h3>
            <button type="button" id="btn-tambah-item" class="btn-secondary py-1 px-3 text-xs font-semibold">
                + Tambah Baris
            </button>
        </div>

        <div class="table-custom-container">
            <div class="overflow-x-auto">
                <table class="table-custom min-w-[55rem] mb-2">
                    <thead class="table-custom-header">
                        <tr>
                            <th scope="col" class="px-3 py-2">Barang <span class="text-red-500 font-bold">*</span></th>
                            <th scope="col" class="px-3 py-2 w-32">Barcode</th>
                            <th scope="col" class="px-3 py-2 w-32">No. Batch <span class="text-red-500 font-bold">*</span></th>
                            <th scope="col" class="px-3 py-2 w-36">Expired Date <span class="text-red-500 font-bold">*</span></th>
                            <th scope="col" class="px-3 py-2 w-28 text-right">Harga Beli <span class="text-red-500 font-bold">*</span></th>
                            <th scope="col" class="px-3 py-2 w-28 text-right">Harga Jual <span class="text-red-500 font-bold">*</span></th>
                            <th scope="col" class="px-3 py-2 w-24">No. Rak <span class="text-red-500 font-bold">*</span></th>
                            <th scope="col" class="px-3 py-2 w-24 text-right">Jumlah <span class="text-red-500 font-bold">*</span></th>
                            <th scope="col" class="px-3 py-2 w-24">Satuan</th>
                            <th scope="col" class="px-3 py-2 w-32 text-right">Subtotal</th>
                            <th scope="col" class="px-3 py-2 w-12"></th>
                        </tr>
                    </thead>
                    <tbody id="item-rows" class="table-custom-body divide-y divide-gray-150"></tbody>
                </table>
            </div>
        </div>

        <p class="text-xs text-gray-400 text-center py-4" id="empty-hint">Belum ada baris. Klik "+ Tambah Baris" untuk mulai input.</p>
        <div class="mt-4 flex flex-col sm:flex-row justify-end gap-4 text-sm font-semibold">
            <span>Total Faktur:</span>
            <span id="total-faktur" class="text-blue-700 font-mono">Rp 0</span>
        </div>
    </div>

    <div class="card-base p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Pembayaran Saat Penerimaan</label>
            <input type="number" name="pembayaran_pertama" id="pembayaran_pertama" value="{{ old('pembayaran_pertama', 0) }}" min="0" step="0.01" class="form-input text-right font-mono">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Jatuh Tempo</label>
            <input type="date" name="jatuh_tempo" value="{{ old('jatuh_tempo') }}" class="form-input">
        </div>
        <div class="text-xs text-gray-500">Pembayaran pertama dicatat sebagai histori dan tidak menimpa pembayaran sebelumnya.</div>
    </div>

    <div class="flex gap-2 pt-2">
        <button type="submit" class="btn-primary">Simpan Faktur</button>
        <a href="{{ route('penerimaan.index') }}" class="btn-secondary">Batal</a>
    </div>
</form>

<template id="row-template">
    <tr class="item-row hover:bg-gray-50 transition-colors">
        <td class="px-3 py-2">
            <select name="items[__i__][barang_id]" required class="form-input py-1 px-2 barang-select">
                <option value="">Pilih barang</option>
                @foreach ($barangs as $barang)
                    <option value="{{ $barang->id }}" data-pabrik="{{ $barang->pabrik->nama ?? '' }}" data-satuan="{{ $barang->satuan->nama ?? '' }}" data-barcode="{{ $barang->barcode }}">{{ $barang->nama }}{{ $barang->barcode ? ' — ' . $barang->barcode : '' }}</option>
                @endforeach
            </select>
        </td>
        <td class="px-3 py-2">
            <input type="text" class="form-input py-1 px-2 barcode-field bg-gray-50" readonly>
        </td>
        <td class="px-3 py-2">
            <input type="text" name="items[__i__][no_batch]" required class="form-input py-1 px-2 font-mono" placeholder="Batch...">
        </td>
        <td class="px-3 py-2">
            <input type="date" name="items[__i__][expired_date]" required class="form-input py-1 px-2">
        </td>
        <td class="px-3 py-2"><input type="number" step="0.01" min="0" name="items[__i__][harga_beli]" required class="form-input py-1 px-2 text-right font-mono harga-beli" placeholder="0"></td>
        <td class="px-3 py-2"><input type="number" step="0.01" min="0" name="items[__i__][harga_jual]" required class="form-input py-1 px-2 text-right font-mono" placeholder="0"></td>
        <td class="px-3 py-2"><input type="text" name="items[__i__][no_rak]" required class="form-input py-1 px-2 font-mono" placeholder="A-01"></td>
        <td class="px-3 py-2">
            <input type="number" min="1" name="items[__i__][jumlah]" required class="form-input py-1 px-2 text-right font-mono jumlah-field" placeholder="1">
        </td>
        <td class="px-3 py-2"><input type="text" class="form-input py-1 px-2 satuan-field bg-gray-50" readonly></td>
        <td class="px-3 py-2 text-right font-mono font-semibold subtotal-field">Rp 0</td>
        <td class="px-3 py-2 text-center">
            <button type="button" class="text-red-500 hover:text-red-700 p-1 btn-hapus-row" aria-label="Hapus baris" title="Hapus baris"><x-heroicon-o-trash class="w-4 h-4" /></button>
        </td>
    </tr>
</template>

<script>
let rowIndex = 0;
const tbody = document.getElementById('item-rows');
const template = document.getElementById('row-template');
const emptyHint = document.getElementById('empty-hint');
const totalFaktur = document.getElementById('total-faktur');

function formatRupiah(value) { return 'Rp ' + Math.round(value).toLocaleString('id-ID'); }
function updateTotal() {
    let total = 0;
    tbody.querySelectorAll('tr').forEach(row => {
        const harga = parseFloat(row.querySelector('.harga-beli')?.value) || 0;
        const jumlah = parseInt(row.querySelector('.jumlah-field')?.value, 10) || 0;
        const subtotal = harga * jumlah;
        total += subtotal;
        row.querySelector('.subtotal-field').textContent = formatRupiah(subtotal);
    });
    totalFaktur.textContent = formatRupiah(total);
}

function tambahBaris() {
    const html = template.innerHTML.replaceAll('__i__', rowIndex);
    const tempTr = document.createElement('tbody');
    tempTr.innerHTML = html;
    tbody.appendChild(tempTr.firstElementChild);
    rowIndex++;
    emptyHint.style.display = 'none';
    updateTotal();
}

document.getElementById('btn-tambah-item').addEventListener('click', tambahBaris);

tbody.addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-hapus-row')) {
        e.target.closest('tr').remove();
        if (tbody.children.length === 0) {
            emptyHint.style.display = 'block';
        }
        updateTotal();
    }
});

tbody.addEventListener('change', function (e) {
    if (e.target.classList.contains('barang-select')) {
        const option = e.target.selectedOptions[0];
        const row = e.target.closest('tr');
        row.querySelector('.barcode-field').value = option?.dataset.barcode || '';
        row.querySelector('.satuan-field').value = option?.dataset.satuan || '';
    }
});
tbody.addEventListener('input', updateTotal);

document.getElementById('supplier_id').addEventListener('change', function () {
    document.getElementById('telepon_supplier').value = this.selectedOptions[0]?.dataset.telepon || '';
});

document.getElementById('form-penerimaan').addEventListener('submit', function (e) {
    if (tbody.children.length === 0) {
        e.preventDefault();
        alert('Tambahkan minimal 1 baris barang.');
    }
});

// Mulai dengan 1 baris kosong
tambahBaris();
</script>
@endsection

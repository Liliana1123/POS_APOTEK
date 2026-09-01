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
            <select name="supplier_id" required class="form-input">
                <option value="">Pilih Supplier</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center pt-8">
            <label class="flex items-center text-xs font-medium text-gray-700 cursor-pointer">
                <input type="checkbox" name="lunas" value="1" @checked(old('lunas')) class="mr-2.5 rounded border-gray-300 focus:ring-blue-500 w-4 h-4 text-blue-600">
                <div>
                    <span class="font-semibold">Sudah Lunas</span>
                    <p class="text-[10px] text-gray-400 mt-0.5 font-sans">Faktur lunas ke supplier.</p>
                </div>
            </label>
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
                            <th scope="col" class="px-3 py-2">Nama Barang <span class="text-red-500 font-bold">*</span></th>
                            <th scope="col" class="px-3 py-2 w-32">No. Batch <span class="text-red-500 font-bold">*</span></th>
                            <th scope="col" class="px-3 py-2 w-28 text-right">Harga Beli <span class="text-red-500 font-bold">*</span></th>
                            <th scope="col" class="px-3 py-2 w-28 text-right">Harga Jual <span class="text-red-500 font-bold">*</span></th>
                            <th scope="col" class="px-3 py-2 w-36">Expired Date <span class="text-red-500 font-bold">*</span></th>
                            <th scope="col" class="px-3 py-2 w-24 text-right">Jumlah <span class="text-red-500 font-bold">*</span></th>
                            <th scope="col" class="px-3 py-2 w-12"></th>
                        </tr>
                    </thead>
                    <tbody id="item-rows" class="table-custom-body divide-y divide-gray-150"></tbody>
                </table>
            </div>
        </div>

        <p class="text-xs text-gray-400 text-center py-4" id="empty-hint">Belum ada baris. Klik "+ Tambah Baris" untuk mulai input.</p>
    </div>

    <div class="flex gap-2 pt-2">
        <button type="submit" class="btn-primary">Simpan Faktur</button>
        <a href="{{ route('penerimaan.index') }}" class="btn-secondary">Batal</a>
    </div>
</form>

<template id="row-template">
    <tr class="item-row hover:bg-gray-50 transition-colors">
        <td class="px-3 py-2">
            <select name="items[__i__][barang_id]" required class="form-input py-1 px-2">
                <option value="">Pilih barang</option>
                @foreach ($barangs as $barang)
                    <option value="{{ $barang->id }}">{{ $barang->nama }}</option>
                @endforeach
            </select>
        </td>
        <td class="px-3 py-2">
            <input type="text" name="items[__i__][no_batch]" required class="form-input py-1 px-2 font-mono" placeholder="Batch...">
        </td>
        <td class="px-3 py-2">
            <input type="number" step="0.01" min="0" name="items[__i__][harga_beli]" required class="form-input py-1 px-2 text-right font-mono" placeholder="0">
        </td>
        <td class="px-3 py-2">
            <input type="number" step="0.01" min="0" name="items[__i__][harga_jual]" required class="form-input py-1 px-2 text-right font-mono" placeholder="0">
        </td>
        <td class="px-3 py-2">
            <input type="date" name="items[__i__][expired_date]" required class="form-input py-1 px-2">
        </td>
        <td class="px-3 py-2">
            <input type="number" min="1" name="items[__i__][jumlah]" required class="form-input py-1 px-2 text-right font-mono" placeholder="1">
        </td>
        <td class="px-3 py-2 text-center">
            <button type="button" class="text-red-500 hover:text-red-700 font-bold text-lg btn-hapus-row" aria-label="Hapus baris">&times;</button>
        </td>
    </tr>
</template>

<script>
let rowIndex = 0;
const tbody = document.getElementById('item-rows');
const template = document.getElementById('row-template');
const emptyHint = document.getElementById('empty-hint');

function tambahBaris() {
    const html = template.innerHTML.replaceAll('__i__', rowIndex);
    const tempTr = document.createElement('tbody');
    tempTr.innerHTML = html;
    tbody.appendChild(tempTr.firstElementChild);
    rowIndex++;
    emptyHint.style.display = 'none';
}

document.getElementById('btn-tambah-item').addEventListener('click', tambahBaris);

tbody.addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-hapus-row')) {
        e.target.closest('tr').remove();
        if (tbody.children.length === 0) {
            emptyHint.style.display = 'block';
        }
    }
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

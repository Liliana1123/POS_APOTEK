@extends('layouts.app')

@section('title', 'Edit Faktur Penerimaan')

@section('content')

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1>Edit Faktur Penerimaan Barang</h1>
        <p class="text-caption mt-1">
            Ubah informasi faktur dan detail barang penerimaan.
        </p>
    </div>

    <a href="{{ route('penerimaan.index') }}" class="btn-secondary py-2 px-4">
        &larr; Kembali
    </a>
</div>

@if ($errors->any())
    <div class="alert-danger p-4 mb-6">
        <strong class="block text-xs font-bold mb-1.5">
            Perbaiki kesalahan berikut sebelum menyimpan:
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

    {{-- HEADER FAKTUR --}}
    <div class="card-base p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-start">

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">
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
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                Tanggal Penerimaan <span class="text-red-500 font-bold">*</span>
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
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">
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
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">
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

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">
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

    {{-- DETAIL BARANG --}}
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
                + Tambah Baris
            </button>
        </div>

        <div class="table-custom-container">
            <div class="overflow-x-auto">

                <table class="table-custom w-full table-fixed mb-2">
                    <colgroup>
                        <col class="w-[210px]">
                        <col class="w-[100px]">
                        <col class="w-[110px]">
                        <col class="w-[125px]">
                        <col class="w-[105px]">
                        <col class="w-[105px]">
                        <col class="w-[90px]">
                        <col class="w-[90px]">
                        <col class="w-[85px]">
                        <col class="w-[110px]">
                        <col class="w-[50px]">
                    </colgroup>

                    <thead class="table-custom-header">
                        <tr>
                            <th scope="col" class="px-3 py-2">
                                Barang <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 w-32">
                                Barcode
                            </th>

                            <th scope="col" class="px-3 py-2 w-32">
                                No. Batch <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 w-36">
                                Expired Date <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 w-28 text-right">
                                Harga Beli <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 w-28 text-right">
                                Harga Jual <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 w-24">
                                No. Rak <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 w-24 text-right">
                                Jumlah <span class="text-red-500 font-bold">*</span>
                            </th>

                            <th scope="col" class="px-3 py-2 w-24">
                                Satuan
                            </th>

                            <th scope="col" class="px-3 py-2 w-32 text-right">
                                Subtotal
                            </th>

                            <th scope="col" class="px-3 py-2 w-12"></th>
                        </tr>
                    </thead>

                    <tbody id="item-rows" class="table-custom-body divide-y divide-gray-150">

                        @foreach ($penerimaan->detail as $index => $item)

                            <tr class="item-row hover:bg-gray-50 transition-colors">
                                <input type="hidden"name="items[{{ $index }}][detail_id]"value="{{ $item->id }}">

                                <td class="px-3 py-2">
                                    <select
                                        name="items[{{ $index }}][barang_id]"
                                        required
                                        class="form-input py-1 px-2 barang-select"
                                    >
                                        <option value="">Pilih barang</option>

                                        @foreach ($barangs as $barang)
                                            <option
                                                value="{{ $barang->id }}"
                                                data-satuan="{{ $barang->satuan->nama ?? '' }}"
                                                data-barcode="{{ $barang->barcode }}"
                                                @selected($item->barang_id == $barang->id)
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
                                        value="{{ $item->barang->barcode ?? '' }}"
                                        readonly
                                    >
                                </td>

                                <td class="px-3 py-2">
                                    <input
                                        type="text"
                                        name="items[{{ $index }}][no_batch]"
                                        value="{{ $item->no_batch }}"
                                        required
                                        class="form-input py-1 px-2 font-mono"
                                        placeholder="Batch..."
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
                                    <input
                                        type="number"
                                        min="1"
                                        name="items[{{ $index }}][jumlah]"
                                        value="{{ $item->jumlah }}"
                                        required
                                        class="form-input py-1 px-2 text-right font-mono jumlah-field"
                                        placeholder="1"
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
            Belum ada baris. Klik "+ Tambah Baris" untuk mulai input.
        </p>

        <div class="mt-4 flex flex-col sm:flex-row justify-end gap-4 text-sm font-semibold">
            <span>Total Faktur:</span>

            <span id="total-faktur" class="text-blue-700 font-mono">
                Rp 0
            </span>
        </div>

    </div>

    {{-- PEMBAYARAN --}}
    <div class="card-base p-6">

        <div class="mb-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">
                Informasi Pembayaran
            </h3>

            <p class="text-caption mt-1">
                Pembayaran yang sudah tercatat tidak diubah melalui Edit Penerimaan.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            <div>
                <span class="block text-xs font-semibold text-gray-500 mb-1">
                    Total Sudah Dibayar
                </span>

                <div class="form-input bg-gray-50 font-mono">
                    Rp {{ number_format($penerimaan->totalDibayar(), 0, ',', '.') }}
                </div>
            </div>

            <div>
                <span class="block text-xs font-semibold text-gray-500 mb-1">
                    Sisa Tagihan
                </span>

                <div class="form-input bg-gray-50 font-mono">
                    Rp {{ number_format($penerimaan->sisaTagihan(), 0, ',', '.') }}
                </div>
            </div>

            <div>
                <span class="block text-xs font-semibold text-gray-500 mb-1">
                    Status
                </span>

                <div class="form-input bg-gray-50 font-semibold">
                    {{ $penerimaan->lunas ? 'Lunas' : 'Belum Lunas' }}
                </div>
            </div>

        </div>

    </div>

    {{-- ACTION --}}
    <div class="flex gap-2 pt-2">

        <button type="submit" class="btn-primary">
            Simpan Perubahan
        </button>

        <a href="{{ route('penerimaan.index') }}" class="btn-secondary">
            Batal
        </a>

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
                name="items[__i__][jumlah]"
                required
                class="form-input py-1 px-2 text-right font-mono jumlah-field"
                placeholder="1"
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
document.addEventListener('DOMContentLoaded', function () {

    let rowIndex = {{ $penerimaan->detail->count() }};

    const tbody = document.getElementById('item-rows');
    const template = document.getElementById('row-template');
    const emptyHint = document.getElementById('empty-hint');
    const totalFaktur = document.getElementById('total-faktur');

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

        totalFaktur.textContent = formatRupiah(total);
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
        .addEventListener('click', tambahBaris);

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
        .addEventListener('change', function () {

            document.getElementById('telepon_supplier').value =
                this.selectedOptions[0]?.dataset.telepon || '';
        });

    document.getElementById('form-penerimaan')
        .addEventListener('submit', function (e) {

            if (tbody.children.length === 0) {
                e.preventDefault();

                alert('Tambahkan minimal 1 baris barang.');
            }
        });

    updateTotal();
});
</script>

@endsection
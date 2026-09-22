@extends('layouts.app')
@section('title', 'Penerimaan Barang')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <h1>Daftar Penerimaan Barang</h1>
        <p class="text-caption mt-1">Kelola data faktur obat masuk, supplier, dan status pembayaran.</p>
    </div>
    <button type="button" id="btn-tambah-penerimaan" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Tambah Faktur Penerimaan Baru</span>
    </button>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('penerimaan.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">No. Faktur</label>
                <div class="relative">
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari no. faktur..."
                        class="form-input pr-8 font-mono">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </span>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Supplier</label>
                <select name="supplier_id" class="form-input">
                    <option value="">Semua Supplier</option>
                    @foreach ($suppliers as $s)
                        <option value="{{ $s->id }}" @selected(request('supplier_id') == $s->id)>{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-center text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Tanggal Penerimaan
                </label>

                <div class="flex items-center gap-2">
                    <input
                        type="date"
                        name="tanggal_mulai"
                        value="{{ request('tanggal_mulai') }}"
                        class="form-input min-w-0"
                    >

                    <span class="text-sm text-gray-400">-</span>

                    <input
                        type="date"
                        name="tanggal_akhir"
                        value="{{ request('tanggal_akhir') }}"
                        class="form-input min-w-0"
                    >
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Status Penerimaan
                </label>

                <select name="status_penerimaan" class="form-input">
                    <option value="">Semua Status</option>

                    <option value="lengkap" @selected(request('status_penerimaan') === 'lengkap')>
                        Lengkap
                    </option>

                    <option value="belum_lengkap" @selected(request('status_penerimaan') === 'belum_lengkap')>
                        Belum Lengkap
                    </option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Status Pembayaran
                </label>

                <select name="status_pembayaran" class="form-input">
                    <option value="">Semua Status</option>
                    <option value="lunas" @selected(request('status_pembayaran') === 'lunas')>
                        Lunas
                    </option>
                    <option value="belum_lunas" @selected(request('status_pembayaran') === 'belum_lunas')>
                        Belum Lunas
                    </option>
                </select>
            </div>
        </div>
        
        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            @if(request()->anyFilled(['cari', 'supplier_id', 'tanggal_mulai', 'tanggal_akhir', 'status_penerimaan', 'status_pembayaran']))
                <a href="{{ route('penerimaan.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                    Clear
                </a>
            @endif
            <button type="submit" class="btn-primary py-1.5 px-4">
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Table Custom Wrapper -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
       <table class="table-custom min-w-[100rem]">
            <thead class="table-custom-header">
                <tr>
                    <tr>
                    <th scope="col" class="text-center">No</th>
                    <th scope="col" class="text-center">Aksi</th>
                    <th scope="col">No. Faktur & Tanggal Terima</th>
                    <th scope="col">Supplier</th>
                    <th scope="col" class="text-center">Status Penerimaan</th>
                    <th scope="col" class="text-right">Total Transaksi</th>
                    <th scope="col" class="text-right">Piutang</th>
                    <th scope="col" class="text-center">Tgl Jatuh Tempo</th>
                    <th scope="col" class="text-center">Status Pembayaran</th>
                </tr>
                </tr>
            </thead>
            <tbody class="table-custom-body ">
                @forelse ($penerimaans as $index => $penerimaan)
                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">

                    {{-- No --}}
                    <td class="text-center">
                        {{ $penerimaans->firstItem() + $index }}
                    </td>

                    {{-- Aksi --}}
                    <td class="text-left space-x-1.5">
                        <div class="flex items-center justify-start gap-1">

                            <button
                                type="button"
                                class="btn-secondary !p-1.5 btn-detail-penerimaan"
                                style="color: #2563EB;"
                                title="Lihat Detail"
                                data-url="{{ route('penerimaan.show', $penerimaan) }}"
                            >
                                <x-heroicon-o-eye class="w-4 h-4" />
                            </button>

                            <button
                                type="button"
                                class="btn-secondary !p-1.5 btn-edit-penerimaan"
                                style="color: #F59E0B;"
                                title="Edit"
                                data-url="{{ route('penerimaan.edit', $penerimaan) }}"
                            >
                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                            </button>

                            @if (!$penerimaan->lunas)
                                <button
                                    type="button"
                                    class="btn-secondary !p-1.5 btn-payment-penerimaan"
                                    style="color: #16A34A;"
                                    title="Pembayaran"
                                    data-id="{{ $penerimaan->id }}"
                                >
                                    <x-heroicon-o-banknotes class="w-4 h-4" />
                                </button>
                            @endif

                            @if ($penerimaan->statusPenerimaan() === 'BELUM LENGKAP')
                                <button
                                    type="button"
                                    class="btn-secondary !p-1.5 btn-susulan-penerimaan"
                                    style="color: #7C3AED;"
                                    title="Penerimaan Susulan"
                                    data-id="{{ $penerimaan->id }}"
                                >
                                    <x-heroicon-o-arrow-path class="w-4 h-4" />
                                </button>
                            @endif

                            <a
                                href="{{ route('penerimaan.print', $penerimaan) }}"
                                class="btn-secondary !p-1.5"
                                title="Print"
                                target="_blank"
                            >
                                <x-heroicon-o-printer class="w-4 h-4" />
                            </a>

                            <form
                                action="{{ route('penerimaan.destroy', $penerimaan) }}"
                                method="POST"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn-secondary !p-1.5"
                                    style="color: #DC2626;"
                                    title="Hapus"
                                >
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </form>

                        </div>
                    </td>

                    {{-- No Faktur & Tanggal Terima --}}
                    <td>
                        <div class="font-semibold text-gray-800 font-mono">
                            {{ $penerimaan->no_faktur }}
                        </div>
                        <div class="text-sm text-gray-500 mt-0.5">
                            {{ $penerimaan->tanggal?->format('d M Y') ?? '—' }}
                        </div>
                    </td>

                    {{-- Supplier --}}
                    <td class="font-medium text-gray-800">
                        {{ $penerimaan->supplier->nama ?? '—' }}
                    </td>

                    {{-- Status Penerimaan --}}
                    <td class="text-center">
                        @if ($penerimaan->statusPenerimaan() === 'LENGKAP')
                            <span class="badge-success">Lengkap</span>
                        @else
                            <span class="badge-warning">Belum Lengkap</span>
                        @endif
                    </td>

                    {{-- Total Transaksi --}}
                    <td class="text-right font-medium">
                        Rp {{ number_format($penerimaan->totalTagihan(), 0, ',', '.') }}
                    </td>

                    {{-- Piutang --}}
                    <td class="text-right font-medium">
                        Rp {{ number_format($penerimaan->sisaTagihan(), 0, ',', '.') }}
                    </td>

                    {{-- Tanggal Jatuh Tempo --}}
                    <td class="text-center text-gray-600">
                        {{ $penerimaan->jatuh_tempo?->format('d M Y') ?? '—' }}
                    </td>

                    {{-- Status Pembayaran --}}
                    <td class="text-center">
                        @if ($penerimaan->lunas)
                            <span class="badge-success">Lunas</span>
                        @else
                            <span class="badge-warning">Belum Lunas</span>
                        @endif
                    </td>

                </tr>
                @empty
                    <tr>
                        <td colspan="9" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">
                                    @if(request()->anyFilled(['cari', 'supplier_id', 'tanggal', 'status_pembayaran']))
                                        Penerimaan Tidak Ditemukan
                                    @else
                                        Penerimaan Kosong
                                    @endif
                                </div>
                                <div class="empty-state-desc">
                                    @if(request()->anyFilled(['cari', 'supplier_id', 'tanggal', 'status_pembayaran']))
                                        Tidak ada faktur penerimaan yang cocok dengan filter kriteria Anda.
                                    @else
                                        Belum ada data faktur masuk terdaftar di sistem.
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-3">
    <div class="flex items-center gap-2 text-sm text-gray-600">
        <span>Tampilkan</span>

        <select
            name="per_page"
            class="form-input py-1.5 w-20"
            onchange="this.form.submit()"
        >
            <option value="10" @selected(request('per_page', 15) == 10)>10</option>
            <option value="15" @selected(request('per_page', 15) == 15)>15</option>
            <option value="25" @selected(request('per_page', 15) == 25)>25</option>
            <option value="50" @selected(request('per_page', 15) == 50)>50</option>
            <option value="100" @selected(request('per_page', 15) == 100)>100</option>
        </select>

        <span>data</span>
    </div>

    <div>
        {{ $penerimaans->links() }}
    </div>
</div>

<!-- Modal Detail Penerimaan -->
<div id="modal-detail-penerimaan"
    class="modal-backdrop-custom hidden"
    aria-hidden="true">

    <div class="modal-container-custom max-w-15xl"
         style="width: 95vw; max-width: 1100px;">

        <div class="modal-header-custom">
            <div>
                <h2>Detail Penerimaan</h2>
                <p class="text-caption mt-1">
                    Informasi lengkap faktur penerimaan barang.
                </p>
            </div>

            <button type="button"
                id="btn-tutup-detail"
                class="btn-secondary !p-1.5"
                title="Tutup">
                ✕
            </button>
        </div>

        <div
            id="detail-penerimaan-content"
            class="modal-body-custom overflow-y-auto"
            style="max-height: calc(100vh - 180px);"
        >
            <div class="text-center py-8 text-gray-500">
                Memuat detail...
            </div>
        </div>

    </div>
</div>


<!-- Modal Pembayaran Penerimaan -->
<div id="modal-payment-penerimaan"
    class="modal-backdrop-custom hidden"
    aria-hidden="true">

    <div class="modal-container-custom max-w-15xl"
         style="width: 95vw; max-width: 1100px;">

        <div class="modal-header-custom">
            <div>
                <h2>Pembayaran Penerimaan</h2>
                <p class="text-caption mt-1">
                    Catat pembayaran untuk faktur penerimaan.
                </p>
            </div>

            <button type="button"
                id="btn-tutup-payment"
                class="btn-secondary !p-1.5"
                title="Tutup">
                ✕
            </button>
        </div>

        <div id="payment-penerimaan-content"
             class="modal-body-custom overflow-y-auto"
             style="max-height: calc(100vh - 180px);">
            <div class="text-center py-8 text-gray-500">
                Memuat pembayaran...
            </div>
        </div>

    </div>
</div>


<!-- Modal Penerimaan Susulan -->
<div
    id="modal-susulan-penerimaan"
    class="modal-backdrop-custom hidden"
    aria-hidden="true"
>
    <div class="modal-container-custom max-w-15xl"
         style="width: 95vw; max-width: 1100px;">
        <div class="modal-header-custom">
            <div>
                <h2>Penerimaan Susulan</h2>
                <p class="text-caption mt-1">
                    Catat penerimaan barang susulan atau pembatalan kekurangan faktur.
                </p>
            </div>

            <button
                type="button"
                id="close-susulan-penerimaan"
                class="btn-secondary !p-1.5"
                title="Tutup"
            >
                ✕
            </button>
        </div>

        <div
            id="susulan-penerimaan-content"
            class="modal-body-custom overflow-y-auto"
            style="max-height: calc(100vh - 180px);"
        >
            <div class="text-center py-8 text-gray-500">
                Memuat formulir penerimaan susulan...
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Penerimaan -->
<div id="modal-edit-penerimaan"
    class="modal-backdrop-custom hidden"
    aria-hidden="true"
>
    <div class="modal-container-custom max-w-15xl"
         style="width: 95vw; max-width: 1100px;">
        <div class="modal-header-custom">
            <div>
                <h2>Edit Penerimaan</h2>
                <p class="text-caption mt-1">
                    Ubah informasi faktur dan detail penerimaan barang.
                </p>
            </div>

            <button
                type="button"
                id="btn-tutup-edit"
                class="btn-secondary !p-1.5"
                title="Tutup"
            >
                ✕
            </button>
        </div>

        <div
            id="edit-penerimaan-content"
            class="modal-body-custom overflow-y-auto"
            style="max-height: calc(100vh - 180px);"
        >
            <div class="text-center py-8 text-gray-500">
                Memuat form edit...
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Faktur Penerimaan Baru -->
<div id="modal-tambah-penerimaan" class="modal-backdrop-custom hidden" aria-hidden="true">
    <div class="modal-container-custom max-w-15xl flex flex-col" style="width: 95vw; max-width: 1100px; max-height: calc(100vh - 60px);">
        <div class="modal-header-custom">
            <div>
                <h2>Faktur Penerimaan Barang Baru</h2>
                <p class="text-caption mt-1">
                    Catat faktur masuk obat dari supplier beserta detail expired date batch.
                </p>
            </div>

            <button type="button" id="btn-tutup-tambah" class="btn-secondary !p-1.5" title="Tutup">
                ✕
            </button>
        </div>

        <div class="modal-body-custom overflow-y-auto" style="max-height: calc(100vh - 180px);">
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
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">
                            Tanggal Faktur <span class="text-red-500 font-bold">*</span>
                        </label>
                        <input
                            type="date"
                            name="tanggal_faktur"
                            value="{{ old('tanggal_faktur') }}"
                            required
                            class="form-input"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Tanggal Terima <span class="text-red-500 font-bold">*</span></label>
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
                            + Tambah Item Barang
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
                                <tbody id="item-rows" class="table-custom-body divide-y divide-gray-150"></tbody>
                            </table>
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 text-center py-4" id="empty-hint">Belum ada baris. Klik "+ Tambah Item Barang" untuk mulai input.</p>
                    <div class="mt-4 flex flex-col sm:flex-row justify-end gap-4 text-sm font-semibold">
                        <span>Total Belanja:</span>
                        <span id="total-faktur" class="text-blue-700 font-mono">Rp 0</span>
                    </div>
                    <div class="mt-2 flex flex-col sm:flex-row justify-end gap-4 text-sm font-semibold items-center">
                        <label for="ppn">PPN (11%)</label>
                        <input type="text" id="ppn" value="Rp 0" readonly class="form-input w-full sm:w-40 text-right font-mono bg-gray-50">
                    </div>

                    <div class="mt-2 flex flex-col sm:flex-row justify-end gap-4 text-sm font-semibold">
                        <span>Total Tagihan:</span>
                        <span id="total-tagihan" class="text-blue-700 font-mono">Rp 0</span>
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
                    <button type="button" id="btn-batal-tambah" class="btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
            <input type="number" min="1" name="items[__i__][jumlah_dipesan]" required class="form-input py-1 px-2 text-right font-mono jumlah-dipesan-field" placeholder="1">
        </td>
        <td class="px-3 py-2">
            <input type="number" min="0" name="items[__i__][jumlah_diterima]" required class="form-input py-1 px-2 text-right font-mono jumlah-diterima-field" placeholder="0">
        </td>
        <td class="px-3 py-2"><input type="text" class="form-input py-1 px-2 satuan-field bg-gray-50" readonly></td>
        <td class="px-3 py-2 text-right font-mono font-semibold subtotal-field">Rp 0</td>
        <td class="px-3 py-2 text-center">
            <button type="button" class="text-red-500 hover:text-red-700 p-1 btn-hapus-row" aria-label="Hapus baris" title="Hapus baris"><x-heroicon-o-trash class="w-4 h-4" /></button>
        </td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modal-detail-penerimaan');
    const content = document.getElementById('detail-penerimaan-content');
    const btnTutup = document.getElementById('btn-tutup-detail');

    const paymentModal = document.getElementById('modal-payment-penerimaan');
    const paymentContent = document.getElementById('payment-penerimaan-content');
    const btnTutupPayment = document.getElementById('btn-tutup-payment');

    // =========================
    // MODAL DETAIL PENERIMAAN
    // =========================
    document.querySelectorAll('.btn-detail-penerimaan').forEach(function (button) {
        button.addEventListener('click', function () {
            const url = button.dataset.url;

            modal.classList.remove('hidden');

            content.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    Memuat detail...
                </div>
            `;

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal mengambil detail penerimaan.');
                    }

                    return response.text();
                })
                .then(html => {
                    content.innerHTML = html;
                })
                .catch(error => {
                    content.innerHTML = `
                        <div class="text-center py-8 text-red-600">
                            Gagal memuat detail penerimaan.
                        </div>
                    `;

                    console.error(error);
                });
        });
    });

    // Edit Penerimaan
    document.addEventListener('click', async function (event) {
        const button = event.target.closest('.btn-edit-penerimaan');

        if (!button) {
            return;
        }

        const modal = document.getElementById('modal-edit-penerimaan');
        const content = document.getElementById('edit-penerimaan-content');
        const url = button.dataset.url;

        if (!modal || !content || !url) {
            return;
        }

        content.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                Memuat form edit...
            </div>
        `;

        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });

            if (!response.ok) {
                throw new Error('Gagal memuat form edit.');
            }

           const html = await response.text();

            content.innerHTML = html;

            content.querySelectorAll('script').forEach(function (oldScript) {
                const newScript = document.createElement('script');

                newScript.textContent = oldScript.textContent;

                document.body.appendChild(newScript);

                oldScript.remove();
            });

            if (typeof initEditPenerimaanForm === 'function') {
                initEditPenerimaanForm();
            }

        } catch (error) {
            content.innerHTML = `
                <div class="text-center py-8 text-red-600">
                    Gagal memuat form edit.
                </div>
            `;

            console.error(error);
        }
    });

    // Tutup modal Edit
    document.addEventListener('click', function (event) {
        if (event.target.closest('#btn-tutup-edit') || event.target.closest('#btn-batal-edit')) {
            const modal = document.getElementById('modal-edit-penerimaan');

            if (modal) {
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
            }
        }
    });

    // =========================
    // MODAL PEMBAYARAN
    // =========================
    document.querySelectorAll('.btn-payment-penerimaan').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = button.dataset.id;

            paymentModal.classList.remove('hidden');

            paymentContent.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    Memuat pembayaran...
                </div>
            `;

            fetch(`/penerimaan/${id}/payment-form`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal mengambil form pembayaran.');
                    }

                    return response.text();
                })
                .then(html => {
                    paymentContent.innerHTML = html;
                })
                .catch(error => {
                    paymentContent.innerHTML = `
                        <div class="text-center py-8 text-red-600">
                            Gagal memuat form pembayaran.
                        </div>
                    `;

                    console.error(error);
                });
        });
    });


     // =========================
    // MODAL SUSULAN
    // =========================
    const susulanModal = document.getElementById('modal-susulan-penerimaan');
    const susulanContent = document.getElementById('susulan-penerimaan-content');
    const closeSusulanButton = document.getElementById('close-susulan-penerimaan');

    // BUKA FORM SUSULAN
    document.querySelectorAll('.btn-susulan-penerimaan').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = button.dataset.id;

            susulanModal.classList.remove('hidden');

            susulanContent.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    Memuat formulir penerimaan susulan...
                </div>
            `;

            fetch(`/penerimaan/${id}/susulan-form`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal memuat formulir penerimaan susulan.');
                    }

                    return response.text();
                })
                .then(html => {
                    susulanContent.innerHTML = html;
                })
                .catch(error => {
                    console.error(error);

                    susulanContent.innerHTML = `
                        <div class="text-center py-8 text-red-600">
                            ${error.message}
                        </div>
                    `;
                });
        });
    });

    // TUTUP FORM SUSULAN
    if (closeSusulanButton) {
        closeSusulanButton.addEventListener('click', function () {
            susulanModal.classList.add('hidden');
            susulanContent.innerHTML = '';
        });
    }

    // SIMPAN PENERIMAAN SUSULAN
    document.addEventListener('submit', function (event) {
        if (event.target.id !== 'form-susulan-penerimaan') {
            return;
        }

        event.preventDefault();

        const form = event.target;
        const button = form.querySelector('#btn-simpan-susulan');

        if (!button || button.disabled) {
            return;
        }

        button.disabled = true;
        button.textContent = 'Menyimpan...';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(async response => {
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(
                        data.message ||
                        data.errors?.jumlah_susulan?.[0] ||
                        'Gagal menyimpan penerimaan susulan.'
                    );
                }

                return data;
            })
            .then(data => {
                susulanModal.classList.add('hidden');
                susulanContent.innerHTML = '';

                window.location.reload();
            })
            .catch(error => {
                console.error('Penerimaan susulan:', error);

                alert(error.message || 'Gagal menyimpan penerimaan susulan.');

                button.disabled = false;
                button.textContent = 'Simpan Penerimaan Susulan';
            });
    });

    // =========================
    // TUTUP MODAL DETAIL
    // =========================
    btnTutup.addEventListener('click', function () {
        modal.classList.add('hidden');
        content.innerHTML = '';
    });

    // =========================
    // TUTUP MODAL PEMBAYARAN
    // =========================
    btnTutupPayment.addEventListener('click', function () {
        paymentModal.classList.add('hidden');
        paymentContent.innerHTML = '';
    });

    // ==========================================
    // MODAL TAMBAH FAKTUR PENERIMAAN BARU
    // ==========================================
    const modalTambah = document.getElementById('modal-tambah-penerimaan');
    const btnBukaTambah = document.getElementById('btn-tambah-penerimaan');
    const btnTutupTambah = document.getElementById('btn-tutup-tambah');
    const btnBatalTambah = document.getElementById('btn-batal-tambah');

    let rowIndex = 0;
    const tbodyTambah = document.getElementById('item-rows');
    const templateTambah = document.getElementById('row-template');
    const emptyHintTambah = document.getElementById('empty-hint');
    const totalFakturTambah = document.getElementById('total-faktur');
    const ppnTambah = document.getElementById('ppn');
    const totalTagihanTambah = document.getElementById('total-tagihan');
    const oldItemsTambah = @json(old('items', []));

    function formatRupiah(value) {
        return 'Rp ' + Math.round(value).toLocaleString('id-ID');
    }

    function updateTotalTambah() {
        if (!tbodyTambah) return;
        let total = 0;
        tbodyTambah.querySelectorAll('tr').forEach(row => {
            const harga = parseFloat(row.querySelector('.harga-beli')?.value) || 0;
            const jumlah = parseInt(row.querySelector('.jumlah-diterima-field')?.value, 10) || 0;
            const subtotal = harga * jumlah;
            total += subtotal;
            const subtotalEl = row.querySelector('.subtotal-field');
            if (subtotalEl) subtotalEl.textContent = formatRupiah(subtotal);
        });
        if (totalFakturTambah) totalFakturTambah.textContent = formatRupiah(total);
        const nilaiPpn = total * 0.11;
        if (ppnTambah) ppnTambah.value = formatRupiah(nilaiPpn);
        if (totalTagihanTambah) totalTagihanTambah.textContent = formatRupiah(total + nilaiPpn);
    }

    function tambahBarisPenerimaan() {
        if (!templateTambah || !tbodyTambah) return;
        const html = templateTambah.innerHTML.replaceAll('__i__', rowIndex);
        const tempTr = document.createElement('tbody');
        tempTr.innerHTML = html;
        tbodyTambah.appendChild(tempTr.firstElementChild);

        if (oldItemsTambah[rowIndex]) {
            const item = oldItemsTambah[rowIndex];
            const row = tbodyTambah.lastElementChild;

            const bId = row.querySelector('[name$="[barang_id]"]');
            if (bId) {
                bId.value = item.barang_id || '';
                const opt = bId.selectedOptions[0];
                const bcField = row.querySelector('.barcode-field');
                const satField = row.querySelector('.satuan-field');
                if (bcField) bcField.value = opt?.dataset.barcode || '';
                if (satField) satField.value = opt?.dataset.satuan || '';
            }
            const nb = row.querySelector('[name$="[no_batch]"]');
            if (nb) nb.value = item.no_batch || '';
            const ed = row.querySelector('[name$="[expired_date]"]');
            if (ed) ed.value = item.expired_date || '';
            const hb = row.querySelector('[name$="[harga_beli]"]');
            if (hb) hb.value = item.harga_beli || '';
            const hj = row.querySelector('[name$="[harga_jual]"]');
            if (hj) hj.value = item.harga_jual || '';
            const nr = row.querySelector('[name$="[no_rak]"]');
            if (nr) nr.value = item.no_rak || '';
            const jd = row.querySelector('[name$="[jumlah_dipesan]"]');
            if (jd) jd.value = item.jumlah_dipesan || '';
            const jt = row.querySelector('[name$="[jumlah_diterima]"]');
            if (jt) jt.value = item.jumlah_diterima || '';
        }
        rowIndex++;
        if (emptyHintTambah) emptyHintTambah.style.display = 'none';
        updateTotalTambah();
    }

    if (modalTambah) {
        if (btnBukaTambah) {
            btnBukaTambah.addEventListener('click', function () {
                modalTambah.classList.remove('hidden');
                modalTambah.setAttribute('aria-hidden', 'false');
            });
        }

        function tutupModalTambah() {
            modalTambah.classList.add('hidden');
            modalTambah.setAttribute('aria-hidden', 'true');
        }

        if (btnTutupTambah) btnTutupTambah.addEventListener('click', tutupModalTambah);
        if (btnBatalTambah) btnBatalTambah.addEventListener('click', tutupModalTambah);

        modalTambah.addEventListener('click', function (e) {
            if (e.target === modalTambah) {
                tutupModalTambah();
            }
        });

        const btnTambahItem = document.getElementById('btn-tambah-item');
        if (btnTambahItem) {
            btnTambahItem.addEventListener('click', tambahBarisPenerimaan);
        }

        if (tbodyTambah) {
            tbodyTambah.addEventListener('click', function (e) {
                if (e.target.closest('.btn-hapus-row')) {
                    e.target.closest('tr').remove();
                    if (tbodyTambah.children.length === 0 && emptyHintTambah) {
                        emptyHintTambah.style.display = 'block';
                    }
                    updateTotalTambah();
                }
            });

            tbodyTambah.addEventListener('change', function (e) {
                if (e.target.classList.contains('barang-select')) {
                    const option = e.target.selectedOptions[0];
                    const row = e.target.closest('tr');
                    const bcField = row.querySelector('.barcode-field');
                    const satField = row.querySelector('.satuan-field');
                    if (bcField) bcField.value = option?.dataset.barcode || '';
                    if (satField) satField.value = option?.dataset.satuan || '';
                }
            });

            tbodyTambah.addEventListener('input', updateTotalTambah);
        }

        const supplierSelectTambah = document.getElementById('supplier_id');
        if (supplierSelectTambah) {
            supplierSelectTambah.addEventListener('change', function () {
                const telEl = document.getElementById('telepon_supplier');
                if (telEl) telEl.value = this.selectedOptions[0]?.dataset.telepon || '';
            });
            if (supplierSelectTambah.value) {
                const telEl = document.getElementById('telepon_supplier');
                if (telEl) telEl.value = supplierSelectTambah.selectedOptions[0]?.dataset.telepon || '';
            }
        }

        const formTambah = document.getElementById('form-penerimaan');
        if (formTambah) {
            formTambah.addEventListener('submit', function (e) {
                if (tbodyTambah && tbodyTambah.children.length === 0) {
                    e.preventDefault();
                    alert('Tambahkan minimal 1 baris barang.');
                }
            });
        }

        // Inisialisasi baris item
        if (oldItemsTambah && oldItemsTambah.length > 0) {
            oldItemsTambah.forEach(() => {
                tambahBarisPenerimaan();
            });
        } else {
            tambahBarisPenerimaan();
        }

        // Buka otomatis jika ada error validasi saat submit form
        @if ($errors->any())
            modalTambah.classList.remove('hidden');
            modalTambah.setAttribute('aria-hidden', 'false');
        @endif
    }
});
</script>



@endsection

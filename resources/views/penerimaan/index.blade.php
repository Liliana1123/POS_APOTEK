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
    <form method="GET" action="{{ route('penerimaan.index') }}" class="space-y-4" id="filter-form">
        @if(request()->filled('tanggal'))
            <input type="hidden" name="tanggal" value="{{ request('tanggal') }}">
        @endif
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
                        value="{{ request('tanggal_mulai', $tanggalMulai) }}"
                        class="form-input min-w-0"
                    >

                    <span class="text-sm text-gray-400">-</span>

                    <input
                        type="date"
                        name="tanggal_akhir"
                        value="{{ request('tanggal_akhir', $tanggalAkhir) }}"
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
            @if(request()->anyFilled(['cari', 'supplier_id', 'tanggal_mulai', 'tanggal_akhir', 'status_penerimaan', 'status_pembayaran', 'tanggal']))
                <a href="{{ route('penerimaan.index', request()->has('per_page') ? ['per_page' => request('per_page')] : []) }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
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
                    <th scope="col">No. Faktur dan Tanggal Terima</th>
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

                            @if ($penerimaan->canBeEdited())
                                <button
                                    type="button"
                                    class="btn-secondary !p-1.5 btn-edit-penerimaan"
                                    style="color: #F59E0B;"
                                    title="Edit Faktur dan Detail"
                                    data-url="{{ route('penerimaan.edit', $penerimaan) }}"
                                >
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </button>
                            @else
                                <button
                                    type="button"
                                    class="btn-secondary !p-1.5 opacity-40 cursor-not-allowed"
                                    style="color: #9CA3AF;"
                                    disabled
                                    title="{{ $penerimaan->alasanTidakBisaDiedit() ?? 'Penerimaan tidak dapat diedit karena sudah memiliki transaksi lanjutan.' }}"
                                >
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </button>
                            @endif

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
                            {{ $penerimaan->tanggal?->translatedFormat('d F Y') ?? '-' }}
                        </div>
                    </td>

                    {{-- Supplier --}}
                    <td class="font-medium text-gray-800">
                        {{ $penerimaan->supplier->nama ?? '-' }}
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
                        {{ $penerimaan->jatuh_tempo?->translatedFormat('d F Y') ?? '-' }}
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
                                    @if(request()->anyFilled(['cari', 'supplier_id', 'tanggal', 'tanggal_mulai', 'tanggal_akhir', 'status_pembayaran', 'status_penerimaan']))
                                        Penerimaan Tidak Ditemukan
                                    @else
                                        Penerimaan Kosong
                                    @endif
                                </div>
                                <div class="empty-state-desc">
                                    @if(request()->anyFilled(['cari', 'supplier_id', 'tanggal', 'tanggal_mulai', 'tanggal_akhir', 'status_pembayaran', 'status_penerimaan']))
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
            form="filter-form"
            onchange="document.getElementById('filter-form').submit()"
        >
            <option value="10" @selected((int) request('per_page', 10) === 10)>10</option>
            <option value="25" @selected((int) request('per_page', 10) === 25)>25</option>
            <option value="50" @selected((int) request('per_page', 10) === 50)>50</option>
            <option value="100" @selected((int) request('per_page', 10) === 100)>100</option>
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

    <div class="modal-container-custom max-w-15xl modal-dialog-large">

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
                title="Tutup"
                aria-label="Tutup">
                <x-heroicon-o-x-mark class="w-4 h-4 pointer-events-none" />
            </button>
        </div>

        <div
            id="detail-penerimaan-content"
            class="modal-body-custom overflow-y-auto modal-body-scroll"
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

    <div class="modal-container-custom max-w-15xl modal-dialog-large">

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
                title="Tutup"
                aria-label="Tutup">
                <x-heroicon-o-x-mark class="w-4 h-4 pointer-events-none" />
            </button>
        </div>

        <div id="payment-penerimaan-content"
             class="modal-body-custom overflow-y-auto modal-body-scroll">
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
    <div class="modal-container-custom max-w-15xl modal-dialog-large">
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
                aria-label="Tutup"
            >
                <x-heroicon-o-x-mark class="w-4 h-4 pointer-events-none" />
            </button>
        </div>

        <div
            id="susulan-penerimaan-content"
            class="modal-body-custom overflow-y-auto modal-body-scroll"
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
    <div class="modal-container-custom max-w-15xl modal-dialog-large">
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
                aria-label="Tutup"
            >
                <x-heroicon-o-x-mark class="w-4 h-4 pointer-events-none" />
            </button>
        </div>

        <div
            id="edit-penerimaan-content"
            class="modal-body-custom overflow-y-auto modal-body-scroll"
        >
            <div class="text-center py-8 text-gray-500">
                Memuat form edit...
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Faktur Penerimaan Baru -->
<div id="modal-tambah-penerimaan" class="modal-backdrop-custom hidden" aria-hidden="true">
    <div class="modal-container-custom max-w-15xl flex flex-col modal-dialog-large" style="max-height: calc(100vh - 60px);">
        <div class="modal-header-custom">
            <div>
                <h2>Faktur Penerimaan Barang Baru</h2>
                <p class="text-caption mt-1">
                    Catat faktur masuk obat dari supplier beserta detail expired date batch.
                </p>
            </div>

            <button type="button" id="btn-tutup-tambah" class="btn-secondary !p-1.5" title="Tutup" aria-label="Tutup">
                <x-heroicon-o-x-mark class="w-4 h-4 pointer-events-none" />
            </button>
        </div>

        <div class="modal-body-custom overflow-y-auto modal-body-scroll">
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
                        <button type="button" id="btn-tambah-item-tambah" class="btn-secondary py-1 px-3 text-xs font-semibold">
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
                        <input type="number" name="pembayaran_pertama" id="pembayaran_pertama_tambah" value="{{ old('_method') ? 0 : old('pembayaran_pertama', 0) }}" min="0" step="0.01" class="form-input text-right font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Jatuh Tempo</label>
                        <input type="date" name="jatuh_tempo" id="jatuh_tempo_tambah" value="{{ old('_method') ? '' : old('jatuh_tempo') }}" class="form-input">
                    </div>
                    <div class="text-xs text-gray-500">Pembayaran pertama dicatat sebagai histori dan tidak menimpa pembayaran sebelumnya.</div>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit" id="btn-simpan-tambah" class="btn-primary">Simpan Faktur</button>
                    <button type="button" id="btn-batal-tambah" class="btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<template id="row-template-tambah">
    <tr class="item-row hover:bg-gray-50 transition-colors">
        <td class="px-3 py-2">
            <select name="items[__i__][barang_id]" required class="form-input py-1 px-2 barang-select">
                <option value="">Pilih barang</option>
                @foreach ($barangs as $barang)
                    <option value="{{ $barang->id }}" data-pabrik="{{ $barang->pabrik->nama ?? '' }}" data-satuan="{{ $barang->satuan->nama ?? '' }}" data-barcode="{{ $barang->barcode }}">{{ $barang->nama }}{{ $barang->pabrik ? ' (' . $barang->pabrik->nama . ')' : '' }}{{ $barang->barcode ? ' - ' . $barang->barcode : '' }}</option>
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
            <input type="date" name="items[__i__][expired_date]" required class="form-input py-1 px-2 expired-field" style="min-width: 130px;">
        </td>
        <td class="px-3 py-2"><input type="number" step="0.01" min="0" name="items[__i__][harga_beli]" required class="form-input py-1 px-2 text-right font-mono harga-beli" placeholder="0" style="min-width: 120px;"></td>
        <td class="px-3 py-2"><input type="number" step="0.01" min="0" name="items[__i__][harga_jual]" required class="form-input py-1 px-2 text-right font-mono harga-jual" placeholder="0" style="min-width: 120px;"></td>
        <td class="px-3 py-2"><input type="text" name="items[__i__][no_rak]" required class="form-input py-1 px-2 font-mono" placeholder="A-01"></td>
        <td class="px-3 py-2">
            <input type="number" min="0" name="items[__i__][jumlah_dipesan]" required class="form-input py-1 px-2 text-right font-mono jumlah-dipesan-field" placeholder="1">
        </td>
        <td class="px-3 py-2">
            <input type="number" min="0" name="items[__i__][jumlah_diterima]" required class="form-input py-1 px-2 text-right font-mono jumlah-diterima-field" placeholder="0">
        </td>
        <td class="px-3 py-2"><input type="text" class="form-input py-1 px-2 satuan-field bg-gray-50" readonly></td>
        <td class="px-3 py-2 text-right font-mono font-semibold subtotal-field">Rp 0</td>
        <td class="px-3 py-2 text-center">
            <button type="button" class="text-red-500 hover:text-red-700 p-1 btn-hapus-row-tambah" aria-label="Hapus baris" title="Hapus baris"><x-heroicon-o-trash class="w-4 h-4" /></button>
        </td>
    </tr>
</template>

{{-- Data untuk halaman penerimaan (dibaca oleh resources/js/pages/penerimaan-index.js) --}}
<script type="application/json" id="penerimaan-old-items">
    @json(old("_method") ? [] : old("items", []))
</script>
<script type="application/json" id="penerimaan-server-errors">
    @json((($errors->any() && !old('_method')) ? $errors->toArray() : []))
</script>

@vite('resources/js/pages/penerimaan-index.js')



@endsection

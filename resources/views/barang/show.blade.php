@extends('layouts.app')
@section('title', 'Detail Barang: ' . $barang->nama)

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2">
            <a href="{{ route('barang.index') }}" class="text-gray-400 hover:text-gray-600 transition" title="Kembali ke Daftar">
                <x-heroicon-o-arrow-left class="w-5 h-5" />
            </a>
            <h1>Detail Barang</h1>
        </div>
        <p class="text-caption mt-1">Rincian informasi lengkap identitas, klasifikasi, inventori, dan status obat.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('barang.edit', $barang) }}" class="btn-primary flex items-center gap-1.5">
            <x-heroicon-o-pencil-square class="w-4 h-4" />
            <span>Edit Barang</span>
        </a>
        <a href="{{ route('barang.index') }}" class="btn-secondary">
            Kembali
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl">
    <!-- Card: Identitas Barang -->
    <div class="card-base p-5 space-y-4">
        <div class="flex items-center justify-between border-b pb-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">Identitas Barang</h3>
            <span class="text-[10px] bg-gray-100 text-gray-500 font-mono px-2 py-0.5 rounded">ID: {{ $barang->id }}</span>
        </div>
        
        <dl class="divide-y divide-gray-100 text-xs">
            <div class="py-2.5 grid grid-cols-3 gap-2">
                <dt class="font-semibold text-gray-500">Kode Apotek</dt>
                <dd class="col-span-2 text-gray-800">{{ $barang->kode_apotek ?? '—' }}</dd>
            </div>
            <div class="py-2.5 grid grid-cols-3 gap-2">
                <dt class="font-semibold text-gray-500">Kode KFA</dt>
                <dd class="col-span-2 font-mono text-gray-800">{{ $barang->kode_kfa ?? '—' }}</dd>
            </div>
            <div class="py-2.5 grid grid-cols-3 gap-2">
                <dt class="font-semibold text-gray-500">Nama Barang</dt>
                <dd class="col-span-2 font-semibold text-gray-900">{{ $barang->nama }}</dd>
            </div>
            <div class="py-2.5 grid grid-cols-3 gap-2">
                <dt class="font-semibold text-gray-500">Merk</dt>
                <dd class="col-span-2 text-gray-800">{{ $barang->merk ?? '—' }}</dd>
            </div>
            <div class="py-2.5 grid grid-cols-3 gap-2">
                <dt class="font-semibold text-gray-500">Barcode</dt>
                <dd class="col-span-2 font-mono text-gray-800">{{ $barang->barcode ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    <!-- Card: Data Master -->
    <div class="card-base p-5 space-y-4">
        <div class="border-b pb-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">Data Master & Klasifikasi</h3>
        </div>
        
        <dl class="divide-y divide-gray-100 text-xs">
            <div class="py-2.5 grid grid-cols-3 gap-2">
                <dt class="font-semibold text-gray-500">Kategori</dt>
                <dd class="col-span-2 font-medium text-gray-800">{{ $barang->kategori->nama ?? '—' }}</dd>
            </div>
            <div class="py-2.5 grid grid-cols-3 gap-2">
                <dt class="font-semibold text-gray-500">Satuan</dt>
                <dd class="col-span-2 font-medium text-gray-800">{{ $barang->satuan->nama ?? '—' }}</dd>
            </div>
            <div class="py-2.5 grid grid-cols-3 gap-2">
                <dt class="font-semibold text-gray-500">Pabrik / Produsen</dt>
                <dd class="col-span-2 font-medium text-gray-800">{{ $barang->pabrik->nama ?? '—' }}</dd>
            </div>
            <div class="py-2.5 grid grid-cols-3 gap-2">
                <dt class="font-semibold text-gray-500">Supplier</dt>
                <dd class="col-span-2 font-medium text-gray-800">{{ $supplierNama }}</dd>
            </div>
        </dl>
    </div>

    <!-- Card: Stok & Inventori -->
    <div class="card-base p-5 space-y-4">
        <div class="border-b pb-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">Stok & Inventori</h3>
        </div>
        
        <dl class="divide-y divide-gray-100 text-xs">
            <div class="py-2.5 grid grid-cols-3 gap-2">
                <dt class="font-semibold text-gray-500">Stok Saat Ini</dt>
                <dd class="col-span-2 font-bold text-gray-900 text-sm">{{ $stokTotal }} {{ $barang->satuan->nama ?? 'Unit' }}</dd>
            </div>
            <div class="py-2.5 grid grid-cols-3 gap-2">
                <dt class="font-semibold text-gray-500">Stok Minimum</dt>
                <dd class="col-span-2 text-gray-800">{{ $barang->stok_minimum }} {{ $barang->satuan->nama ?? 'Unit' }}</dd>
            </div>
            <div class="py-2.5 grid grid-cols-3 gap-2">
                <dt class="font-semibold text-gray-500">Status Stok</dt>
                <dd class="col-span-2">
                    <span class="{{ $statusStokBadge }}">{{ $statusStok }}</span>
                </dd>
            </div>
        </dl>
    </div>

    <!-- Card: Ketentuan Lainnya -->
    <div class="card-base p-5 space-y-4">
        <div class="border-b pb-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">Ketentuan & Status</h3>
        </div>
        
        <dl class="divide-y divide-gray-100 text-xs">
            <div class="py-2.5 grid grid-cols-3 gap-2">
                <dt class="font-semibold text-gray-500">Butuh Resep</dt>
                <dd class="col-span-2">
                    @if ($barang->butuh_resep)
                        <span class="badge-danger">Wajib Resep Dokter</span>
                    @else
                        <span class="badge-neutral">Bebas (Tanpa Resep)</span>
                    @endif
                </dd>
            </div>
            <div class="py-2.5 grid grid-cols-3 gap-2">
                <dt class="font-semibold text-gray-500">Status Keaktifan</dt>
                <dd class="col-span-2">
                    @if ($barang->aktif)
                        <span class="badge-success">Aktif</span>
                    @else
                        <span class="badge-neutral">Nonaktif</span>
                    @endif
                </dd>
            </div>
        </dl>
    </div>
</div>
@endsection

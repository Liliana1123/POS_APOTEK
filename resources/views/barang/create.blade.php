@extends('layouts.app')
@section('title', 'Tambah Barang')

@section('content')
<div class="mb-6">
    <h1>Tambah Barang Baru</h1>
    <p class="text-caption mt-1">Tambah data obat, barcode, dan penggolongan minimum stok baru.</p>
</div>

<div class="card-base max-w-lg">
    <form action="{{ route('barang.store') }}" method="POST" class="space-y-5">
        @csrf

        <!-- Section: Identitas Barang -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 border-b pb-1.5">Identitas Barang</h3>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Nama Barang / Obat <span class="text-red-500 font-bold">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" required class="form-input" placeholder="Masukkan nama barang...">
                @error('nama') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Section: Klasifikasi -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 border-b pb-1.5">Klasifikasi</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Kategori <span class="text-red-500 font-bold">*</span></label>
                    <select name="kategori_id" required class="form-input">
                        <option value="">Pilih Kategori</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" @selected(old('kategori_id') == $kategori->id)>{{ $kategori->nama }}</option>
                        @endforeach
                    </select>
                    @error('kategori_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Satuan <span class="text-red-500 font-bold">*</span></label>
                    <select name="satuan_id" required class="form-input">
                        <option value="">Pilih Satuan</option>
                        @foreach ($satuans as $satuan)
                            <option value="{{ $satuan->id }}" @selected(old('satuan_id') == $satuan->id)>{{ $satuan->nama }}</option>
                        @endforeach
                    </select>
                    @error('satuan_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Pabrik <span class="text-red-500 font-bold">*</span></label>
                    <select name="pabrik_id" required class="form-input">
                        <option value="">Pilih Pabrik</option>
                        @foreach ($pabriks as $pabrik)
                            <option value="{{ $pabrik->id }}" @selected(old('pabrik_id') == $pabrik->id)>{{ $pabrik->nama }}</option>
                        @endforeach
                    </select>
                    @error('pabrik_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Section: Parameter Operasional & Status -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 border-b pb-1.5">Inventori & Status</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Barcode (Opsional)</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}" class="form-input" placeholder="Scan / ketik barcode...">
                    @error('barcode') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Stok Minimum <span class="text-red-500 font-bold">*</span></label>
                    <input type="number" name="stok_minimum" value="{{ old('stok_minimum', 0) }}" min="0" required class="form-input" placeholder="Batas minimum alert...">
                    @error('stok_minimum') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center text-xs font-medium text-gray-700 cursor-pointer">
                    <input type="checkbox" name="butuh_resep" value="1" @checked(old('butuh_resep')) class="mr-2.5 rounded border-gray-300 focus:ring-blue-500 w-4 h-4 text-blue-600">
                    <div>
                        <span class="font-semibold">Obat wajib pakai resep dokter</span>
                        <p class="text-[10px] text-gray-400 mt-0.5">Menandai bahwa obat memerlukan resep dokter saat penjualan di kasir.</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-2 pt-4 border-t border-gray-100">
            <button type="submit" class="btn-primary">Simpan</button>
            <a href="{{ route('barang.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

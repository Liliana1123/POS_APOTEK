@extends('layouts.app')
@section('title', 'Edit Barang')

@section('content')
<div class="mb-6">
    <h1>Edit Barang / Obat</h1>
    <p class="text-caption mt-1">Ubah rincian obat, barcode, status keaktifan, dan minimum stok.</p>
</div>

<div class="card-base max-w-lg">
    <form action="{{ route('barang.update', $barang) }}" method="POST" class="space-y-5">
        @csrf @method('PUT')

        <!-- Section: Identitas Barang -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 border-b pb-1.5">Identitas Barang</h3>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Nama Barang / Obat <span class="text-red-500 font-bold">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $barang->nama) }}" required class="form-input" placeholder="Masukkan nama barang...">
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
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" @selected(old('kategori_id', $barang->kategori_id) == $kategori->id)>{{ $kategori->nama }}</option>
                        @endforeach
                    </select>
                    @error('kategori_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Satuan <span class="text-red-500 font-bold">*</span></label>
                    <select name="satuan_id" required class="form-input">
                        @foreach ($satuans as $satuan)
                            <option value="{{ $satuan->id }}" @selected(old('satuan_id', $barang->satuan_id) == $satuan->id)>{{ $satuan->nama }}</option>
                        @endforeach
                    </select>
                    @error('satuan_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Pabrik <span class="text-red-500 font-bold">*</span></label>
                    <select name="pabrik_id" required class="form-input">
                        @foreach ($pabriks as $pabrik)
                            <option value="{{ $pabrik->id }}" @selected(old('pabrik_id', $barang->pabrik_id) == $pabrik->id)>{{ $pabrik->nama }}</option>
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
                    <input type="text" name="barcode" value="{{ old('barcode', $barang->barcode) }}" class="form-input" placeholder="Scan / ketik barcode...">
                    @error('barcode') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Stok Minimum <span class="text-red-500 font-bold">*</span></label>
                    <input type="number" name="stok_minimum" value="{{ old('stok_minimum', $barang->stok_minimum) }}" min="0" required class="form-input" placeholder="Batas minimum alert...">
                    @error('stok_minimum') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-2 space-y-3">
                <label class="flex items-start text-xs font-medium text-gray-700 cursor-pointer">
                    <input type="checkbox" name="butuh_resep" value="1" @checked(old('butuh_resep', $barang->butuh_resep)) class="mr-2.5 rounded border-gray-300 focus:ring-blue-500 w-4 h-4 text-blue-600 mt-0.5">
                    <div>
                        <span class="font-semibold">Obat wajib pakai resep dokter</span>
                        <p class="text-[10px] text-gray-400 mt-0.5">Menandai bahwa obat memerlukan resep dokter saat penjualan di kasir.</p>
                    </div>
                </label>

                <label class="flex items-start text-xs font-medium text-gray-700 cursor-pointer">
                    <input type="checkbox" name="aktif" value="1" @checked(old('aktif', $barang->aktif)) class="mr-2.5 rounded border-gray-300 focus:ring-blue-500 w-4 h-4 text-blue-600 mt-0.5">
                    <div>
                        <span class="font-semibold">Barang aktif</span>
                        <p class="text-[10px] text-gray-400 mt-0.5">Menandai apakah produk obat ini tampil dan dapat digunakan untuk transaksi baru.</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-2 pt-4 border-t border-gray-100">
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
            <a href="{{ route('barang.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Pengaturan Apotek')

@section('content')
<!-- Page Header -->
<x-page-header title="Pengaturan Apotek" subtitle="Identitas apotek yang dipakai di menu dan dokumen cetak.">
    <a href="{{ route('dashboard') }}" class="btn-secondary">Kembali</a>
</x-page-header>

<div class="card-base max-w-3xl">
    <form action="{{ route('pengaturan.update') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        @method('PUT')

        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Nama Apotek <span class="text-red-500 font-bold">*</span></label>
            <input type="text" name="nama_apotek" value="{{ old('nama_apotek', $apotek->nama_apotek) }}" required class="form-input">
            @error('nama_apotek') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Alamat</label>
            <textarea name="alamat" rows="3" class="form-input">{{ old('alamat', $apotek->alamat) }}</textarea>
            @error('alamat') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Telepon</label>
            <input type="text" name="telepon" value="{{ old('telepon', $apotek->telepon) }}" class="form-input">
            @error('telepon') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Email</label>
            <input type="email" name="email" value="{{ old('email', $apotek->email) }}" class="form-input">
            @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">No. Izin SIA</label>
            <input type="text" name="no_izin_sia" value="{{ old('no_izin_sia', $apotek->no_izin_sia) }}" class="form-input">
            @error('no_izin_sia') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Nama Apoteker Penanggung Jawab</label>
            <input type="text" name="nama_apoteker_pj" value="{{ old('nama_apoteker_pj', $apotek->nama_apoteker_pj) }}" class="form-input">
            @error('nama_apoteker_pj') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">No. SIPA</label>
            <input type="text" name="no_sipa" value="{{ old('no_sipa', $apotek->no_sipa) }}" class="form-input">
            @error('no_sipa') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Logo Apotek</label>
            <input type="file" name="logo" accept="image/*" class="form-input">
            @error('logo') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            <p class="text-xs text-gray-500 mt-1">Maksimal 2 MB, format gambar. Kosongkan jika tidak ingin mengubah.</p>
            @if($apotek->logo)
                <div class="mt-3 flex items-center gap-3">
                    <img src="{{ asset('storage/' . $apotek->logo) }}" alt="Logo apotek" class="w-16 h-16 object-contain rounded-lg border border-gray-200 bg-white p-1">
                    <span class="text-xs text-gray-500">Logo saat ini</span>
                </div>
            @endif
        </div>

        <div class="md:col-span-2 flex gap-2 pt-2 border-t border-gray-100">
            <button type="submit" class="btn-primary">Simpan Pengaturan</button>
        </div>
    </form>
</div>
@endsection
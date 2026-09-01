@extends('layouts.app')
@section('title', 'Tambah Kategori')

@section('content')
<div class="mb-6">
    <h1>Tambah Kategori Baru</h1>
    <p class="text-caption mt-1">Tambah jenis penggolongan/kategori obat baru.</p>
</div>

<div class="card-base max-w-md">
    <form action="{{ route('kategori.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Nama Kategori <span class="text-red-500 font-bold">*</span></label>
            <input type="text" name="nama" value="{{ old('nama') }}" required class="form-input">
            @error('nama') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-2 pt-2 border-t border-gray-100">
            <button type="submit" class="btn-primary">Simpan</button>
            <a href="{{ route('kategori.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

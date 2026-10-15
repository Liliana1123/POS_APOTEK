@extends('layouts.app')
@section('title', 'Edit Kategori')

@section('content')
<div class="mb-6">
    <h1>Edit Kategori</h1>
    <p class="text-caption mt-1">Ubah rincian nama kategori penggolongan obat.</p>
</div>

<div class="card-base max-w-md">
    <form action="{{ route('kategori.update', $kategori) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Nama Kategori <span class="text-red-500 font-bold">*</span></label>
            <input type="text" name="nama" value="{{ old('nama', $kategori->nama) }}" required class="form-input">
            @error('nama') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-2 pt-2 border-t border-gray-100">
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
            <a href="{{ route('kategori.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

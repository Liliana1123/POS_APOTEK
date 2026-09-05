@extends('layouts.app')
@section('title', 'Tambah Pelanggan')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1>Tambah Member Baru</h1>
        <p class="text-caption mt-1">Tambahkan Member baru ke sistem POS Apotek.</p>
    </div>
    <a href="{{ route('pelanggan.index') }}" class="btn-secondary py-2 px-4 shrink-0">
        &larr; Kembali
    </a>
</div>

<form action="{{ route('pelanggan.store') }}" method="POST" class="card-base p-6 max-w-md space-y-4">
    @csrf

    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Nama Pelanggan <span class="text-red-500 font-bold">*</span></label>
        <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Ketik nama pelanggan..." class="form-input">
        @error('nama') <p class="text-red-600 text-[10px] mt-1 font-sans">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Nomor HP / Telepon <span class="text-red-500 font-bold">*</span></label>
        <input type="text" name="telepon" value="{{ old('telepon') }}" required placeholder="Contoh: 08123456789" class="form-input">
        @error('telepon') <p class="text-red-600 text-[10px] mt-1 font-sans">{{ $message }}</p> @enderror
    </div>

    <div class="flex gap-2 border-t pt-4">
        <button type="submit" class="btn-primary py-2 px-6">Simpan</button>
        <a href="{{ route('pelanggan.index') }}" class="btn-secondary py-2 px-4 flex items-center justify-center">Batal</a>
    </div>
</form>
@endsection

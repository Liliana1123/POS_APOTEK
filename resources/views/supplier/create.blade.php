@extends('layouts.app')
@section('title', 'Tambah Supplier')

@section('content')
<div class="mb-6">
    <h1>Tambah Supplier Baru</h1>
    <p class="text-caption mt-1">Tambah distributor/supplier rekanan baru.</p>
</div>

<div class="card-base max-w-md">
    <form action="{{ route('supplier.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Nama Supplier <span class="text-red-500 font-bold">*</span></label>
            <input type="text" name="nama" value="{{ old('nama') }}" required class="form-input">
            @error('nama') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Telepon</label>
            <input type="text" name="telepon" value="{{ old('telepon') }}" class="form-input">
            @error('telepon') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Alamat</label>
            <textarea name="alamat" rows="3" class="form-input">{{ old('alamat') }}</textarea>
            @error('alamat') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-2 pt-2 border-t border-gray-100">
            <button type="submit" class="btn-primary">Simpan</button>
            <a href="{{ route('supplier.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

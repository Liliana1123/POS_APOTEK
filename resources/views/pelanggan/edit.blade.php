@extends('layouts.app')
@section('title', 'Edit Pelanggan')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1>Edit Membership</h1>
        <p class="text-caption mt-1">Ubah data profil member.</p>
    </div>
    <a href="{{ route('pelanggan.index') }}" class="btn-secondary py-2 px-4 shrink-0">
        &larr; Kembali
    </a>
</div>

<form action="{{ route('pelanggan.update', $pelanggan) }}" method="POST" class="card-base p-6 max-w-md space-y-4">
    @csrf @method('PUT')

    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Nama Membership <span class="text-red-500 font-bold">*</span></label>
        <input type="text" name="nama" value="{{ old('nama', $pelanggan->nama) }}" required placeholder="Ketik nama membership..." class="form-input">
        @error('nama') <p class="text-red-600 text-[10px] mt-1 font-sans">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Nomor HP / Telepon <span class="text-red-500 font-bold">*</span></label>
        <input type="text" name="telepon" value="{{ old('telepon', $pelanggan->telepon) }}" placeholder="Contoh: 08123456789" class="form-input">
        @error('telepon') <p class="text-red-600 text-[10px] mt-1 font-sans">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Alamat <span class="text-red-500 font-bold">*</span></label>
        <textarea name="alamat" required class="form-input" rows="3">{{ old('alamat', $pelanggan->alamat) }}</textarea>
        @error('alamat') <p class="text-red-600 text-[10px] mt-1 font-sans">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', optional($pelanggan->tanggal_lahir)->format('Y-m-d')) }}" class="form-input">
        @error('tanggal_lahir') <p class="text-red-600 text-[10px] mt-1 font-sans">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Keterangan</label>
        <textarea name="keterangan" class="form-input" rows="3">{{ old('keterangan', $pelanggan->keterangan) }}</textarea>
        @error('keterangan') <p class="text-red-600 text-[10px] mt-1 font-sans">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-400 mb-1.5 font-sans">Member ID (Permanen)</label>
        <input type="text" value="{{ $pelanggan->member_id }}" disabled readonly class="form-input bg-gray-50 font-mono text-gray-400 border-gray-200">
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-400 mb-1.5 font-sans">Status Member</label>
        <div class="form-input bg-gray-50 text-gray-500">Member Aktif</div>
    </div>

    <div class="flex gap-2 border-t pt-4">
        <button type="submit" class="btn-primary py-2 px-6">Simpan Perubahan</button>
        <a href="{{ route('pelanggan.index') }}" class="btn-secondary py-2 px-4 flex items-center justify-center">Batal</a>
    </div>
</form>
@endsection

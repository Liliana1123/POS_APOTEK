@extends('layouts.app')
@section('title', 'Tambah Pelanggan')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Page Header -->
    <x-page-header title="Tambah Member Baru" subtitle="Tambahkan Member baru ke sistem POS Apotek.">
        <a href="{{ route('pelanggan.index') }}" class="btn-secondary">
            &larr; Kembali
        </a>
    </x-page-header>

    <form action="{{ route('pelanggan.store') }}" method="POST" class="card-base p-6 space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Nama Pelanggan <span class="text-red-500 font-bold">*</span></label>
            <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Ketik nama pelanggan..." class="form-input">
            @error('nama') <p class="text-red-600 text-xs mt-1 font-sans">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Nomor HP / Telepon <span class="text-red-500 font-bold">*</span></label>
            <input type="text" name="telepon" value="{{ old('telepon') }}" required placeholder="Contoh: 08123456789" class="form-input">
            @error('telepon') <p class="text-red-600 text-xs mt-1 font-sans">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Alamat <span class="text-red-500 font-bold">*</span></label>
            <textarea name="alamat" required class="form-input" rows="3" placeholder="Alamat lengkap...">{{ old('alamat') }}</textarea>
            @error('alamat') <p class="text-red-600 text-xs mt-1 font-sans">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="form-input">
            @error('tanggal_lahir') <p class="text-red-600 text-xs mt-1 font-sans">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="status_member" class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">
                Status Member <span class="text-red-500 font-bold">*</span>
            </label>
            <select id="status_member" name="status_member" required class="form-input">
                <option value="">Pilih status member</option>
                <option value="Member Pelanggan Tetap" @selected(old('status_member') === 'Member Pelanggan Tetap')>Member Pelanggan Tetap</option>
                <option value="Member Keluarga Nakes" @selected(old('status_member') === 'Member Keluarga Nakes')>Member Keluarga Nakes</option>
                <option value="Member Only" @selected(old('status_member') === 'Member Only')>Member Only</option>
            </select>
            @error('status_member') <p class="text-red-600 text-xs mt-1 font-sans">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="custom_discount_percentage" class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">
                Custom Diskon (%)
            </label>
            <input type="number" id="custom_discount_percentage" name="custom_discount_percentage" value="{{ old('custom_discount_percentage') }}" min="0" max="100" step="any" placeholder="Kosongkan untuk diskon default 10%" class="form-input">
            <p class="text-[11px] text-gray-400 mt-1 font-sans">Kosongkan untuk menggunakan diskon default 10%.</p>
            @error('custom_discount_percentage') <p class="text-red-600 text-xs mt-1 font-sans">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-2 border-t border-gray-100 pt-4 mt-6">
            <a href="{{ route('pelanggan.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan</button>
        </div>
    </form>
</div>
@endsection

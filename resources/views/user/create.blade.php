@extends('layouts.app')
@section('title', 'Tambah User')

@section('content')
<div class="mb-6">
    <h1>Tambah User Baru</h1>
    <p class="text-caption mt-1">Buat akun pengguna baru untuk sistem POS Apotek.</p>
</div>

<div class="card-base max-w-2xl">
    <form action="{{ route('user.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Nama <span class="text-red-500 font-bold">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required class="form-input">
            @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Email <span class="text-red-500 font-bold">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required class="form-input">
            @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Role <span class="text-red-500 font-bold">*</span></label>
            @php $roles = config('permission.roles', []); @endphp
            <select name="role" required class="form-input">
                <option value="">Pilih role</option>
                @foreach ($roles as $slug => $meta)
                    @if (($meta['kunci'] ?? false) && ! auth()->user()->isSuperAdmin())
                        @continue
                    @endif
                    <option value="{{ $slug }}" {{ old('role') === $slug ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                @endforeach
            </select>
            @error('role') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Password <span class="text-red-500 font-bold">*</span></label>
            <input type="password" name="password" required class="form-input" placeholder="Minimal 8 karakter">
            @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-2 pt-2 border-t border-gray-100">
            <button type="submit" class="btn-primary">Simpan</button>
            <a href="{{ route('user.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
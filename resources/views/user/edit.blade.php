@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="mb-6">
    <h1>Edit User</h1>
    <p class="text-caption mt-1">Ubah rincian akun pengguna. Status aktif diatur lewat halaman daftar user.</p>
</div>

<div class="card-base max-w-2xl">
    <form action="{{ route('user.update', $user) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Nama <span class="text-red-500 font-bold">*</span></label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-input">
            @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Email <span class="text-red-500 font-bold">*</span></label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-input">
            @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Role <span class="text-red-500 font-bold">*</span></label>
            @php $roles = config('permission.roles', []); @endphp
            <select name="role" required class="form-input">
                @foreach ($roles as $slug => $meta)
                    @if (($meta['kunci'] ?? false) && ! auth()->user()->isSuperAdmin())
                        @continue
                    @endif
                    <option value="{{ $slug }}" {{ old('role', $user->role) === $slug ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                @endforeach
            </select>
            @error('role') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Password</label>
            <input type="password" name="password" class="form-input" placeholder="Kosongkan jika tidak ingin mengubah password">
            @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-2 pt-2 border-t border-gray-100">
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
            <a href="{{ route('user.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
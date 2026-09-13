@extends('layouts.app')
@section('title', 'Manajemen User')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <h1>Manajemen User</h1>
        <p class="text-caption mt-1">Kelola akun pengguna sistem: admin, apoteker, kasir.</p>
    </div>
    <button type="button" id="btn-tambah-user" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Tambah User</span>
    </button>
</div>

<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('user.index') }}" class="flex flex-wrap gap-2 items-center">
        <div class="relative shrink-0 w-full sm:w-64">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau email..."
                class="form-input pr-8">
            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                <x-heroicon-o-magnifying-glass class="w-4 h-4" />
            </span>
        </div>
        <button type="submit" class="btn-primary py-1.5 px-4">Cari</button>
        @if(request()->filled('cari'))
            <a href="{{ route('user.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">Clear</a>
        @endif
    </form>
</div>

<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[50rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="text-center w-36">Aksi</th>
                    <th scope="col" class="w-16">ID</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Email</th>
                    <th scope="col" class="w-40">Role</th>
                    <th scope="col" class="w-36">Status</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($users as $index => $user)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left">
                            <div class="flex items-center justify-start gap-1">
                                <button type="button"
                                    class="btn-secondary !p-1.5 btn-edit-user"
                                    style="color: #F59E0B;"
                                    title="Edit"
                                    data-id="{{ $user->id }}"
                                    data-json="{{ json_encode(['name' => $user->name, 'email' => $user->email, 'role' => $user->role], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) }}">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </button>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('user.toggle', $user) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="btn-secondary !p-1.5"
                                            style="color: {{ $user->aktif ? '#DC2626' : '#16A34A' }};"
                                            title="{{ $user->aktif ? 'Nonaktifkan' : 'Aktifkan' }}"
                                            aria-label="{{ $user->aktif ? 'Nonaktifkan' : 'Aktifkan' }}"
                                            onclick="return confirm('Yakin ingin {{ $user->aktif ? 'menonaktifkan' : 'mengaktifkan kembali' }} user ini?')">
                                            <x-heroicon-o-{{ $user->aktif ? 'user-circle' : 'user-plus' }} class="w-4 h-4" />
                                        </button>
                                    </form>
                                    @if(! $user->penerimaan()->exists() && ! $user->penjualan()->exists())
                                        <form action="{{ route('user.destroy', $user) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="btn-secondary !p-1.5"
                                                style="color: #DC2626;"
                                                title="Hapus"
                                                aria-label="Hapus"
                                                onclick="return confirm('Yakin ingin menghapus user ini? Tindakan ini permanen.')">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="table-num">{{ $user->id }}</td>
                        <td class="font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="text-gray-600">{{ $user->email }}</td>
                        <td>
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                @if($user->role === 'admin') bg-purple-100 text-purple-800
                                @elseif($user->role === 'apoteker') bg-blue-100 text-blue-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                {{ $user->aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">
                                    @if(request()->filled('cari')) User Tidak Ditemukan @else User Kosong @endif
                                </div>
                                <div class="empty-state-desc">
                                    @if(request()->filled('cari')) Tidak ada user yang cocok dengan kata kunci "{{ request('cari') }}". @else Belum ada user terdaftar. @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $users->links() }}</div>

<x-modal-form
    id="modal-user"
    create-title="Tambah User"
    edit-title="Edit User"
    create-url="{{ route('user.store') }}"
    update-base="{{ url('user') }}"
    create-btn="#btn-tambah-user"
    edit-btn=".btn-edit-user">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Nama <span class="text-red-500">*</span></label>
        <input type="text" name="name" required class="form-input" placeholder="Nama lengkap">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="name"></p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Email <span class="text-red-500">*</span></label>
        <input type="email" name="email" required class="form-input" placeholder="email@domain.com">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="email"></p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Role <span class="text-red-500">*</span></label>
        <select name="role" required class="form-input">
            <option value="">Pilih role</option>
            <option value="admin">Admin</option>
            <option value="apoteker">Apoteker</option>
            <option value="kasir">Kasir</option>
        </select>
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="role"></p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Password <span class="text-red-500">*</span></label>
        <input type="password" name="password" class="form-input" placeholder="Minimal 8 karakter" {{ $errors->has('password') ? '' : 'required' }}>
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="password"></p>
        <p class="text-xs text-gray-500 mt-1" id="password-hint">Minimal 8 karakter. Kosongkan jika tidak ingin mengubah (saat edit).</p>
    </div>
</x-modal-form>
@endsection
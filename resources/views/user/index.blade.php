@extends('layouts.app')
@section('title', 'Manajemen User')

@section('content')
<!-- Page Header -->
<x-page-header title="Manajemen User" subtitle="Kelola akun pengguna sistem: admin, apoteker, kasir.">
    <button type="button" id="btn-tambah-user" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Tambah User</span>
    </button>
</x-page-header>

<!-- Filter & Search Card -->
<x-card-filter :action="route('user.index')" :reset-url="route('user.index')">
    <div class="relative shrink-0 w-full sm:w-64">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau email..." class="form-input pr-8">
        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
        </span>
    </div>
</x-card-filter>

<!-- Table Custom Wrapper -->
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
                    <th scope="col" class="w-36 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($users as $index => $user)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left">
                            <x-table-action
                                edit-class="btn-edit-user"
                                :edit-id="$user->id"
                                :edit-data="['name' => $user->name, 'email' => $user->email, 'role' => $user->role]"
                                :delete-url="($user->id !== auth()->id() && ! $user->penerimaan()->exists() && ! $user->penjualan()->exists()) ? route('user.destroy', $user) : null"
                                delete-confirm="Yakin ingin menghapus user ini? Tindakan ini permanen.">
                                
                                @if($user->id !== auth()->id())
                                    <!-- Toggle Aktif / Nonaktif -->
                                    <form action="{{ route('user.toggle', $user) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="btn-secondary !p-1.5 {{ $user->aktif ? 'hover:bg-red-50 hover:border-red-300' : 'hover:bg-green-50 hover:border-green-300' }} transition-colors"
                                            style="color: {{ $user->aktif ? '#DC2626' : '#16A34A' }};"
                                            title="{{ $user->aktif ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                            aria-label="{{ $user->aktif ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                            onclick="return confirm('Yakin ingin {{ $user->aktif ? 'menonaktifkan' : 'mengaktifkan kembali' }} user ini?')">
                                            @if($user->aktif)
                                                <x-heroicon-o-user-minus class="w-4 h-4" />
                                            @else
                                                <x-heroicon-o-user-plus class="w-4 h-4" />
                                            @endif
                                        </button>
                                    </form>
                                @endif
                            </x-table-action>
                        </td>
                        <td class="table-num">{{ $user->id }}</td>
                        <td class="font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="text-gray-600">{{ $user->email }}</td>
                        <td>
                            @php
                                $roleVariant = match($user->role) {
                                    'admin' => 'info',
                                    'apoteker' => 'warning',
                                    default => 'success',
                                };
                            @endphp
                            <x-badge :variant="$roleVariant">
                                {{ ucfirst($user->role) }}
                            </x-badge>
                        </td>
                        <td class="text-center">
                            <x-badge :variant="$user->aktif ? 'success' : 'danger'">
                                {{ $user->aktif ? 'Aktif' : 'Nonaktif' }}
                            </x-badge>
                        </td>
                    </tr>
                @empty
                    <x-empty-state colspan="6" />
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
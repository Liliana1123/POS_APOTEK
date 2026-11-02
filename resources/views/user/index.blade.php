@extends('layouts.app')
@section('title', 'Manajemen User')

@section('content')
<!-- Page Header -->
<x-page-header title="Manajemen User" subtitle="Kelola akun pengguna sistem: {{ collect(config('permission.roles'))->pluck('label')->implode(', ') }}.">
    @if(auth()->user()->canManage('sistem.kelola-user'))
        <button type="button" id="btn-tambah-user" class="btn-primary flex items-center gap-2">
            <x-heroicon-o-plus class="w-4 h-4" />
            <span>Tambah User</span>
        </button>
    @endif
</x-page-header>

<!-- Filter & Search Card -->
<x-card-filter
    :action="route('user.index')"
    :reset-url="route('user.index')"
    :live-search="true"
    :has-reset="request()->filled('cari')">
    <div class="relative shrink-0 w-full sm:w-[360px]">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau email..." class="form-input pr-8">
        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
        </span>
    </div>
</x-card-filter>

<!-- Table Custom Wrapper -->
<div class="table-custom-container">
    {{-- Desktop Table View --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="table-custom min-w-[50rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="text-left w-36 pl-3">Aksi</th>
                    <th scope="col" class="w-16">ID</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Email</th>
                    <th scope="col" class="w-40">Role</th>
                    <th scope="col" class="w-36 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @php
                    $canManageUser = auth()->user()->canManage('sistem.kelola-user');
                    $isSuper = auth()->user()->isSuperAdmin();
                @endphp
                @forelse ($users as $index => $user)
                    @php
                        $canEditUser = $canManageUser && (! $user->isSuperAdmin() || $isSuper);
                        $canDeleteUser = $canManageUser && $user->id !== auth()->id() && (! $user->isSuperAdmin() || $isSuper) && ! $user->penerimaan()->exists() && ! $user->penjualan()->exists();
                        $canToggleUser = $canManageUser && $user->id !== auth()->id() && (! $user->isSuperAdmin() || $isSuper);
                    @endphp
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left pl-3">
                            <x-table-action
                                edit-class="{{ $canEditUser ? 'btn-edit-user' : '' }}"
                                :edit-id="$canEditUser ? $user->id : null"
                                :edit-data="$canEditUser ? ['name' => $user->name, 'email' => $user->email, 'role' => $user->role] : null"
                                :delete-url="$canDeleteUser ? route('user.destroy', $user) : null"
                                :delete-item="$user->name"
                                delete-confirm="Yakin ingin menghapus user ini? Tindakan ini permanen.">
                                
                                @if($canToggleUser)
                                    <!-- Toggle Aktif / Nonaktif -->
                                    <button type="button"
                                        data-confirm-action="{{ route('user.toggle', $user) }}"
                                        data-confirm-method="PATCH"
                                        data-confirm-title="{{ $user->aktif ? 'Nonaktifkan Akun User' : 'Aktifkan Akun User' }}"
                                        data-confirm-message="Apakah Anda yakin ingin {{ $user->aktif ? 'menonaktifkan akun user ini? User tidak akan dapat login sampai diaktifkan kembali.' : 'mengaktifkan kembali akun user ini agar dapat login dan mengakses sistem?' }}"
                                        data-confirm-detail="{{ $user->name }} ({{ $user->email }})"
                                        data-confirm-detail-label="User:"
                                        data-confirm-type="{{ $user->aktif ? 'warning' : 'info' }}"
                                        data-confirm-btn-text="{{ $user->aktif ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}"
                                        data-confirm-loading-text="{{ $user->aktif ? 'Menonaktifkan...' : 'Mengaktifkan...' }}"
                                        class="btn-secondary !p-1.5 {{ $user->aktif ? 'hover:bg-red-50 hover:border-red-300' : 'hover:bg-green-50 hover:border-green-300' }} transition-colors"
                                        style="color: {{ $user->aktif ? '#DC2626' : '#16A34A' }};"
                                        title="{{ $user->aktif ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                        aria-label="{{ $user->aktif ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                        @if($user->aktif)
                                            <x-heroicon-o-user-minus class="w-4 h-4" />
                                        @else
                                            <x-heroicon-o-user-plus class="w-4 h-4" />
                                        @endif
                                    </button>
                                @endif
                            </x-table-action>
                        </td>
                         <td class="table-num">{{ sprintf('%02d', $user->id) }}</td>
                        <td class="font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="text-gray-600">{{ $user->email }}</td>
                        <td>
                            <x-status-badge type="role" :status="$user->role" />
                        </td>
                        <td class="text-center">
                            <x-status-badge type="aktif" :status="$user->aktif" />
                        </td>
                    </tr>
                @empty
                    <x-empty-state colspan="6" />
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Card View --}}
    <div class="md:hidden divide-y divide-gray-150 mobile-card-list">
        @forelse ($users as $index => $user)
            @php
                $canEditUser = $canManageUser && (! $user->isSuperAdmin() || $isSuper);
                $canDeleteUser = $canManageUser && $user->id !== auth()->id() && (! $user->isSuperAdmin() || $isSuper) && ! $user->penerimaan()->exists() && ! $user->penjualan()->exists();
                $canToggleUser = $canManageUser && $user->id !== auth()->id() && (! $user->isSuperAdmin() || $isSuper);
            @endphp
            <div class="mobile-card-item p-4 space-y-2.5 bg-white">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center shrink-0 text-xs shadow-2xs">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-gray-900 text-sm leading-snug break-words">
                                {{ $user->name }}
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5 truncate">
                                {{ $user->email }}
                            </div>
                        </div>
                    </div>
                    <div class="shrink-0 flex flex-col items-end gap-1">
                        <x-status-badge type="aktif" :status="$user->aktif" />
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs py-2 px-3 bg-gray-50/80 rounded-lg border border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Role:</span>
                        <x-status-badge type="role" :status="$user->role" />
                    </div>
                    <div class="text-right">
                        <span class="font-mono text-gray-400 text-[11px]">ID #{{ sprintf('%02d', $user->id) }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-xs">
                    <span class="text-[11px] text-gray-400">Aksi Akun</span>
                    <x-table-action
                        edit-class="{{ $canEditUser ? 'btn-edit-user' : '' }}"
                        :edit-id="$canEditUser ? $user->id : null"
                        :edit-data="$canEditUser ? ['name' => $user->name, 'email' => $user->email, 'role' => $user->role] : null"
                        :delete-url="$canDeleteUser ? route('user.destroy', $user) : null"
                        :delete-item="$user->name"
                        delete-confirm="Yakin ingin menghapus user ini? Tindakan ini permanen.">
                        
                        @if($canToggleUser)
                            <button type="button"
                                data-confirm-action="{{ route('user.toggle', $user) }}"
                                data-confirm-method="PATCH"
                                data-confirm-title="{{ $user->aktif ? 'Nonaktifkan Akun User' : 'Aktifkan Akun User' }}"
                                data-confirm-message="Apakah Anda yakin ingin {{ $user->aktif ? 'menonaktifkan akun user ini? User tidak akan dapat login sampai diaktifkan kembali.' : 'mengaktifkan kembali akun user ini agar dapat login dan mengakses sistem?' }}"
                                data-confirm-detail="{{ $user->name }} ({{ $user->email }})"
                                data-confirm-detail-label="User:"
                                data-confirm-type="{{ $user->aktif ? 'warning' : 'info' }}"
                                data-confirm-btn-text="{{ $user->aktif ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}"
                                data-confirm-loading-text="{{ $user->aktif ? 'Menonaktifkan...' : 'Mengaktifkan...' }}"
                                class="btn-secondary !p-1.5 {{ $user->aktif ? 'hover:bg-red-50 hover:border-red-300' : 'hover:bg-green-50 hover:border-green-300' }} transition-colors"
                                style="color: {{ $user->aktif ? '#DC2626' : '#16A34A' }};"
                                title="{{ $user->aktif ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                aria-label="{{ $user->aktif ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                @if($user->aktif)
                                    <x-heroicon-o-user-minus class="w-4 h-4" />
                                @else
                                    <x-heroicon-o-user-plus class="w-4 h-4" />
                                @endif
                            </button>
                        @endif
                    </x-table-action>
                </div>
            </div>
        @empty
            <x-empty-state :as-card="true" />
        @endforelse
    </div>
</div>

<x-pagination :paginator="$users" />

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
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="role"></p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">
            Password <span class="text-red-500" data-create-only>*</span>
        </label>
        <div class="relative">
            <input type="password" name="password" id="modal-user-password" class="form-input pr-10" placeholder="Minimal 8 karakter">
            <button type="button" id="modal-user-password-toggle" tabindex="-1"
                class="absolute inset-y-0 right-0 flex w-10 items-center justify-center text-gray-400 hover:text-gray-600 transition-colors cursor-pointer"
                aria-label="Tampilkan password" title="Tampilkan / Sembunyikan Password">
                <x-heroicon-o-eye class="w-4 h-4 show-icon" />
                <x-heroicon-o-eye-slash class="w-4 h-4 hide-icon hidden" />
            </button>
        </div>
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="password"></p>
        <p class="text-xs text-gray-500 mt-1" id="password-hint">Minimal 8 karakter. Kosongkan jika tidak ingin mengubah (saat edit).</p>
    </div>
</x-modal-form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var pwdInput = document.getElementById('modal-user-password');
        var pwdToggle = document.getElementById('modal-user-password-toggle');
        if (!pwdInput || !pwdToggle) return;

        var showIcon = pwdToggle.querySelector('.show-icon');
        var hideIcon = pwdToggle.querySelector('.hide-icon');

        function setVisibility(show) {
            pwdInput.type = show ? 'text' : 'password';
            if (show) {
                showIcon.classList.add('hidden');
                hideIcon.classList.remove('hidden');
            } else {
                showIcon.classList.remove('hidden');
                hideIcon.classList.add('hidden');
            }
        }

        pwdToggle.addEventListener('click', function (e) {
            e.preventDefault();
            var isText = pwdInput.type === 'text';
            setVisibility(!isText);
        });

        // Reset ke masked password saat tombol Tambah User diklik
        var btnTambah = document.querySelector('#btn-tambah-user');
        if (btnTambah) {
            btnTambah.addEventListener('click', function () {
                setVisibility(false);
            });
        }

        // Reset ke masked password saat tombol Edit diklik
        document.addEventListener('click', function (e) {
            if (e.target.closest('.btn-edit-user')) {
                setVisibility(false);
            }
        });

        // Reset saat modal ditutup
        var modalEl = document.getElementById('modal-user');
        if (modalEl) {
            modalEl.querySelectorAll('.modal-user-close').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    setVisibility(false);
                });
            });
        }
    });
</script>
@endpush
@endsection
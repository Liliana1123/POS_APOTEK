@extends('layouts.app')
@section('title', 'Kategori')

@section('content')
<x-page-header title="Daftar Kategori" subtitle="Kelola tipe penggolongan/kategori obat.">
    @if(auth()->user()->canManage('master.kategori'))
        <button type="button" id="btn-tambah-kategori" class="btn-primary flex items-center gap-2">
            <x-heroicon-o-plus class="w-4 h-4" />
            <span>Tambah Kategori</span>
        </button>
    @endif
</x-page-header>

<x-card-filter
    :action="route('kategori.index')"
    :reset-url="route('kategori.index')"
    :live-search="true"
    :has-reset="request()->filled('cari')">
    <x-search-input name="cari" placeholder="Cari nama kategori..." class="shrink-0 w-full sm:w-[360px]" />
</x-card-filter>

<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[50rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="text-center w-36">Aksi</th>
                    <th scope="col" class="w-16">ID</th>
                    <th scope="col">Nama Kategori</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($kategoris as $index => $kategori)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left pl-3">
                            <x-table-action
                                edit-class="{{ auth()->user()->canManage('master.kategori') ? 'btn-edit-kategori' : '' }}"
                                :edit-id="auth()->user()->canManage('master.kategori') ? $kategori->id : null"
                                :edit-data="auth()->user()->canManage('master.kategori') ? ['nama' => $kategori->nama] : null"
                                :delete-url="auth()->user()->canManage('master.kategori') ? route('kategori.destroy', $kategori) : null"
                                delete-confirm="Yakin ingin menghapus kategori ini?"
                            />
                        </td>
                        <td class="table-num">{{ sprintf('%02d', $kategori->id) }}</td>
                        <td class="font-medium text-gray-800">{{ $kategori->nama }}</td>
                    </tr>
                @empty
                    <x-empty-state colspan="3" />
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 pagination-container">{{ $kategoris->links('vendor.pagination.apotek') }}</div>

<x-modal-form
    id="modal-kategori"
    create-title="Tambah Kategori"
    edit-title="Edit Kategori"
    create-url="{{ route('kategori.store') }}"
    update-base="{{ url('kategori') }}"
    create-btn="#btn-tambah-kategori"
    edit-btn=".btn-edit-kategori">
    <x-form.input name="nama" label="Nama Kategori" required placeholder="Masukkan nama kategori..." />
</x-modal-form>
@endsection
@extends('layouts.app')
@section('title', 'Kategori')

@section('content')
<x-page-header title="Daftar Kategori" subtitle="Kelola tipe penggolongan/kategori obat.">
    <button type="button" id="btn-tambah-kategori" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Tambah Kategori</span>
    </button>
</x-page-header>

<x-card-filter :action="route('kategori.index')" :reset-url="route('kategori.index')">
    <div class="relative shrink-0 w-full sm:w-64">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama kategori..." class="form-input pr-8">
        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
        </span>
    </div>
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
                        <td class="text-left">
                            <x-table-action
                                edit-class="btn-edit-kategori"
                                :edit-id="$kategori->id"
                                :edit-data="['nama' => $kategori->nama]"
                                :delete-url="route('kategori.destroy', $kategori)"
                                delete-confirm="Yakin ingin menghapus kategori ini?"
                            />
                        </td>
                        <td class="table-num">{{ $kategori->id }}</td>
                        <td class="font-medium text-gray-800">{{ $kategori->nama }}</td>
                    </tr>
                @empty
                    <x-empty-state colspan="3" />
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $kategoris->links() }}</div>

<x-modal-form
    id="modal-kategori"
    create-title="Tambah Kategori"
    edit-title="Edit Kategori"
    create-url="{{ route('kategori.store') }}"
    update-base="{{ url('kategori') }}"
    create-btn="#btn-tambah-kategori"
    edit-btn=".btn-edit-kategori">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Nama Kategori <span class="text-red-500">*</span></label>
        <input type="text" name="nama" required class="form-input" placeholder="Masukkan nama kategori...">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="nama"></p>
    </div>
</x-modal-form>
@endsection
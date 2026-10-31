@extends('layouts.app')
@section('title', 'Satuan')

@section('content')
<x-page-header title="Daftar Satuan" subtitle="Kelola tipe satuan kemasan barang/obat.">
    @if(auth()->user()->canManage('master.satuan'))
    <button type="button" id="btn-tambah-satuan" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Tambah Satuan</span>
    </button>
    @endif
</x-page-header>

<x-card-filter
    :action="route('satuan.index')"
    :reset-url="route('satuan.index')"
    :live-search="true"
    :has-reset="request()->filled('cari')">
    <x-search-input name="cari" placeholder="Cari satuan..." class="shrink-0 w-full sm:w-[360px]" />
</x-card-filter>

<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[50rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="text-left w-36 pl-3">Aksi</th>
                    <th scope="col" class="w-16">ID</th>
                    <th scope="col">Nama Satuan</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($satuans as $index => $satuan)
                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                    <td class="text-left pl-3">
                        <x-table-action
                            edit-class="{{ auth()->user()->canManage('master.satuan') ? 'btn-edit-satuan' : '' }}"
                            :edit-id="auth()->user()->canManage('master.satuan') ? $satuan->id : null"
                            :edit-data="auth()->user()->canManage('master.satuan') ? ['nama' => $satuan->nama] : null"
                            :delete-url="auth()->user()->canManage('master.satuan') ? route('satuan.destroy', $satuan) : null"
                            :delete-item="$satuan->nama"
                            delete-confirm="Yakin ingin menghapus satuan ini?" />
                    </td>
                    <td class="table-num">{{ sprintf('%02d', $satuan->id) }}</td>
                    <td class="font-medium text-gray-800">{{ $satuan->nama }}</td>
                </tr>
                @empty
                <x-empty-state colspan="3" />
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-pagination :paginator="$satuans" />

<x-modal-form
    id="modal-satuan"
    create-title="Tambah Satuan"
    edit-title="Edit Satuan"
    create-url="{{ route('satuan.store') }}"
    update-base="{{ url('satuan') }}"
    create-btn="#btn-tambah-satuan"
    edit-btn=".btn-edit-satuan">
    <x-form.input name="nama" label="Nama Satuan" required placeholder="Masukkan nama satuan..." />
</x-modal-form>
@endsection
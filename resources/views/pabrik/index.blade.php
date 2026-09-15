@extends('layouts.app')
@section('title', 'Pabrik')

@section('content')
<x-page-header title="Daftar Pabrik" subtitle="Kelola data pabrikan produsen obat.">
    <button type="button" id="btn-tambah-pabrik" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Tambah Pabrik</span>
    </button>
</x-page-header>

<x-card-filter :action="route('pabrik.index')" :reset-url="route('pabrik.index')">
    <div class="relative shrink-0 w-full sm:w-64">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama pabrik..." class="form-input pr-8">
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
                    <th scope="col">Nama Pabrik</th>
                    <th scope="col" class="w-40">Telepon</th>
                    <th scope="col">Alamat</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($pabriks as $index => $pabrik)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left">
                            <x-table-action
                                edit-class="btn-edit-pabrik"
                                :edit-id="$pabrik->id"
                                :edit-data="['nama' => $pabrik->nama, 'telepon' => $pabrik->telepon, 'alamat' => $pabrik->alamat]"
                                :delete-url="route('pabrik.destroy', $pabrik)"
                                delete-confirm="Yakin ingin menghapus pabrik ini?"
                            />
                        </td>
                        <td class="table-num">{{ $pabrik->id }}</td>
                        <td class="font-medium text-gray-800">{{ $pabrik->nama }}</td>
                        <td class="text-gray-600">{{ $pabrik->telepon ?? '—' }}</td>
                        <td class="text-gray-600 truncate max-w-xs" title="{{ $pabrik->alamat }}">{{ $pabrik->alamat ?? '—' }}</td>
                    </tr>
                @empty
                    <x-empty-state colspan="5" />
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $pabriks->links() }}</div>

<x-modal-form
    id="modal-pabrik"
    create-title="Tambah Pabrik"
    edit-title="Edit Pabrik"
    create-url="{{ route('pabrik.store') }}"
    update-base="{{ url('pabrik') }}"
    create-btn="#btn-tambah-pabrik"
    edit-btn=".btn-edit-pabrik">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Nama Pabrik <span class="text-red-500">*</span></label>
        <input type="text" name="nama" required class="form-input" placeholder="Masukkan nama pabrik...">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="nama"></p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">No Telepon</label>
        <input type="text" name="telepon" class="form-input" placeholder="08xxxxxxxxxx">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="telepon"></p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Alamat</label>
        <textarea name="alamat" rows="3" class="form-input" placeholder="Alamat lengkap pabrik..."></textarea>
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="alamat"></p>
    </div>
</x-modal-form>
@endsection

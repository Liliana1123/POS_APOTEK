@extends('layouts.app')
@section('title', 'Pabrik')

@section('content')
<x-page-header title="Daftar Pabrik" subtitle="Kelola data pabrikan produsen obat.">
    @if(auth()->user()->canManage('master.pabrik'))
    <button type="button" id="btn-tambah-pabrik" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Tambah Pabrik</span>
    </button>
    @endif
</x-page-header>

<x-card-filter
    :action="route('pabrik.index')"
    :reset-url="route('pabrik.index')"
    :live-search="true"
    :has-reset="request()->filled('cari')">
    <x-search-input name="cari" placeholder="Cari nama pabrik..." class="shrink-0 w-full sm:w-[360px]" />
</x-card-filter>

<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[50rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="text-left w-36 pl-3">Aksi</th>
                    <th scope="col" class="w-16">ID</th>
                    <th scope="col">Nama Pabrik</th>
                    <th scope="col" class="w-40">Telepon</th>
                    <th scope="col">Alamat</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($pabriks as $index => $pabrik)
                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                    <td class="text-left pl-3">
                        <x-table-action
                            edit-class="{{ auth()->user()->canManage('master.pabrik') ? 'btn-edit-pabrik' : '' }}"
                            :edit-id="auth()->user()->canManage('master.pabrik') ? $pabrik->id : null"
                            :edit-data="auth()->user()->canManage('master.pabrik') ? ['nama' => $pabrik->nama, 'telepon' => $pabrik->telepon, 'alamat' => $pabrik->alamat] : null"
                            :delete-url="auth()->user()->canManage('master.pabrik') ? route('pabrik.destroy', $pabrik) : null"
                            :delete-item="$pabrik->nama"
                            delete-confirm="Yakin ingin menghapus pabrik ini?" />
                    </td>
                    <td class="table-num">{{ sprintf('%02d', $pabrik->id) }}</td>
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

<x-pagination :paginator="$pabriks" />

<x-modal-form
    id="modal-pabrik"
    create-title="Tambah Pabrik"
    edit-title="Edit Pabrik"
    create-url="{{ route('pabrik.store') }}"
    update-base="{{ url('pabrik') }}"
    create-btn="#btn-tambah-pabrik"
    edit-btn=".btn-edit-pabrik">
    <x-form.input name="nama" label="Nama Pabrik" required placeholder="Masukkan nama pabrik..." />
    <x-form.input name="telepon" label="No Telepon" placeholder="08xxxxxxxxxx" />
    <x-form.textarea name="alamat" label="Alamat" rows="3" placeholder="Alamat lengkap pabrik..." />
</x-modal-form>
@endsection
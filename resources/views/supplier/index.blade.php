@extends('layouts.app')
@section('title', 'Supplier')

@section('content')
<x-page-header title="Daftar Supplier" subtitle="Kelola data penyalur/distributor obat.">
    @if(auth()->user()->canManage('master.supplier'))
        <button type="button" id="btn-tambah-supplier" class="btn-primary flex items-center gap-2">
            <x-heroicon-o-plus class="w-4 h-4" />
            <span>Tambah Supplier</span>
        </button>
    @endif
</x-page-header>

<x-card-filter
    :action="route('supplier.index')"
    :reset-url="route('supplier.index')"
    :live-search="true"
    :has-reset="request()->filled('cari')">
    <x-search-input name="cari" placeholder="Cari nama supplier..." class="shrink-0 w-full sm:w-[360px]" />
</x-card-filter>

<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[50rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="text-left w-36 pl-3">Aksi</th>
                    <th scope="col" class="w-16">ID</th>
                    <th scope="col">Nama Supplier</th>
                    <th scope="col" class="w-44">Telepon</th>
                    <th scope="col">Alamat</th>
                    <th scope="col" class="w-40">PIC</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($suppliers as $index => $supplier)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left pl-3">
                            <x-table-action
                                edit-class="{{ auth()->user()->canManage('master.supplier') ? 'btn-edit-supplier' : '' }}"
                                :edit-id="auth()->user()->canManage('master.supplier') ? $supplier->id : null"
                                :edit-data="auth()->user()->canManage('master.supplier') ? ['nama' => $supplier->nama, 'telepon' => $supplier->telepon, 'alamat' => $supplier->alamat, 'pic' => $supplier->pic] : null"
                                :delete-url="auth()->user()->canManage('master.supplier') ? route('supplier.destroy', $supplier) : null"
                                :delete-item="$supplier->nama"
                                delete-confirm="Yakin ingin menghapus supplier ini?"
                            />
                        </td>
                        <td class="table-num">{{ sprintf('%02d', $supplier->id) }}</td>
                        <td class="font-medium text-gray-800">{{ $supplier->nama }}</td>
                        <td class="text-gray-600 font-mono">{{ $supplier->telepon ?? '—' }}</td>
                        <td class="text-gray-600 truncate max-w-xs" title="{{ $supplier->alamat }}">{{ $supplier->alamat ?? '—' }}</td>
                        <td class="text-gray-600">{{ $supplier->pic ?? '—' }}</td>
                    </tr>
                @empty
                    <x-empty-state colspan="6" />
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-pagination :paginator="$suppliers" />

<x-modal-form
    id="modal-supplier"
    create-title="Tambah Supplier"
    edit-title="Edit Supplier"
    create-url="{{ route('supplier.store') }}"
    update-base="{{ url('supplier') }}"
    create-btn="#btn-tambah-supplier"
    edit-btn=".btn-edit-supplier">
    <x-form.input name="nama" label="Nama Supplier" required placeholder="Masukkan nama supplier..." />
    <x-form.input name="telepon" label="Telepon" placeholder="08xxxxxxxxxx" />
    <x-form.textarea name="alamat" label="Alamat" rows="3" placeholder="Alamat lengkap supplier..." />
    <x-form.input name="pic" label="PIC (Person In Charge)" placeholder="Nama penanggung jawab supplier..." />
</x-modal-form>
@endsection
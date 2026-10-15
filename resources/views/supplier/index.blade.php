@extends('layouts.app')
@section('title', 'Supplier')

@section('content')
<x-page-header title="Daftar Supplier" subtitle="Kelola data penyalur/distributor obat.">
    <button type="button" id="btn-tambah-supplier" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Tambah Supplier</span>
    </button>
</x-page-header>

<x-card-filter :action="route('supplier.index')" :reset-url="route('supplier.index')">
    <div class="relative shrink-0 w-full sm:w-64">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama supplier..." class="form-input pr-8">
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
                    <th scope="col">Nama Supplier</th>
                    <th scope="col" class="w-44">Telepon</th>
                    <th scope="col">Alamat</th>
                    <th scope="col" class="w-40">PIC</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($suppliers as $index => $supplier)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left">
                            <x-table-action
                                edit-class="btn-edit-supplier"
                                :edit-id="$supplier->id"
                                :edit-data="['nama' => $supplier->nama, 'telepon' => $supplier->telepon, 'alamat' => $supplier->alamat, 'pic' => $supplier->pic]"
                                :delete-url="route('supplier.destroy', $supplier)"
                                delete-confirm="Yakin ingin menghapus supplier ini?"
                            />
                        </td>
                        <td class="table-num">{{ $supplier->id }}</td>
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

<div class="mt-4">{{ $suppliers->links() }}</div>

<x-modal-form
    id="modal-supplier"
    create-title="Tambah Supplier"
    edit-title="Edit Supplier"
    create-url="{{ route('supplier.store') }}"
    update-base="{{ url('supplier') }}"
    create-btn="#btn-tambah-supplier"
    edit-btn=".btn-edit-supplier">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Nama Supplier <span class="text-red-500">*</span></label>
        <input type="text" name="nama" required class="form-input" placeholder="Masukkan nama supplier...">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="nama"></p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Telepon</label>
        <input type="text" name="telepon" class="form-input" placeholder="08xxxxxxxxxx">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="telepon"></p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Alamat</label>
        <textarea name="alamat" rows="3" class="form-input" placeholder="Alamat lengkap supplier..."></textarea>
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="alamat"></p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">PIC (Person In Charge)</label>
        <input type="text" name="pic" class="form-input" placeholder="Nama penanggung jawab supplier...">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="pic"></p>
    </div>
</x-modal-form>
@endsection
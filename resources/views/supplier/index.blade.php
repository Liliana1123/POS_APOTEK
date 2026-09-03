@extends('layouts.app')
@section('title', 'Supplier')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1>Daftar Supplier</h1>
        <p class="text-caption mt-1">Kelola data penyalur/distributor obat.</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <button type="button" id="btn-tambah-supplier" class="btn-primary flex items-center gap-2">
            <x-heroicon-o-plus class="w-4 h-4" />
            <span>Tambah Supplier</span>
        </button>
    </div>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('supplier.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Cari Supplier</label>
                <div class="relative">
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama supplier..."
                        class="form-input pr-8">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </span>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            @if(request()->filled('cari'))
                <a href="{{ route('supplier.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                    Reset
                </a>
            @endif
            <button type="submit" class="btn-primary !p-1.5" title="Filter">
                <x-heroicon-o-funnel class="w-4 h-4" />
            </button>
        </div>
    </form>
</div>

<!-- Table Custom Wrapper -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[50rem]">
<<<<<<< HEAD
        <thead class="table-custom-header">
            <tr>
                <th scope="col" class="w-32 text-center align-middle">Aksi</th>
                <th scope="col" class="w-24 text-center align-middle">No</th>
                <th scope="col" class="w-48 text-left align-middle">Nama Supplier</th>
                <th scope="col" class="w-40 text-left align-middle">Telepon</th>
                <th scope="col" class="text-left align-middle">Alamat</th>
            </tr>
        </thead>
        <tbody class="table-custom-body">
            @forelse ($suppliers as $index => $supplier)
                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                    <td class="w-32 text-center align-middle">
                        <div class="flex items-center justify-center gap-1">
                        <button type="button"
                                class="btn-secondary action-icon-button action-icon-edit !p-1.5 btn-edit-satuan"
                                style="color: #F59E0B;"
                                title="Edit"
                                data-id="{{ $supplier->id }}"
                                data-json="{{ json_encode(['nama' => $supplier->nama, 'telepon' => $supplier->telepon, 'alamat' => $supplier->alamat], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-[4px] w-[4px]"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        style="width: 16px; height: 16px; flex-shrink: 0; display: block;">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m16.862 4.487 2.651 2.651M18.5 2.5a2.121 2.121 0 1 1 3 3L7.5 18.5l-4 1 1-4L18.5 2.5Z" />
                                    </svg>
=======
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="text-center w-36">Aksi</th>
                    <th scope="col" class="w-16">No</th>
                    <th scope="col">Nama Supplier</th>
                    <th scope="col" class="w-44">Telepon</th>
                    <th scope="col">Alamat</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($suppliers as $index => $supplier)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left">
                            <div class="flex items-center justify-start gap-1">
                                <button type="button"
                                    class="btn-secondary !p-1.5 btn-edit-supplier"
                                    style="color: #F59E0B;"
                                    title="Edit"
                                    data-id="{{ $supplier->id }}"
                                    data-json="{{ json_encode(['nama' => $supplier->nama, 'telepon' => $supplier->telepon, 'alamat' => $supplier->alamat], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) }}">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
>>>>>>> risang-fitur-data-master
                                </button>
                                <form action="{{ route('supplier.destroy', $supplier) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn-secondary action-icon-button action-icon-delete !p-1.5"
                                        style="color: #DC2626;"
                                        title="Hapus"
                                        aria-label="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus supplier ini?')">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </form>
<<<<<<< HEAD
                        </div>
                    </td>
                    <td class="w-24 text-center align-middle">{{ $suppliers->firstItem() + $index }}</td>
                    <td class="w-48 font-medium text-gray-800 text-left align-middle">{{ $supplier->nama }}</td>
                    <td class="w-40 text-gray-600 font-mono text-left align-middle">{{ $supplier->telepon ?? '-' }}</td>
                    <td class="text-gray-600 text-left truncate max-w-xs align-middle" title="{{ $supplier->alamat }}">{{ $supplier->alamat ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-0">
                        <div class="empty-state-container">
                            <div class="empty-state-title">
                                @if(request()->filled('cari'))
                                    Pencarian Tidak Ditemukan
                                @else
                                    Supplier Kosong
                                @endif
=======
>>>>>>> risang-fitur-data-master
                            </div>
                        </td>
                        <td class="table-num">{{ $suppliers->firstItem() + $index }}</td>
                        <td class="font-medium text-gray-800">{{ $supplier->nama }}</td>
                        <td class="text-gray-600 font-mono">{{ $supplier->telepon ?? '—' }}</td>
                        <td class="text-gray-600 truncate max-w-xs" title="{{ $supplier->alamat }}">{{ $supplier->alamat ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">
                                    @if(request()->filled('cari'))
                                        Supplier Tidak Ditemukan
                                    @else
                                        Supplier Kosong
                                    @endif
                                </div>
                                <div class="empty-state-desc">
                                    @if(request()->filled('cari'))
                                        Tidak ada supplier yang cocok dengan kata kunci "{{ request('cari') }}".
                                    @else
                                        Belum ada data supplier terdaftar di sistem.
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
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
</x-modal-form>
@endsection

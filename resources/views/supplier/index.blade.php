@extends('layouts.app')
@section('title', 'Supplier')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <h1>Daftar Supplier</h1>
        <p class="text-caption mt-1">Kelola data penyalur/distributor obat.</p>
    </div>
    <a href="{{ route('supplier.create') }}" class="btn-primary flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
        </svg>
        <span>Tambah Supplier</span>
    </a>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('supplier.index') }}" class="flex flex-wrap gap-2 items-center">
        <div class="relative shrink-0 w-full sm:w-64">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari supplier..."
                class="form-input pr-8">
            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </span>
        </div>
        <button type="submit" class="btn-primary py-1.5 px-4">
            Cari
        </button>
        @if(request()->filled('cari'))
            <a href="{{ route('supplier.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                Clear
            </a>
        @endif
    </form>
</div>

<!-- Table Custom Wrapper -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[50rem]">
        <thead class="table-custom-header">
            <tr>
                <th scope="col" class="w-16">No</th>
                <th scope="col">Nama Supplier</th>
                <th scope="col" class="w-44">Telepon</th>
                <th scope="col">Alamat</th>
                <th scope="col" class="text-right w-36">Aksi</th>
            </tr>
        </thead>
        <tbody class="table-custom-body">
            @forelse ($suppliers as $index => $supplier)
                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                    <td class="table-num">{{ $suppliers->firstItem() + $index }}</td>
                    <td class="font-medium text-gray-800">{{ $supplier->nama }}</td>
                    <td class="text-gray-600 font-mono">{{ $supplier->telepon ?? '-' }}</td>
                    <td class="text-gray-600 truncate max-w-xs" title="{{ $supplier->alamat }}">{{ $supplier->alamat ?? '-' }}</td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('supplier.edit', $supplier) }}" class="btn-secondary !p-1.5" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"></path>
                                </svg>
                            </a>
                            <form action="{{ route('supplier.destroy', $supplier) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-destructive !p-1.5" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
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
@endsection

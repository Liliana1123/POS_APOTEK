@extends('layouts.app')
@section('title', 'Supplier')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <h1>Daftar Supplier</h1>
        <p class="text-caption mt-1">Kelola data penyalur/distributor obat.</p>
    </div>
    <a href="{{ route('supplier.create') }}" class="btn-primary">
        + Tambah Supplier
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
    <table class="table-custom">
        <thead class="table-custom-header">
            <tr>
                <th scope="col" class="w-16">No</th>
                <th scope="col">Nama Supplier</th>
                <th scope="col" class="w-44">Telepon</th>
                <th scope="col">Alamat</th>
                <th scope="col" class="text-right w-44">Aksi</th>
            </tr>
        </thead>
        <tbody class="table-custom-body divide-y divide-gray-150">
            @forelse ($suppliers as $index => $supplier)
                <tr>
                    <td class="table-num">{{ $suppliers->firstItem() + $index }}</td>
                    <td class="font-medium text-gray-800">{{ $supplier->nama }}</td>
                    <td class="text-gray-600 font-mono">{{ $supplier->telepon ?? '-' }}</td>
                    <td class="text-gray-600 truncate max-w-xs" title="{{ $supplier->alamat }}">{{ $supplier->alamat ?? '-' }}</td>
                    <td class="text-right space-x-1.5">
                        <a href="{{ route('supplier.edit', $supplier) }}" class="btn-secondary py-1 px-2.5 text-[10px]">Edit</a>
                        <form action="{{ route('supplier.destroy', $supplier) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-destructive py-1 px-2.5 text-[10px]">Hapus</button>
                        </form>
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

<div class="mt-4">{{ $suppliers->links() }}</div>
@endsection

@extends('layouts.app')
@section('title', 'Satuan')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <h1>Daftar Satuan</h1>
        <p class="text-caption mt-1">Kelola tipe satuan kemasan barang/obat.</p>
    </div>
    <a href="{{ route('satuan.create') }}" class="btn-primary">
        + Tambah Satuan
    </a>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('satuan.index') }}" class="flex flex-wrap gap-2 items-center">
        <div class="relative shrink-0 w-full sm:w-64">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari satuan..."
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
            <a href="{{ route('satuan.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
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
                <th scope="col">Nama Satuan</th>
                <th scope="col" class="text-right w-44">Aksi</th>
            </tr>
        </thead>
        <tbody class="table-custom-body divide-y divide-gray-150">
            @forelse ($satuans as $index => $satuan)
                <tr>
                    <td class="table-num">{{ $satuans->firstItem() + $index }}</td>
                    <td class="font-medium text-gray-800">{{ $satuan->nama }}</td>
                    <td class="text-right space-x-1.5">
                        <a href="{{ route('satuan.edit', $satuan) }}" class="btn-secondary py-1 px-2.5 text-[10px]">Edit</a>
                        <form action="{{ route('satuan.destroy', $satuan) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-destructive py-1 px-2.5 text-[10px]">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="p-0">
                        <div class="empty-state-container">
                            <div class="empty-state-title">
                                @if(request()->filled('cari'))
                                    Pencarian Tidak Ditemukan
                                @else
                                    Satuan Kosong
                                @endif
                            </div>
                            <div class="empty-state-desc">
                                @if(request()->filled('cari'))
                                    Tidak ada satuan yang cocok dengan kata kunci "{{ request('cari') }}".
                                @else
                                    Belum ada data satuan terdaftar di sistem.
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $satuans->links() }}</div>
@endsection

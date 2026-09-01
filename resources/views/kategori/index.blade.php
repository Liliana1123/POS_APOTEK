@extends('layouts.app')
@section('title', 'Kategori')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <h1>Daftar Kategori</h1>
        <p class="text-caption mt-1">Kelola tipe penggolongan/kategori obat.</p>
    </div>
    <a href="{{ route('kategori.create') }}" class="btn-primary">
        + Tambah Kategori
    </a>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('kategori.index') }}" class="flex flex-wrap gap-2 items-center">
        <div class="relative shrink-0 w-full sm:w-64">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama kategori..."
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
            <a href="{{ route('kategori.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
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
                <th scope="col">Nama Kategori</th>
                <th scope="col" class="text-right w-44">Aksi</th>
            </tr>
        </thead>
        <tbody class="table-custom-body divide-y divide-gray-150">
            @forelse ($kategoris as $index => $kategori)
                <tr>
                    <td class="table-num">{{ $kategoris->firstItem() + $index }}</td>
                    <td class="font-medium text-gray-800">{{ $kategori->nama }}</td>
                    <td class="text-right space-x-1.5">
                        <a href="{{ route('kategori.edit', $kategori) }}" class="btn-secondary py-1 px-2.5 text-[10px]">Edit</a>
                        <form action="{{ route('kategori.destroy', $kategori) }}" method="POST" class="inline">
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
                                    Kategori Kosong
                                @endif
                            </div>
                            <div class="empty-state-desc">
                                @if(request()->filled('cari'))
                                    Tidak ada kategori yang cocok dengan kata kunci "{{ request('cari') }}".
                                @else
                                    Belum ada data kategori terdaftar di sistem.
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $kategoris->links() }}</div>
@endsection

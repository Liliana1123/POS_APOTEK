@extends('layouts.app')
@section('title', 'Barang')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <h1>Daftar Barang & Obat</h1>
        <p class="text-caption mt-1">Kelola data obat, kategori, resep, dan stok minimum.</p>
    </div>
    <a href="{{ route('barang.create') }}" class="btn-primary flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
        </svg>
        <span>Tambah Barang</span>
    </a>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('barang.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Cari Barang</label>
                <div class="relative">
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau barcode..."
                        class="form-input pr-8">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Kategori</label>
                <select name="kategori_id" class="form-input">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoris as $k)
                        <option value="{{ $k->id }}" @selected(request('kategori_id') == $k->id)>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Resep</label>
                <select name="butuh_resep" class="form-input">
                    <option value="">Semua</option>
                    <option value="1" @selected(request('butuh_resep') === '1')>Wajib Resep</option>
                    <option value="0" @selected(request('butuh_resep') === '0')>Bebas</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Status</label>
                <select name="aktif" class="form-input">
                    <option value="">Semua</option>
                    <option value="1" @selected(request('aktif') === '1')>Aktif</option>
                    <option value="0" @selected(request('aktif') === '0')>Nonaktif</option>
                </select>
            </div>
        </div>
        
        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            @if(request()->anyFilled(['cari', 'kategori_id', 'butuh_resep', 'aktif']))
                <a href="{{ route('barang.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                    Reset
                </a>
            @endif
            <button type="submit" class="btn-primary py-1.5 px-4">
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Table Custom Wrapper -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[50rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="w-16">No</th>
                    <th scope="col">Nama Barang / Produk</th>
                    <th scope="col">Kategori</th>
                    <th scope="col">Satuan</th>
                    <th scope="col" class="w-32">Stok</th>
                    <th scope="col" class="text-center w-28">Resep</th>
                    <th scope="col" class="text-center w-28">Status</th>
                    <th scope="col" class="text-right w-36">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($barangs as $index => $barang)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="table-num">{{ $barangs->firstItem() + $index }}</td>
                        <td>
                            <div class="font-semibold text-gray-800">{{ $barang->nama }}</div>
                            @if ($barang->barcode)
                                <div class="text-[9px] text-gray-400 font-mono mt-0.5" title="Barcode">Code: {{ $barang->barcode }}</div>
                            @endif
                        </td>
                        <td>{{ $barang->kategori->nama ?? '—' }}</td>
                        <td>{{ $barang->satuan->nama ?? '—' }}</td>
                        <td>
                            @php $stok = $barang->stokTotal(); @endphp
                            @if ($stok <= 0)
                                <span class="badge-danger">Habis</span>
                            @elseif ($stok <= $barang->stok_minimum)
                                <span class="badge-warning" title="Min: {{ $barang->stok_minimum }}">{{ $stok }} &middot; Menipis</span>
                            @else
                                <span class="badge-success">{{ $stok }} &middot; Aman</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($barang->butuh_resep)
                                <span class="badge-danger">Wajib Resep</span>
                            @else
                                <span class="badge-neutral">Bebas</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($barang->aktif)
                                <span class="badge-success">Aktif</span>
                            @else
                                <span class="badge-neutral">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('barang.edit', $barang) }}" class="btn-secondary !p-1.5" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('barang.destroy', $barang) }}" method="POST">
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
                        <td colspan="8" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">
                                    @if(request()->anyFilled(['cari', 'kategori_id', 'butuh_resep', 'aktif']))
                                        Barang Tidak Ditemukan
                                    @else
                                        Barang Kosong
                                    @endif
                                </div>
                                <div class="empty-state-desc">
                                    @if(request()->anyFilled(['cari', 'kategori_id', 'butuh_resep', 'aktif']))
                                        Tidak ada produk obat yang cocok dengan filter kriteria pencarian Anda.
                                    @else
                                        Belum ada data barang/obat terdaftar di sistem POS.
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

<div class="mt-4">{{ $barangs->links() }}</div>
@endsection

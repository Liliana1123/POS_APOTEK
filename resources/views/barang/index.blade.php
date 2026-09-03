@extends('layouts.app')
@section('title', 'Barang')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
       <h1 class="text-base font-semibold">Daftar Barang & Obat</h1>
        <p class="text-caption mt-1">Kelola data obat, kategori, resep, dan stok minimum.</p>
    </div>
    <button type="button" id="btn-tambah-barang" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Tambah Barang</span>
    </button>
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
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
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
            <button type="submit" class="btn-primary !p-1.5" title="Filter">
                <x-heroicon-o-funnel class="w-4 h-4" />
            </button>
        </div>
    </form>
</div>

<!-- Table Custom Wrapper -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom table-fixed min-w-[68rem]">
            <thead class="table-custom-header text-center align-middle">
                <tr>
                    <th scope="col" class="w-28">Aksi</th>
                    <th scope="col" class="w-14">No</th>
                    <th scope="col" class="w-[24%]">Nama Barang / Produk</th>
                    <th scope="col" class="w-[17%]">Kategori</th>
                    <th scope="col" class="w-[12%]">Satuan</th>
                    <th scope="col" class="w-32">Stok</th>
                    <th scope="col" class="w-32">Resep</th>
                    <th scope="col" class="w-28">Status</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150 align-middle">
                @forelse ($barangs as $index => $barang)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="w-28 text-center align-middle">
                            <div class="flex items-center justify-center gap-2">
                                  <button type="button"
                                    class="btn-secondary action-icon-button action-icon-edit !flex !h-8 !w-8 !items-center !justify-center !p-0 btn-edit-barang"
                                    style="color: #F59E0B;"
                                    title="Edit"
                                    data-id="{{ $barang->id }}"
                                    data-json="{{ json_encode(['nama' => $barang->nama, 'kategori_id' => $barang->kategori_id, 'satuan_id' => $barang->satuan_id, 'pabrik_id' => $barang->pabrik_id, 'barcode' => $barang->barcode, 'stok_minimum' => $barang->stok_minimum, 'butuh_resep' => (int) $barang->butuh_resep, 'aktif' => (int) $barang->aktif], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) }}">

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
                                </button>

                               <form action="{{ route('barang.destroy', $barang) }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="btn-secondary action-icon-button action-icon-delete !flex !h-8 !w-8 !items-center !justify-center !p-0"
                                    style="color: #DC2626;"
                                    title="Hapus"
                                    aria-label="Hapus"
                                    onclick="return confirm('Yakin ingin menghapus barang ini?')">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-[4px] w-[4px]"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        style="width: 16px; height: 16px; flex-shrink: 0; display: block;">

                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 6V4h8v2"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l1 14h10l-1-14"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 11v6M14 11v6"/>

                                    </svg>
                                </button>
                         </form>
                        </td>
                        <td class="table-num text-center align-middle">{{ $barangs->firstItem() + $index }}</td>
                        <td class="align-middle text-center">
                            <div class="font-semibold text-gray-800">{{ $barang->nama }}</div>
                            @if ($barang->barcode)
                                <div class="text-[9px] text-gray-400 font-mono mt-0.5" title="Barcode">Code: {{ $barang->barcode }}</div>
                            @endif
                        </td>
                        <td class="align-middle text-center">{{ $barang->kategori->nama ?? '—' }}</td>
                        <td class="align-middle text-center">{{ $barang->satuan->nama ?? '—' }}</td>
                        <td class="align-middle text-center whitespace-nowrap">
                            @php $stok = $barang->stokTotal(); @endphp
                            @if ($stok <= 0)
                                <span class="badge-danger">Habis</span>
                            @elseif ($stok <= $barang->stok_minimum)
                                <span class="badge-warning" title="Min: {{ $barang->stok_minimum }}">{{ $stok }} &middot; Menipis</span>
                            @else
                                <span class="badge-success">{{ $stok }} &middot; Aman</span>
                            @endif
                        </td>
                        <td class="text-center align-middle whitespace-nowrap">
                            @if ($barang->butuh_resep)
                                <span class="badge-danger">Wajib Resep</span>
                            @else
                                <span class="badge-neutral">Bebas</span>
                            @endif
                        </td>
                        <td class="text-center align-middle whitespace-nowrap">
                            @if ($barang->aktif)
                                <span class="badge-success">Aktif</span>
                            @else
                                <span class="badge-neutral">Nonaktif</span>
                            @endif
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

<x-modal-form
    id="modal-barang"
    create-title="Tambah Barang"
    edit-title="Edit Barang"
    create-url="{{ route('barang.store') }}"
    update-base="{{ url('barang') }}"
    create-btn="#btn-tambah-barang"
    edit-btn=".btn-edit-barang"
    submit-label="Simpan"
    width="max-w-lg">
    <div class="space-y-4">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Nama Barang / Obat <span class="text-red-500">*</span></label>
            <input type="text" name="nama" required class="form-input" placeholder="Masukkan nama barang...">
            <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="nama"></p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Kategori <span class="text-red-500">*</span></label>
                <select name="kategori_id" required class="form-input">
                    <option value="">Pilih</option>
                    @foreach ($kategoris as $k)
                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
                <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="kategori_id"></p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Satuan <span class="text-red-500">*</span></label>
                <select name="satuan_id" required class="form-input">
                    <option value="">Pilih</option>
                    @foreach ($satuans as $s)
                        <option value="{{ $s->id }}">{{ $s->nama }}</option>
                    @endforeach
                </select>
                <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="satuan_id"></p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Pabrik <span class="text-red-500">*</span></label>
                <select name="pabrik_id" required class="form-input">
                    <option value="">Pilih</option>
                    @foreach ($pabriks as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }}</option>
                    @endforeach
                </select>
                <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="pabrik_id"></p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Barcode</label>
                <input type="text" name="barcode" class="form-input" placeholder="Scan / ketik barcode...">
                <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="barcode"></p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Stok Minimum <span class="text-red-500">*</span></label>
                <input type="number" name="stok_minimum" min="0" class="form-input" placeholder="0">
                <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="stok_minimum"></p>
            </div>
        </div>
        <div class="space-y-2">
            <label class="flex items-start text-xs font-medium text-gray-700 cursor-pointer">
                <input type="checkbox" name="butuh_resep" value="1" class="mr-2.5 mt-0.5 rounded border-gray-300 focus:ring-blue-500 w-4 h-4 text-blue-600">
                <div>
                    <span class="font-semibold">Wajib resep dokter</span>
                    <p class="text-[10px] text-gray-400 mt-0.5">Obat memerlukan resep saat penjualan kasir.</p>
                </div>
            </label>
            <label class="flex items-start text-xs font-medium text-gray-700 cursor-pointer" data-edit-only>
                <input type="checkbox" name="aktif" value="1" class="mr-2.5 mt-0.5 rounded border-gray-300 focus:ring-blue-500 w-4 h-4 text-blue-600">
                <div>
                    <span class="font-semibold">Barang aktif</span>
                    <p class="text-[10px] text-gray-400 mt-0.5">Produk tampil dan dapat dipakai transaksi baru.</p>
                </div>
            </label>
        </div>
    </div>
</x-modal-form>
@endsection

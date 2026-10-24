@extends('layouts.app')
@section('title', 'Data Master Barang')

@section('content')
<!-- Page Header -->
<x-page-header title="Daftar Barang dan Obat" subtitle="Kelola data master obat, kode apotek, KFA, merk, kategori, dan stok minimum.">
    @if(auth()->user()->canManage('master.barang'))
        <button type="button" id="btn-import-barang" class="btn-secondary flex items-center gap-2" title="Import Data Barang">
            <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
            <span>Import Data</span>
        </button>

        <a href="{{ route('barang.export', request()->query()) }}" id="btn-export-barang" data-sync-filter="true" class="btn-secondary flex items-center gap-2" title="Export Data Barang">
            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
            <span>Export Data</span>
        </a>

        <button type="button" id="btn-tambah-barang" class="btn-primary flex items-center gap-2">
            <x-heroicon-o-plus class="w-4 h-4" />
            <span>Tambah Barang</span>
        </button>
    @endif
</x-page-header>

<!-- Filter & Search Card -->
<x-card-filter
    :action="route('barang.index')"
    :reset-url="route('barang.index')"
    :grid="true"
    grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-7"
    :live-search="true"
    :has-reset="request()->anyFilled(['cari', 'kategori_id', 'butuh_resep', 'aktif', 'stok'])">
    <x-search-input name="cari" placeholder="Cari nama, kode, KFA, merk..." class="sm:col-span-2 md:col-span-2 lg:col-span-2" />
    <select name="kategori_id" class="form-input" aria-label="Kategori" title="Kategori">
        <option value="">Kategori: Semua</option>
        @foreach ($kategoris as $k)
            <option value="{{ $k->id }}" @selected(request('kategori_id') == $k->id)>{{ $k->nama }}</option>
        @endforeach
    </select>
    <select name="butuh_resep" class="form-input" aria-label="Resep" title="Resep">
        <option value="">Resep: Semua</option>
        <option value="1" @selected(request('butuh_resep') === '1')>Wajib Resep</option>
        <option value="0" @selected(request('butuh_resep') === '0')>Bebas</option>
    </select>
    <select name="aktif" class="form-input" aria-label="Status" title="Status">
        <option value="">Status: Semua</option>
        <option value="1" @selected(request('aktif') === '1')>Aktif</option>
        <option value="0" @selected(request('aktif') === '0')>Nonaktif</option>
    </select>
    <select name="stok" class="form-input" aria-label="Stok" title="Stok">
        <option value="">Stok: Semua</option>
        <option value="habis" @selected(request('stok') === 'habis')>Habis</option>
        <option value="menipis" @selected(request('stok') === 'menipis')>Menipis</option>
        <option value="aman" @selected(request('stok') === 'aman')>Aman</option>
    </select>
</x-card-filter>

<!-- Table Custom Wrapper -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[72rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="text-center w-36">Aksi</th>
                    <th scope="col" class="w-32">Kode Apotek</th>
                    <th scope="col" class="w-32">Kode KFA</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col" class="w-40">Merk</th>
                    <th scope="col" class="w-28">Satuan</th>
                    <th scope="col" class="w-32">Stok</th>
                    <th scope="col" class="text-center w-28">Status</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($barangs as $index => $barang)
                    @php
                        $stok = $barang->stokTotal();
                        $statusStok = 'Aman';
                        $statusStokBadge = 'badge-success';
                        if ($stok <= 0) {
                            $statusStok = 'Habis';
                            $statusStokBadge = 'badge-danger';
                        } elseif ($stok <= $barang->stok_minimum) {
                            $statusStok = 'Menipis';
                            $statusStokBadge = 'badge-warning';
                        }
                    @endphp
                    @php
                        $canManageBarang = auth()->user()->canManage('master.barang');
                    @endphp
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left pl-3">
                            <x-table-action
                                edit-class="{{ $canManageBarang ? 'btn-edit-barang' : '' }}"
                                :edit-id="$canManageBarang ? $barang->id : null"
                                :edit-data="$canManageBarang ? [
                                    'kode_apotek' => $barang->kode_apotek,
                                    'kode_kfa' => $barang->kode_kfa,
                                    'nama' => $barang->nama,
                                    'merk' => $barang->merk,
                                    'kategori_id' => $barang->kategori_id,
                                    'satuan_id' => $barang->satuan_id,
                                    'pabrik_id' => $barang->pabrik_id,
                                    'barcode' => $barang->barcode,
                                    'stok_minimum' => $barang->stok_minimum,
                                    'butuh_resep' => (int) $barang->butuh_resep,
                                    'aktif' => (int) $barang->aktif
                                ] : null"
                                :delete-url="$canManageBarang ? route('barang.destroy', $barang) : null"
                                delete-confirm="Yakin ingin menghapus barang ini?">
                                
                                <button type="button"
                                    class="btn-secondary !p-1.5 hover:bg-blue-50 hover:border-blue-300 transition-colors btn-detail-barang"
                                    style="color: #2563EB;"
                                    title="Lihat Detail"
                                    aria-label="Lihat Detail"
                                    data-json="{{ json_encode([
                                        'id' => $barang->id,
                                        'kode_apotek' => $barang->kode_apotek ?? '—',
                                        'kode_kfa' => $barang->kode_kfa ?? '—',
                                        'nama' => $barang->nama,
                                        'merk' => $barang->merk ?? '—',
                                        'barcode' => $barang->barcode ?? '—',
                                        'kategori' => $barang->kategori->nama ?? '—',
                                        'satuan' => $barang->satuan->nama ?? '—',
                                        'pabrik' => $barang->pabrik->nama ?? '—',
                                        'supplier' => $barang->supplierNama(),
                                        'stok' => $stok,
                                        'stok_minimum' => $barang->stok_minimum,
                                        'status_stok' => $statusStok,
                                        'status_stok_badge' => $statusStokBadge,
                                        'butuh_resep' => $barang->butuh_resep ? 'Wajib Resep Dokter' : 'Bebas (Tanpa Resep)',
                                        'aktif' => $barang->aktif ? 'Aktif' : 'Nonaktif',
                                        'edit_url' => route('barang.edit', $barang),
                                    ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) }}">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </button>
                            </x-table-action>
                        </td>
                        <td class="font-mono">{{ $barang->kode_apotek ?? '—' }}</td>
                        <td class="font-mono">{{ $barang->kode_kfa ?? '—' }}</td>
                        <td class="font-medium text-gray-800">{{ $barang->nama }}</td>
                        <td>{{ $barang->merk ?: '—' }}</td>
                        <td>{{ $barang->satuan->nama ?? '—' }}</td>
                        <td>{{ $stok }}</td>
                        <td class="text-center">
                            <x-status-badge type="aktif" :status="$barang->aktif" />
                        </td>
                    </tr>
                @empty
                    <x-empty-state colspan="8" />
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-pagination :paginator="$barangs" />

<!-- Modal Tambah / Edit Barang -->
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
        <!-- Info Auto-generate Kode Apotek saat Tambah -->
        <div class="p-2.5 bg-blue-50/70 border border-blue-100 rounded-lg text-xs flex items-center gap-2 text-blue-800" data-create-only>
            <x-heroicon-o-information-circle class="w-4 h-4 shrink-0 text-blue-600" />
            <span>Kode Apotek akan dibuat otomatis oleh sistem saat disimpan (contoh: <code>P-0001</code>).</span>
        </div>

        <!-- Kode Apotek (Hanya tampil saat Edit - Read Only) -->
        <div class="p-2.5 bg-blue-50/70 border border-blue-100 rounded-lg text-xs flex items-center justify-between" data-edit-only>
            <span class="text-gray-600 font-medium">Kode Apotek (Permanen):</span>
            <input type="text" name="kode_apotek" readonly class="bg-transparent font-mono font-bold text-blue-700 text-xs text-right focus:outline-none cursor-default" title="Kode apotek dibuat otomatis oleh sistem dan tidak dapat diubah">
        </div>

        <x-form.input name="nama" label="Nama Barang / Obat" required placeholder="Masukkan nama barang..." />

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <x-form.input name="kode_kfa" label="Kode KFA (Opsional)" placeholder="Contoh: KFA-12345..." />
            <x-form.input name="merk" label="Merk (Opsional)" placeholder="Contoh: Kimia Farma, Kalbe..." />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <x-form.select name="kategori_id" label="Kategori" required placeholder="Pilih" :options="$kategoris->pluck('nama', 'id')" />
            <x-form.select name="satuan_id" label="Satuan" required placeholder="Pilih" :options="$satuans->pluck('nama', 'id')" />
            <x-form.select name="pabrik_id" label="Pabrik" required placeholder="Pilih" :options="$pabriks->pluck('nama', 'id')" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <x-form.input name="barcode" label="Barcode (Opsional)" placeholder="Scan / ketik barcode..." />
            <x-form.input type="number" name="stok_minimum" label="Stok Minimum" required min="0" placeholder="0" />
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

<!-- Modal Partials -->
@include('barang.partials.modal-detail')
@include('barang.partials.modal-import')

@vite('resources/js/pages/barang-index.js')
@endsection

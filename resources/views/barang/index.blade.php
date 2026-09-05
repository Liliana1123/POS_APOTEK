@extends('layouts.app')
@section('title', 'Data Master Barang')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1>Daftar Barang & Obat</h1>
        <p class="text-caption mt-1">Kelola data master obat, kode apotek, KFA, merk, kategori, dan stok minimum.</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <button type="button" id="btn-tambah-barang" class="btn-primary flex items-center gap-2">
            <x-heroicon-o-plus class="w-4 h-4" />
            <span>Tambah Barang</span>
        </button>

        <button type="button" id="btn-import-barang" class="btn-secondary flex items-center gap-2" title="Import Data Barang">
            <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
            <span>Import Data</span>
        </button>

        <a href="{{ route('barang.export', request()->query()) }}" class="btn-secondary flex items-center gap-2" title="Export Data Barang">
            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
            <span>Export Data</span>
        </a>
    </div>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('barang.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Cari Barang</label>
                <div class="relative">
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama, kode apotek, KFA, merk, barcode..."
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
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Stok</label>
                <select name="stok" class="form-input">
                    <option value="">Semua</option>
                    <option value="habis" @selected(request('stok') === 'habis')>Habis</option>
                    <option value="menipis" @selected(request('stok') === 'menipis')>Menipis</option>
                    <option value="aman" @selected(request('stok') === 'aman')>Aman</option>
                </select>
            </div>
        </div>
        
        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            @if(request()->anyFilled(['cari', 'kategori_id', 'butuh_resep', 'aktif', 'stok']))
                <a href="{{ route('barang.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                    Reset
                </a>
            @endif
            <button type="submit" class="btn-primary flex items-center gap-2">
                <x-heroicon-o-funnel class="w-4 h-4" />
                <span>Filter</span>
            </button>
        </div>
    </form>
</div>

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
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left">
                            <div class="flex items-center justify-start gap-1">
                                <!-- 1. Lihat Detail -->
                                <button type="button"
                                    class="btn-secondary !p-1.5 btn-detail-barang"
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

                                <!-- 2. Edit -->
                                <button type="button"
                                    class="btn-secondary !p-1.5 btn-edit-barang"
                                    style="color: #F59E0B;"
                                    title="Edit"
                                    aria-label="Edit"
                                    data-id="{{ $barang->id }}"
                                    data-json="{{ json_encode([
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
                                    ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) }}">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </button>

                                <!-- 3. Hapus -->
                                <form action="{{ route('barang.destroy', $barang) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn-secondary !p-1.5"
                                        style="color: #DC2626;"
                                        title="Hapus"
                                        aria-label="Hapus">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="font-mono">{{ $barang->kode_apotek ?? '—' }}</td>
                        <td class="font-mono">{{ $barang->kode_kfa ?? '—' }}</td>
                        <td class="font-medium text-gray-800">{{ $barang->nama }}</td>
                        <td>{{ $barang->merk ?: '—' }}</td>
                        <td>{{ $barang->satuan->nama ?? '—' }}</td>
                        <td class="whitespace-nowrap">
                            @if ($stok <= 0)
                                <span class="badge-danger">Habis</span>
                            @elseif ($stok <= $barang->stok_minimum)
                                <span class="badge-warning" title="Min: {{ $barang->stok_minimum }}">{{ $stok }} · Menipis</span>
                            @else
                                <span class="badge-success">{{ $stok }} · Aman</span>
                            @endif
                        </td>
                        <td class="text-center whitespace-nowrap">
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
                                    @if(request()->anyFilled(['cari', 'kategori_id', 'butuh_resep', 'aktif', 'stok']))
                                        Barang Tidak Ditemukan
                                    @else
                                        Barang Kosong
                                    @endif
                                </div>
                                <div class="empty-state-desc">
                                    @if(request()->anyFilled(['cari', 'kategori_id', 'butuh_resep', 'aktif', 'stok']))
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

<!-- Modal Detail Barang -->
<div id="modal-detail-barang-backdrop" class="fixed inset-0 bg-slate-900/40 z-50 hidden transition-opacity"></div>
<div id="modal-detail-barang" class="fixed inset-0 z-50 hidden flex items-start justify-center pt-[4vh] sm:pt-[6vh] px-4">
    <div class="w-full max-w-2xl bg-white rounded-xl shadow-2xl relative max-h-[90vh] flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 shrink-0 bg-slate-50/70">
            <div>
                <h3 class="text-sm font-bold text-gray-900" id="detail-title-nama">Detail Barang</h3>
                <p class="text-[11px] text-gray-500 font-sans">Informasi lengkap data barang & klasifikasi obat.</p>
            </div>
            <button type="button" id="btn-close-detail-modal" class="text-gray-400 hover:text-gray-600 transition-colors p-1" aria-label="Tutup">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <!-- Body -->
        <div class="px-6 py-5 overflow-y-auto flex-1 space-y-6">
            <!-- 1. IDENTITAS -->
            <div>
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2.5 pb-1 border-b">1. Identitas Barang</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-2.5 bg-gray-50/80 rounded-lg">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Kode Apotek</span>
                        <span class="font-semibold text-gray-800 text-xs mt-0.5 block" id="detail-kode-apotek">—</span>
                    </div>
                    <div class="p-2.5 bg-gray-50/80 rounded-lg">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Kode KFA</span>
                        <span class="font-mono font-semibold text-gray-800 mt-0.5 block" id="detail-kode-kfa">—</span>
                    </div>
                    <div class="p-2.5 bg-gray-50/80 rounded-lg">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Nama Barang</span>
                        <span class="font-semibold text-gray-900 mt-0.5 block" id="detail-nama">—</span>
                    </div>
                    <div class="p-2.5 bg-gray-50/80 rounded-lg">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Merk</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block" id="detail-merk">—</span>
                    </div>
                    <div class="p-2.5 bg-gray-50/80 rounded-lg sm:col-span-2">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Barcode</span>
                        <span class="font-mono text-gray-800 mt-0.5 block" id="detail-barcode">—</span>
                    </div>
                </div>
            </div>

            <!-- 2. DATA MASTER -->
            <div>
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2.5 pb-1 border-b">2. Data Master</h4>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="p-2.5 bg-gray-50/80 rounded-lg">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Kategori</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block" id="detail-kategori">—</span>
                    </div>
                    <div class="p-2.5 bg-gray-50/80 rounded-lg">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Satuan</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block" id="detail-satuan">—</span>
                    </div>
                    <div class="p-2.5 bg-gray-50/80 rounded-lg">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Pabrik</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block" id="detail-pabrik">—</span>
                    </div>
                    <div class="p-2.5 bg-gray-50/80 rounded-lg">
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Supplier</span>
                        <span class="font-semibold text-gray-800 mt-0.5 block" id="detail-supplier">—</span>
                    </div>
                </div>
            </div>

            <!-- 3. STOK & 4. LAINNYA -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2.5 pb-1 border-b">3. Inventori & Stok</h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between items-center p-2.5 bg-gray-50/80 rounded-lg">
                            <span class="text-gray-500 font-medium">Stok Saat Ini:</span>
                            <span class="font-bold text-gray-900 text-sm" id="detail-stok">0</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-gray-50/80 rounded-lg">
                            <span class="text-gray-500 font-medium">Stok Minimum:</span>
                            <span class="font-semibold text-gray-800" id="detail-stok-minimum">0</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-gray-50/80 rounded-lg">
                            <span class="text-gray-500 font-medium">Status Stok:</span>
                            <span id="detail-status-stok-badge" class="badge-success">Aman</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2.5 pb-1 border-b">4. Ketentuan & Status</h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between items-center p-2.5 bg-gray-50/80 rounded-lg">
                            <span class="text-gray-500 font-medium">Butuh Resep:</span>
                            <span class="font-semibold text-gray-800" id="detail-butuh-resep">Bebas</span>
                        </div>
                        <div class="flex justify-between items-center p-2.5 bg-gray-50/80 rounded-lg">
                            <span class="text-gray-500 font-medium">Status Aktif:</span>
                            <span class="font-semibold text-gray-800" id="detail-status-aktif">Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between gap-2 px-6 py-3.5 border-t border-gray-100 shrink-0 bg-slate-50/70">
            <button type="button" id="btn-edit-from-detail" class="btn-primary py-1.5 px-4 flex items-center gap-1.5 text-xs">
                <x-heroicon-o-pencil-square class="w-4 h-4" />
                <span>Edit Barang</span>
            </button>
            <button type="button" id="btn-cancel-detail-modal" class="btn-secondary py-1.5 px-4 text-xs">
                Tutup
            </button>
        </div>
    </div>
</div>

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

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Nama Barang / Obat <span class="text-red-500">*</span></label>
            <input type="text" name="nama" required class="form-input" placeholder="Masukkan nama barang...">
            <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="nama"></p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Kode KFA (Opsional)</label>
                <input type="text" name="kode_kfa" class="form-input" placeholder="Contoh: KFA-12345...">
                <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="kode_kfa"></p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Merk (Opsional)</label>
                <input type="text" name="merk" class="form-input" placeholder="Contoh: Kimia Farma, Kalbe...">
                <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="merk"></p>
            </div>
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
                <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Barcode (Opsional)</label>
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

<!-- Modal Import Barang -->
<div id="modal-import-barang-backdrop" class="fixed inset-0 bg-slate-900/40 z-50 hidden transition-opacity"></div>
<div id="modal-import-barang" class="fixed inset-0 z-50 hidden flex items-start justify-center pt-[5vh] sm:pt-[10vh] px-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-2xl relative max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 shrink-0">
            <h3 class="text-sm font-bold text-gray-800">Import Data Master Barang</h3>
            <button type="button" id="btn-close-import-modal" class="text-gray-400 hover:text-gray-600 transition-colors p-1" aria-label="Tutup">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>
        
        <form action="{{ route('barang.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden" id="form-import-barang">
            @csrf
            <div class="px-5 py-4 overflow-y-auto flex-1 space-y-4">
                <!-- Info & Download Template Card -->
                <div class="p-3 bg-blue-50/70 border border-blue-100 rounded-lg text-xs space-y-2">
                    <div class="flex items-start gap-2 text-blue-800 font-medium">
                        <x-heroicon-o-information-circle class="w-4 h-4 shrink-0 mt-0.5 text-blue-600" />
                        <span>Format file yang didukung adalah <strong>CSV (.csv)</strong>.</span>
                    </div>
                    <p class="text-blue-600 text-[11px] leading-relaxed">
                        Pastikan kolom pada file CSV mencakup: <code class="bg-blue-100/80 px-1 py-0.5 rounded text-[10px] font-mono text-blue-900">nama, kode_kfa, merk, kategori, satuan, pabrik, barcode, stok_minimum, butuh_resep</code>.
                    </p>
                    <div class="pt-1">
                        <a href="{{ route('barang.import-template') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 hover:text-blue-900 underline">
                            <x-heroicon-o-arrow-down-tray class="w-3.5 h-3.5" />
                            <span>Unduh Template CSV</span>
                        </a>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Pilih File CSV <span class="text-red-500">*</span></label>
                    <input type="file" name="file" accept=".csv,text/csv,text/plain" required class="block w-full text-xs text-gray-700 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-gray-200 rounded-lg p-1.5 bg-gray-50/50">
                </div>

                <div class="pt-1">
                    <label class="flex items-start text-xs font-medium text-gray-700 cursor-pointer">
                        <input type="checkbox" name="auto_create_master" value="1" checked class="mr-2.5 mt-0.5 rounded border-gray-300 focus:ring-blue-500 w-4 h-4 text-blue-600">
                        <div>
                            <span class="font-semibold text-gray-800">Otomatis daftarkan master baru</span>
                            <p class="text-[10px] text-gray-400 mt-0.5">Jika nama kategori, satuan, atau pabrik belum ada di sistem, buat otomatis.</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 px-5 py-3 border-t border-gray-100 shrink-0 bg-gray-50/50">
                <button type="button" id="btn-cancel-import-modal" class="btn-secondary py-1.5 px-4">Batal</button>
                <button type="submit" id="btn-submit-import" class="btn-primary py-1.5 px-5 flex items-center gap-2">
                    <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                    <span>Upload & Import</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- DETAIL MODAL LOGIC ---
    var detailModal = document.getElementById('modal-detail-barang');
    var detailBackdrop = document.getElementById('modal-detail-barang-backdrop');
    var btnCloseDetail = document.getElementById('btn-close-detail-modal');
    var btnCancelDetail = document.getElementById('btn-cancel-detail-modal');
    var btnEditFromDetail = document.getElementById('btn-edit-from-detail');

    function openDetailModal(data) {
        document.getElementById('detail-title-nama').textContent = data.nama;
        document.getElementById('detail-kode-apotek').textContent = data.kode_apotek;
        document.getElementById('detail-kode-kfa').textContent = data.kode_kfa;
        document.getElementById('detail-nama').textContent = data.nama;
        document.getElementById('detail-merk').textContent = data.merk;
        document.getElementById('detail-barcode').textContent = data.barcode;
        document.getElementById('detail-kategori').textContent = data.kategori;
        document.getElementById('detail-satuan').textContent = data.satuan;
        document.getElementById('detail-pabrik').textContent = data.pabrik;
        document.getElementById('detail-supplier').textContent = data.supplier;
        document.getElementById('detail-stok').textContent = data.stok + ' ' + data.satuan;
        document.getElementById('detail-stok-minimum').textContent = data.stok_minimum + ' ' + data.satuan;

        var badge = document.getElementById('detail-status-stok-badge');
        badge.className = data.status_stok_badge;
        badge.textContent = data.status_stok;

        document.getElementById('detail-butuh-resep').textContent = data.butuh_resep;
        document.getElementById('detail-status-aktif').textContent = data.aktif;

        window.__detailEditId = data.id;

        detailBackdrop.classList.remove('hidden');
        detailModal.classList.remove('hidden');
    }

    function closeDetailModal() {
        detailBackdrop.classList.add('hidden');
        detailModal.classList.add('hidden');
    }

    document.querySelectorAll('.btn-detail-barang').forEach(function (btn) {
        btn.addEventListener('click', function () {
            try {
                var data = JSON.parse(this.dataset.json);
                openDetailModal(data);
            } catch (e) {
                console.error('Error parsing barang detail JSON', e);
            }
        });
    });

    if (btnCloseDetail) btnCloseDetail.addEventListener('click', closeDetailModal);
    if (btnCancelDetail) btnCancelDetail.addEventListener('click', closeDetailModal);
    if (detailBackdrop) detailBackdrop.addEventListener('click', closeDetailModal);
    if (btnEditFromDetail) btnEditFromDetail.addEventListener('click', function() {
        closeDetailModal();
        var editBtn = document.querySelector('.btn-edit-barang[data-id="' + window.__detailEditId + '"]');
        if (editBtn) editBtn.click();
    });

    // --- IMPORT MODAL LOGIC ---
    var importModal = document.getElementById('modal-import-barang');
    var importBackdrop = document.getElementById('modal-import-barang-backdrop');
    var btnOpen = document.getElementById('btn-import-barang');
    var btnClose = document.getElementById('btn-close-import-modal');
    var btnCancel = document.getElementById('btn-cancel-import-modal');
    var formImport = document.getElementById('form-import-barang');
    var submitBtn = document.getElementById('btn-submit-import');

    function openImportModal() {
        importBackdrop.classList.remove('hidden');
        importModal.classList.remove('hidden');
    }

    function closeImportModal() {
        importBackdrop.classList.add('hidden');
        importModal.classList.add('hidden');
    }

    if (btnOpen) btnOpen.addEventListener('click', openImportModal);
    if (btnClose) btnClose.addEventListener('click', closeImportModal);
    if (btnCancel) btnCancel.addEventListener('click', closeImportModal);
    if (importBackdrop) importBackdrop.addEventListener('click', closeImportModal);

    if (formImport) {
        formImport.addEventListener('submit', function () {
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span>Memproses...</span>';
            }
        });
    }
});
</script>
@endsection

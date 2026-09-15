@extends('layouts.app')
@section('title', 'Data Master Barang')

@section('content')
<!-- Page Header -->
<x-page-header title="Daftar Barang & Obat" subtitle="Kelola data master obat, kode apotek, KFA, merk, kategori, dan stok minimum.">
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
</x-page-header>

<!-- Filter & Search Card -->
<x-card-filter
    :action="route('barang.index')"
    :reset-url="route('barang.index')"
    :grid="true"
    grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-5"
    :has-reset="request()->anyFilled(['cari', 'kategori_id', 'butuh_resep', 'aktif', 'stok'])">
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Cari Barang</label>
        <div class="relative">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama, kode, KFA, merk..." class="form-input pr-8">
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
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left">
                            <x-table-action
                                edit-class="btn-edit-barang"
                                :edit-id="$barang->id"
                                :edit-data="[
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
                                ]"
                                :delete-url="route('barang.destroy', $barang)"
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
                            <x-badge :variant="$barang->aktif ? 'success' : 'secondary'">
                                {{ $barang->aktif ? 'Aktif' : 'Nonaktif' }}
                            </x-badge>
                        </td>
                    </tr>
                @empty
                    <x-empty-state colspan="8" />
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $barangs->links() }}</div>

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

<!-- Modal Partials -->
@include('barang.partials.modal-detail')
@include('barang.partials.modal-import')

<!-- Page Specific Script -->
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

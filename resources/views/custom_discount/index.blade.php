@extends('layouts.app')
@section('title', 'Custom Discount')

@section('content')
<!-- Page Header -->
<x-page-header title="Manajemen Custom Discount (Promo)" subtitle="Kelola skema promo diskon, periode aktif kalender, dan cakupan obat.">
    <button type="button" id="btn-tambah-promo" class="btn-primary flex items-center gap-1.5">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Tambah Promo</span>
    </button>
</x-page-header>

<!-- Filter & Search Card -->
<x-card-filter :action="route('custom-discount.index')" :reset-url="route('custom-discount.index')" :has-filter="request()->anyFilled(['cari', 'status'])">
    <div class="relative shrink-0 w-full sm:w-64">
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama promo..." class="form-input pr-8">
        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
        </span>
    </div>

    <div class="w-full sm:w-48">
        <select name="status" class="form-input">
            <option value="">Semua Status</option>
            <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
            <option value="belum_mulai" @selected(request('status') === 'belum_mulai')>Belum Mulai</option>
            <option value="berakhir" @selected(request('status') === 'berakhir')>Berakhir</option>
            <option value="nonaktif" @selected(request('status') === 'nonaktif')>Non-aktif</option>
        </select>
    </div>
</x-card-filter>

<!-- Table Card -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[55rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="text-center w-36">Aksi</th>
                    <th scope="col">Nama Promo</th>
                    <th scope="col" class="w-24">Persentase</th>
                    <th scope="col" class="w-32">Mulai</th>
                    <th scope="col" class="w-32">Selesai</th>
                    <th scope="col" class="w-28">Cakupan</th>
                    <th scope="col" class="text-center w-28">Status</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($discounts as $index => $discount)
                    @php
                        $today = now()->format('Y-m-d');
                        $startDate = $discount->tanggal_mulai->format('Y-m-d');
                        $endDate = $discount->tanggal_selesai->format('Y-m-d');
                        
                        if (!$discount->aktif) {
                            $statusText = 'Non-aktif';
                            $statusType = 'secondary';
                        } elseif ($startDate > $today) {
                            $statusText = 'Belum Mulai';
                            $statusType = 'warning';
                        } elseif ($endDate < $today) {
                            $statusText = 'Berakhir';
                            $statusType = 'danger';
                        } else {
                            $statusText = 'Aktif';
                            $statusType = 'success';
                        }
                    @endphp
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left">
                            <x-table-action
                                edit-class="btn-edit-promo"
                                :edit-id="$discount->id"
                                :edit-data="[
                                    'nama' => $discount->nama,
                                    'persentase' => $discount->persentase,
                                    'aktif' => $discount->aktif ? '1' : '0',
                                    'tanggal_mulai' => $discount->tanggal_mulai->format('Y-m-d'),
                                    'tanggal_selesai' => $discount->tanggal_selesai->format('Y-m-d'),
                                    'cakupan' => $discount->cakupan,
                                    'kategori_ids' => $discount->kategoris->pluck('id')->toArray(),
                                    'barang_ids' => $discount->barangs->pluck('id')->toArray(),
                                ]"
                                :delete-url="route('custom-discount.destroy', $discount)"
                                delete-confirm="Yakin ingin menghapus promo diskon ini?"
                            >
                                <form action="{{ route('custom-discount.toggle', $discount) }}" method="POST" class="inline toggle-promo-form">
                                    @csrf
                                    <button type="submit" 
                                            class="btn-secondary !p-1.5 hover:bg-slate-100 transition-colors {{ $discount->aktif ? 'text-slate-500 hover:text-red-600' : 'text-slate-500 hover:text-emerald-600' }}"
                                            title="{{ $discount->aktif ? 'Nonaktifkan Promo' : 'Aktifkan Promo' }}"
                                            aria-label="{{ $discount->aktif ? 'Nonaktifkan Promo' : 'Aktifkan Promo' }}">
                                        @if($discount->aktif)
                                            <x-heroicon-o-pause-circle class="w-4 h-4 text-amber-600" />
                                        @else
                                            <x-heroicon-o-play-circle class="w-4 h-4 text-emerald-600" />
                                        @endif
                                    </button>
                                </form>
                            </x-table-action>
                        </td>
                        <td class="font-medium text-gray-800">{{ $discount->nama }}</td>
                        <td class="text-emerald-600 font-semibold font-mono">{{ $discount->persentase }}%</td>
                        <td class="text-gray-600 font-mono text-xs">{{ $discount->tanggal_mulai->format('d M Y') }}</td>
                        <td class="text-gray-600 font-mono text-xs">{{ $discount->tanggal_selesai->format('d M Y') }}</td>
                        <td>
                            <x-badge type="info">{{ $discount->cakupan }}</x-badge>
                        </td>
                        <td class="text-center">
                            <x-badge :type="$statusType">{{ $statusText }}</x-badge>
                        </td>
                    </tr>
                @empty
                    <x-empty-state colspan="7" title="Promo Kosong" description="Belum ada custom discount atau promo terdaftar di sistem." />
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $discounts->links() }}
</div>

<!-- Modal Form CRUD Promo -->
<x-modal-form
    id="modal-promo"
    create-title="Tambah Promo Baru"
    edit-title="Edit Custom Discount"
    create-url="{{ route('custom-discount.store') }}"
    update-base="{{ url('/custom-discount') }}"
    create-btn="#btn-tambah-promo"
    edit-btn=".btn-edit-promo"
    width="max-w-2xl"
>
    <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1.5 font-sans">Nama Diskon / Promo <span class="text-red-500 font-bold">*</span></label>
        <input type="text" name="nama" required placeholder="Contoh: Promo Spesial Ulang Tahun Apotek" class="form-input">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="nama"></p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5 font-sans">Persentase Diskon (0 - 50%) <span class="text-red-500 font-bold">*</span></label>
            <input type="number" name="persentase" min="0" max="50" required class="form-input font-mono" placeholder="0">
            <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="persentase"></p>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5 font-sans">Status Keaktifan <span class="text-red-500 font-bold">*</span></label>
            <select name="aktif" class="form-input">
                <option value="1">Aktif</option>
                <option value="0">Non-aktif</option>
            </select>
            <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="aktif"></p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5 font-sans">Tanggal Mulai <span class="text-red-500 font-bold">*</span></label>
            <input type="date" name="tanggal_mulai" value="{{ now()->format('Y-m-d') }}" required class="form-input">
            <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="tanggal_mulai"></p>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5 font-sans">Tanggal Selesai <span class="text-red-500 font-bold">*</span></label>
            <input type="date" name="tanggal_selesai" value="{{ now()->format('Y-m-d') }}" required class="form-input">
            <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="tanggal_selesai"></p>
        </div>
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1.5 font-sans">Cakupan Promo <span class="text-red-500 font-bold">*</span></label>
        <div class="flex flex-wrap gap-4 text-xs mt-1.5">
            <label class="flex items-center gap-1.5 cursor-pointer">
                <input type="radio" name="cakupan" value="semua" checked onclick="toggleModalCakupanFields('semua')" class="rounded-full border-gray-300 text-blue-600 focus:ring-blue-500">
                <span>Semua Barang</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer">
                <input type="radio" name="cakupan" value="kategori" onclick="toggleModalCakupanFields('kategori')" class="rounded-full border-gray-300 text-blue-600 focus:ring-blue-500">
                <span>Kategori</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer">
                <input type="radio" name="cakupan" value="barang" onclick="toggleModalCakupanFields('barang')" class="rounded-full border-gray-300 text-blue-600 focus:ring-blue-500">
                <span>Barang Tertentu</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer">
                <input type="radio" name="cakupan" value="kombinasi" onclick="toggleModalCakupanFields('kombinasi')" class="rounded-full border-gray-300 text-blue-600 focus:ring-blue-500">
                <span>Kombinasi (Kategori + Barang)</span>
            </label>
        </div>
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="cakupan"></p>
    </div>

    <!-- Kategori Selection Wrapper -->
    <div id="modal-section-kategori" class="hidden border-t border-gray-100 pt-3 space-y-1.5">
        <label class="block text-xs font-semibold text-gray-700 mb-1.5 font-sans">Pilih Kategori</label>
        <div class="max-h-36 overflow-y-auto border border-gray-200 rounded-lg p-3 grid grid-cols-1 sm:grid-cols-2 gap-2 bg-gray-50">
            @foreach ($kategoris as $k)
                <label class="flex items-center gap-2 text-xs cursor-pointer hover:bg-white p-1.5 rounded transition-colors">
                    <input type="checkbox" name="kategori_ids[]" value="{{ $k->id }}"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-gray-700 font-medium">{{ $k->nama }}</span>
                </label>
            @endforeach
        </div>
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="kategori_ids"></p>
    </div>

    <!-- Barang Selection Wrapper (Searchable) -->
    <div id="modal-section-barang" class="hidden border-t border-gray-100 pt-3 space-y-2">
        <label class="block text-xs font-semibold text-gray-700 mb-1.5 font-sans">Pilih Barang</label>
        <input type="text" id="modal-filter-barang" placeholder="Ketik nama obat untuk menyaring..." class="form-input py-1 px-2.5 text-xs">
        <div class="max-h-44 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-1.5 bg-gray-50" id="modal-barang-list-container">
            @foreach ($barangs as $b)
                <label class="modal-barang-item flex items-center gap-2 text-xs cursor-pointer hover:bg-white p-1 rounded transition-colors" data-nama="{{ strtolower($b->nama) }}">
                    <input type="checkbox" name="barang_ids[]" value="{{ $b->id }}"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <div class="text-gray-700">
                        <strong class="font-semibold">{{ $b->nama }}</strong>
                        <span class="text-gray-400 font-mono text-[10px] ml-1">({{ $b->kategori->nama ?? 'Tanpa Kategori' }})</span>
                    </div>
                </label>
            @endforeach
        </div>
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="barang_ids"></p>
    </div>
</x-modal-form>

<script>
function toggleModalCakupanFields(cakupanVal) {
    const secKategori = document.getElementById('modal-section-kategori');
    const secBarang = document.getElementById('modal-section-barang');
    if (!secKategori || !secBarang) return;

    if (cakupanVal === 'semua') {
        secKategori.classList.add('hidden');
        secBarang.classList.add('hidden');
    } else if (cakupanVal === 'kategori') {
        secKategori.classList.remove('hidden');
        secBarang.classList.add('hidden');
    } else if (cakupanVal === 'barang') {
        secKategori.classList.add('hidden');
        secBarang.classList.remove('hidden');
    } else if (cakupanVal === 'kombinasi') {
        secKategori.classList.remove('hidden');
        secBarang.classList.remove('hidden');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Confirmation on toggle promo status
    document.querySelectorAll('.toggle-promo-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!confirm('Yakin ingin mengubah status keaktifan promo ini?')) {
                e.preventDefault();
            }
        });
    });

    // Reset cakupan view when Create button clicked
    const btnTambah = document.getElementById('btn-tambah-promo');
    if (btnTambah) {
        btnTambah.addEventListener('click', () => {
            toggleModalCakupanFields('semua');
        });
    }

    // Sync cakupan view when Edit button clicked
    document.querySelectorAll('.btn-edit-promo').forEach(btn => {
        btn.addEventListener('click', () => {
            if (btn.dataset.json) {
                try {
                    const data = JSON.parse(btn.dataset.json);
                    toggleModalCakupanFields(data.cakupan || 'semua');
                } catch(e) {}
            }
        });
    });

    // Filter barang search input inside modal
    const filterBarangInput = document.getElementById('modal-filter-barang');
    if (filterBarangInput) {
        filterBarangInput.addEventListener('input', () => {
            const term = filterBarangInput.value.toLowerCase().trim();
            const items = document.querySelectorAll('.modal-barang-item');
            items.forEach(item => {
                const name = item.dataset.nama;
                if (name.includes(term)) {
                    item.classList.remove('hidden');
                    item.classList.add('flex');
                } else {
                    item.classList.add('hidden');
                    item.classList.remove('flex');
                }
            });
        });
    }
});
</script>
@endsection

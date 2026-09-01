@extends('layouts.app')
@section('title', 'Edit Promo')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1>Edit Custom Discount</h1>
        <p class="text-caption mt-1">Ubah skema promo diskon, masa aktif, atau filter cakupan barang.</p>
    </div>
    <a href="{{ route('custom-discount.index') }}" class="btn-secondary py-2 px-4 shrink-0">
        &larr; Kembali
    </a>
</div>

@if ($errors->any())
    <div class="alert-danger p-3 mb-6">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('custom-discount.update', $customDiscount) }}" method="POST" class="card-base p-6 max-w-2xl space-y-5">
    @csrf
    @method('PUT')

    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Nama Diskon / Promo <span class="text-red-500 font-bold">*</span></label>
        <input type="text" name="nama" value="{{ old('nama', $customDiscount->nama) }}" required placeholder="Contoh: Promo Spesial Ulang Tahun Apotek" class="form-input">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Persentase Diskon (0 - 50%) <span class="text-red-500 font-bold">*</span></label>
            <input type="number" name="persentase" value="{{ old('persentase', $customDiscount->persentase) }}" min="0" max="50" required class="form-input font-mono">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Status Keaktifan <span class="text-red-500 font-bold">*</span></label>
            <select name="aktif" class="form-input">
                <option value="1" {{ old('aktif', $customDiscount->aktif ? '1' : '0') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ old('aktif', $customDiscount->aktif ? '1' : '0') === '0' ? 'selected' : '' }}>Non-aktif</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Tanggal Mulai <span class="text-red-500 font-bold">*</span></label>
            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $customDiscount->tanggal_mulai->format('Y-m-d')) }}" required class="form-input">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Tanggal Selesai <span class="text-red-500 font-bold">*</span></label>
            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $customDiscount->tanggal_selesai->format('Y-m-d')) }}" required class="form-input">
        </div>
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Cakupan Promo <span class="text-red-500 font-bold">*</span></label>
        <div class="flex flex-wrap gap-4 text-xs mt-1.5">
            <label class="flex items-center gap-1.5 cursor-pointer">
                <input type="radio" name="cakupan" value="semua" {{ old('cakupan', $customDiscount->cakupan) === 'semua' ? 'checked' : '' }} onclick="toggleCakupanFields('semua')" class="rounded-full border-gray-300 text-blue-600 focus:ring-blue-500">
                <span>Semua Barang</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer">
                <input type="radio" name="cakupan" value="kategori" {{ old('cakupan', $customDiscount->cakupan) === 'kategori' ? 'checked' : '' }} onclick="toggleCakupanFields('kategori')" class="rounded-full border-gray-300 text-blue-600 focus:ring-blue-500">
                <span>Kategori</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer">
                <input type="radio" name="cakupan" value="barang" {{ old('cakupan', $customDiscount->cakupan) === 'barang' ? 'checked' : '' }} onclick="toggleCakupanFields('barang')" class="rounded-full border-gray-300 text-blue-600 focus:ring-blue-500">
                <span>Barang Tertentu</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer">
                <input type="radio" name="cakupan" value="kombinasi" {{ old('cakupan', $customDiscount->cakupan) === 'kombinasi' ? 'checked' : '' }} onclick="toggleCakupanFields('kombinasi')" class="rounded-full border-gray-300 text-blue-600 focus:ring-blue-500">
                <span>Kombinasi (Kategori + Barang)</span>
            </label>
        </div>
    </div>

    <!-- Kategori Selection Wrapper -->
    <div id="section-kategori" class="hidden border-t pt-3 space-y-1.5">
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Pilih Kategori</label>
        <div class="max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach ($kategoris as $k)
                <label class="flex items-center gap-2 text-xs cursor-pointer hover:bg-gray-50 p-1.5 rounded transition-colors">
                    <input type="checkbox" name="kategori_ids[]" value="{{ $k->id }}"
                        {{ in_array($k->id, old('kategori_ids', $selectedKategoriIds)) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-gray-700">{{ $k->nama }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <!-- Barang Selection Wrapper (Searchable) -->
    <div id="section-barang" class="hidden border-t pt-3 space-y-2">
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Pilih Barang</label>
        <input type="text" id="filter-barang" placeholder="Ketik nama obat untuk menyaring..." class="form-input py-1 px-2.5 text-xs">
        <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-1.5" id="barang-list-container">
            @foreach ($barangs as $b)
                <label class="barang-item flex items-center gap-2 text-xs cursor-pointer hover:bg-gray-50 p-1 rounded transition-colors" data-nama="{{ strtolower($b->nama) }}">
                    <input type="checkbox" name="barang_ids[]" value="{{ $b->id }}"
                        {{ in_array($b->id, old('barang_ids', $selectedBarangIds)) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <div class="text-gray-700">
                        <strong class="font-semibold">{{ $b->nama }}</strong>
                        <span class="text-gray-400 font-mono text-[10px] ml-1">({{ $b->kategori->nama ?? 'Tanpa Kategori' }})</span>
                    </div>
                </label>
            @endforeach
        </div>
    </div>

    <div class="flex gap-2 border-t pt-4">
        <button type="submit" class="btn-primary py-2 px-6">Simpan Perubahan</button>
        <a href="{{ route('custom-discount.index') }}" class="btn-secondary py-2 px-4 flex items-center justify-center">Batal</a>
    </div>
</form>

<script>
function toggleCakupanFields(cakupanVal) {
    const secKategori = document.getElementById('section-kategori');
    const secBarang = document.getElementById('section-barang');

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

// Filter barang in search box
const filterBarangInput = document.getElementById('filter-barang');
filterBarangInput.addEventListener('input', () => {
    const term = filterBarangInput.value.toLowerCase().trim();
    const items = document.querySelectorAll('.barang-item');
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

// Trigger toggle on load
document.addEventListener('DOMContentLoaded', () => {
    const activeRadio = document.querySelector('input[name="cakupan"]:checked');
    if (activeRadio) {
        toggleCakupanFields(activeRadio.value);
    }
});
</script>
@endsection

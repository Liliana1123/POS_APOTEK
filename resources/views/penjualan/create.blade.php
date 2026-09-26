@extends('layouts.app')
@section('title', 'Transaksi Baru')

@section('content')
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">Transaksi Penjualan Baru</h1>
    <p class="text-xs text-gray-500 mt-0.5">Catat penjualan obat kasir baru, terapkan diskon member dan FEFO batch otomatis.</p>
</div>

@if ($errors->any())
    <div class="bg-red-50 text-red-700 text-xs p-3 rounded-lg mb-4 border border-red-200">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">

    <!-- ===================================================== -->
    <!-- KOLOM KIRI: PENCARIAN OBAT / BARANG -->
    <!-- ===================================================== -->
    <div class="lg:col-span-1 card-base p-4 transaksi-card">
        <div class="border-b border-blue-100 pb-3 mb-3">
           <h2 class="text-sm font-bold text-slate-800">
                Pencarian Obat / Barang
            </h2>
            <p class="text-[10px] text-slate-500 mt-0.5">
                Pilih jenis transaksi, lalu cari dan pilih barang.
            </p>
        </div>

        {{-- Jenis Transaksi - Kolom Kiri --}}
<div class="mb-3">
    <label class="block text-[10px] font-semibold text-gray-500 mb-1.5">
        Jenis Transaksi
    </label>

    <div class="grid grid-cols-2 gap-2">
        <label class="jenis-option">
            <input
                type="radio"
                name="jenis_transaksi_kiri"
                id="jenis-non-resep-kiri"
                value="non_resep"
                checked
            >
            <x-heroicon-o-document-text class="jenis-icon" />
            <span>Non Resep</span>
        </label>

        <label class="jenis-option">
            <input
                type="radio"
                name="jenis_transaksi_kiri"
                id="jenis-resep-kiri"
                value="resep"
            >
            <x-heroicon-o-clipboard-document-list class="jenis-icon" />
            <span>Resep</span>
        </label>
    </div>
</div>

        {{-- Pencarian Obat / Barang --}}
        <div class="mb-2">
            <input
                type="text"
                id="cari-barang"
                placeholder="Cari nama atau kode barang..."
                class="form-input text-xs rounded-lg border-slate-200
                bg-slate-50/50 transition-all duration-150
                focus:border-blue-400 focus:bg-white
                focus:ring-2 focus:ring-blue-100"
            >
        </div>

        {{-- Info jumlah barang tersedia --}}
        <p id="info-jumlah-barang" class="text-[10px] text-slate-400 mb-2 px-0.5"></p>

        {{-- Hasil pencarian --}}
        <div
            id="daftar-barang"
            class="space-y-1.5 max-h-[calc(100vh-320px)] overflow-y-auto pr-1"
        ></div>
    </div>

    <!-- ===================================================== -->
    <!-- KOLOM KANAN: KERANJANG TRANSAKSI -->
    <!-- ===================================================== -->
    <div class="lg:col-span-2 card-base p-4 transaksi-card">

       <div class="border-b border-blue-100 pb-3 mb-3">
           <h2 class="text-sm font-bold text-slate-800">
                Keranjang Transaksi
            </h2>

            <p class="text-[10px] text-slate-500 mt-0.5">
                Ringkasan transaksi dan pembayaran.
            </p>
        </div>

        <form
            action="{{ route('penjualan.store') }}"
            method="POST"
            id="form-penjualan"
        >
            @csrf
            <input
                type="hidden"
                name="jenis_transaksi"
                id="jenis-transaksi-value"
                value="{{ old('jenis_transaksi', 'non_resep') }}"
            >

            <!-- ================================================= -->
            <!-- NO INVOICE + TANGGAL -->
            <!-- ================================================= -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">
                        No. Invoice
                    </label>

                    <input
                        type="text"
                        name="no_faktur"
                        value="{{ old('no_faktur', 'INV-' . now()->format('Ymd-His')) }}"
                        required
                        class="form-input font-mono font-semibold text-xs"
                    >
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">
                        Tanggal Transaksi
                    </label>

                    <input
                        type="date"
                        name="tanggal"
                        value="{{ old('tanggal', now()->format('Y-m-d')) }}"
                        required
                        class="form-input text-xs"
                    >
                </div>

            </div>

            <!-- ================================================= -->
            <!-- PELANGGAN / MEMBER -->
            <!-- ================================================= -->
            <div class="mb-3 relative">

                <label class="block text-[10px] font-semibold text-gray-500 mb-1">
                    Cari Pelanggan / Member
                    <span class="text-[9px] text-blue-600 font-bold ml-1 font-mono">
                        [F4]
                    </span>
                </label>

                <div class="flex gap-2">

                    <input
                        type="text"
                        id="pencarian-pelanggan"
                        placeholder="Cari nama, Member ID, atau HP..."
                        class="form-input text-xs"
                    >

                    <button
                        type="button"
                        id="btn-tambah-member"
                        class="btn-secondary whitespace-nowrap text-xs px-3"
                    >
                        + Member
                    </button>

                </div>

                <!-- Dropdown hasil pencarian -->
                <div
                    id="hasil-pencarian-pelanggan"
                    class="absolute left-0 right-0 mt-1.5 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto z-10 hidden"
                ></div>

                <input
                    type="hidden"
                    name="pelanggan_id"
                    id="selected-pelanggan-id"
                    value="{{ old('pelanggan_id') }}"
                >

                <input
                    type="hidden"
                    name="pelanggan_nama"
                    id="pelanggan-nama"
                    value="{{ old('pelanggan_nama') }}"
                >

                <input
                    type="hidden"
                    name="pelanggan_telepon"
                    id="pelanggan-telepon"
                    value="{{ old('pelanggan_telepon') }}"
                >

            </div>

            <!-- INFO PELANGGAN -->
            <div
                id="info-pelanggan-terpilih"
                class="mb-3 bg-blue-50/40 border border-blue-100 rounded-lg p-3 text-xs"
            >
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-slate-500">Pelanggan:</span>

                        <strong
                            id="selected-pelanggan-nama"
                            class="text-slate-800 ml-1 font-semibold"
                        >
                            Umum
                        </strong>

                        <span
                            id="selected-pelanggan-member-id"
                            class="font-mono text-blue-700 font-semibold ml-1"
                        ></span>
                    </div>

                    <button
                        type="button"
                        id="btn-reset-pelanggan"
                        class="text-red-500 hover:text-red-700 font-bold text-xs hidden"
                    >
                        &times; Batal
                    </button>
                </div>

                <div
                    id="badge-diskon-member"
                    class="mt-1.5 badge-success inline-block hidden"
                >
                    Diskon Member Aktif:
                    <span id="label-diskon-percent" class="font-bold">0</span>%
                </div>
            </div>

            {{-- Jenis Transaksi - Kolom Kanan --}}
            <div class="mb-4 rounded-lg border border-gray-200 p-3">
                <label class="block text-xs font-semibold text-gray-600 mb-2">
                    Jenis Transaksi
                </label>

                <div class="flex flex-wrap gap-4 text-xs">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="radio"
                            name="jenis_transaksi_kanan"
                            id="jenis-non-resep-kanan"
                            value="non_resep"
                            checked
                            class="text-blue-600"
                        >
                        <span>Non Resep</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="radio"
                            name="jenis_transaksi_kanan"
                            id="jenis-resep-kanan"
                            value="resep"
                            class="text-blue-600"
                        >
                        <span>Resep</span>
                    </label>
                </div>

                {{-- Form resep hanya satu kali --}}
                <div
                    id="form-data-resep"
                    class="hidden mt-3 border-t border-gray-200 pt-3 space-y-3"
                >
                    <p class="text-[11px] text-amber-700">
                        Lengkapi data resep sesuai resep kertas yang diberikan pasien.
                    </p>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">
                            Nama Dokter <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="nama_dokter"
                            id="nama-dokter"
                            maxlength="255"
                            class="form-input text-xs"
                            placeholder="Masukkan nama dokter"
                            value="{{ old('nama_dokter') }}"
                        >
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">
                            ID Dokter <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="id_dokter"
                            id="id-dokter"
                            maxlength="255"
                            class="form-input text-xs"
                            placeholder="Masukkan ID dokter"
                            value="{{ old('id_dokter') }}"
                        >
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">
                            Alamat Lembaga / Klinik <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="alamat_lembaga"
                            id="alamat-lembaga"
                            maxlength="255"
                            class="form-input text-xs"
                            placeholder="Masukkan alamat lembaga atau klinik"
                            value="{{ old('alamat_lembaga') }}"
                        >
                    </div>
                </div>
            </div>


            <!-- ================================================= -->
            <!-- ISI KERANJANG -->
            <!-- ================================================= -->
            <div
                id="keranjang-items"
                class="space-y-2 mb-3 max-h-[280px] overflow-y-auto pr-1"
            ></div>

            <p
                id="keranjang-kosong"
                class="text-xs text-gray-400 mb-3 text-center py-2"
            >
                Keranjang masih kosong.
            </p>


            <!-- ================================================= -->
            <!-- TOTAL -->
            <!-- ================================================= -->
            <div class="border-t pt-3 mb-3 flex justify-between items-center">

                <span class="text-xs font-semibold text-gray-600">
                    Total Tagihan
                </span>

                <span
                    id="total-display"
                    class="text-base font-bold text-gray-800"
                >
                    Rp 0
                </span>

            </div>


            <!-- ================================================= -->
            <!-- PEMBAYARAN -->
            <!-- ================================================= -->
            <div class="border-t pt-3 space-y-2.5 mb-3">

                <div>

                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">
                        Metode Pembayaran
                    </label>

                    <select
                        name="metode_pembayaran"
                        id="metode-pembayaran"
                        class="form-input text-xs"
                        required
                    >
                        <option value="cash">Cash</option>
                        <option value="qris">QRIS</option>
                        <option value="debit">Debit</option>
                        <option value="piutang">Piutang</option>
                    </select>

                </div>

                <!-- JATUH TEMPO (Tepat di bawah Metode Pembayaran, hanya muncul jika Piutang) -->
                <div id="bagian-jatuh-tempo" class="hidden">
                    <label for="input-jatuh-tempo" class="block text-[10px] font-semibold text-gray-500 mb-1">
                        Jatuh Tempo
                        <span class="text-[9px] text-gray-400 font-normal ml-1">
                            (Opsional, default 1 bulan)
                        </span>
                    </label>
                    <input
                        type="date"
                        name="due_date"
                        id="input-jatuh-tempo"
                        class="form-input text-xs"
                    >
                </div>


                <!-- CASH -->
                <div id="bagian-pembayaran-cash">

                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">
                        Bayar (Uang Tunai)
                        <span class="text-[9px] text-blue-600 font-bold ml-1 font-mono">
                            [F8]
                        </span>
                    </label>

                    <input
                        type="number"
                        id="input-bayar"
                        placeholder="Masukkan nominal pembayaran..."
                        class="form-input font-mono font-semibold text-right text-xs"
                        min="0"
                    >

                </div>

                <!-- QRIS -->
                <div id="bagian-pembayaran-qris" class="hidden rounded-lg border border-blue-100 bg-blue-50/60 px-3 py-2">
                    <label class="flex items-center justify-between gap-3 cursor-pointer">
                        <span class="flex items-center gap-2 min-w-0">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-white text-blue-600 border border-blue-100">
                                <x-heroicon-o-qr-code class="w-3.5 h-3.5" />
                            </span>

                            <span>
                                <span class="block text-[10px] font-semibold text-gray-700">
                                    QRIS
                                </span>
                                <span class="block text-[9px] text-gray-400">
                                    Konfirmasi pembayaran sudah sukses
                                </span>
                            </span>
                        </span>

                        <span class="inline-flex items-center gap-1.5 shrink-0">
                            <input
                                type="checkbox"
                                name="qris_lunas"
                                id="qris-lunas"
                                value="1"
                                class="h-3.5 w-3.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                            <span class="text-[10px] font-semibold text-gray-600">
                                Lunas
                            </span>
                        </span>
                    </label>
                </div>

                <!-- Debit -->
                <div id="bagian-pembayaran-debit" class="hidden rounded-lg border border-blue-100 bg-blue-50/60 px-3 py-2">
                    <label class="flex items-center justify-between gap-3 cursor-pointer">
                        <span class="flex items-center gap-2 min-w-0">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-white text-blue-600 border border-blue-100">
                                <x-heroicon-o-credit-card class="w-3.5 h-3.5" />
                            </span>

                            <span>
                                <span class="block text-[10px] font-semibold text-gray-700">
                                    Debit
                                </span>
                                <span class="block text-[9px] text-gray-400">
                                    Konfirmasi pembayaran sudah sukses
                                </span>
                            </span>
                        </span>

                        <span class="inline-flex items-center gap-1.5 shrink-0">
                            <input
                                type="checkbox"
                                name="debit_lunas"
                                id="debit-lunas"
                                value="1"
                                class="h-3.5 w-3.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                            <span class="text-[10px] font-semibold text-gray-600">
                                Lunas
                            </span>
                        </span>
                    </label>
                </div>


                <!-- PIUTANG -->
                <div
                    id="info-pembayaran-piutang"
                    class="hidden bg-amber-50 border border-amber-200 rounded-lg p-2.5 text-xs text-amber-700"
                >
                    Transaksi akan dicatat sebagai
                    <strong>Piutang</strong>.
                    Pembayaran dapat dilakukan kemudian melalui halaman Pelanggan/Member.
                </div>


                <!-- KEMBALIAN -->
                <div
                    id="baris-kembalian"
                    class="flex justify-between items-center text-xs"
                >
                    <span
                        id="label-kembalian"
                        class="font-semibold text-gray-500"
                    >
                        Kembalian:
                    </span>

                    <strong
                        id="display-kembalian"
                        class="text-xs text-gray-400"
                    >
                        Masukkan jumlah pembayaran
                    </strong>
                </div>

            </div>


            <!-- ================================================= -->
            <!-- SIMPAN -->
            <!-- ================================================= -->
            <button
                type="submit"
                class="btn-primary w-full py-2.5 text-center text-xs uppercase tracking-wider"
            >
                Simpan Transaksi
            </button>

        </form>

    </div>

</div>

<!-- Modal Daftar Member Baru (Phase A Restructured) -->
<div id="modal-daftar-member" class="modal-backdrop-custom hidden">
    <div class="modal-container-custom max-w-sm w-full mx-4">
        <div class="modal-header-custom">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">Tambah Member Baru</h3>
            <button type="button" id="btn-close-member-x" class="text-gray-400 hover:text-gray-600 font-bold text-base" aria-label="Tutup modal">&times;</button>
        </div>
        
        <div id="member-error" class="bg-red-50 text-red-700 text-xs p-2 rounded-lg mb-3 hidden border border-red-200"></div>

        <form id="form-daftar-member" class="modal-body-custom space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Nama Member</label>
                <input type="text" id="member-nama" required class="form-input">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Nomor HP</label>
                <input type="text" id="member-telepon" class="form-input">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                    Jenis Member
                </label>

                <select
                    id="member-status"
                    required
                    class="form-input"
                >
                    <option value="">Pilih jenis member...</option>
                    <option value="Member Pelanggan Tetap">
                        Member Pelanggan Tetap
                    </option>
                    <option value="Member Keluarga Nakes">
                        Member Keluarga Nakes
                    </option>
                    <option value="Member Only">
                        Member Only
                    </option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                    Custom Diskon (%)
                    <span class="text-gray-400 font-normal">(Opsional)</span>
                </label>
                <input
                    type="number"
                    id="member-custom-discount"
                    min="0"
                    max="100"
                    step="0.01"
                    placeholder="Kosongkan untuk diskon default 10%"
                    class="form-input"
                >
                <p class="mt-1 text-[10px] text-gray-400">
                    Jika dikosongkan, member menggunakan diskon default 10%. Batas maksimal 100%.
                </p>
            </div>
            <div class="modal-footer-custom">
                <button type="button" id="btn-cancel-member" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">Daftar & Gunakan</button>
            </div>
        </form>
    </div>
</div>

<script>


let pelanggans = @json($pelanggans);
const barangs = @json($barangs);

const cariInput = document.getElementById('cari-barang');
const daftarBarang = document.getElementById('daftar-barang');
const keranjangItems = document.getElementById('keranjang-items');
const keranjangKosong = document.getElementById('keranjang-kosong');
const totalDisplay = document.getElementById('total-display');
const form = document.getElementById('form-penjualan');

// Jenis Transaksi & Data Resep Elements
const radioNonResepKiri = document.getElementById('jenis-non-resep-kiri');
const radioResepKiri = document.getElementById('jenis-resep-kiri');
const radioNonResepKanan = document.getElementById('jenis-non-resep-kanan');
const radioResepKanan = document.getElementById('jenis-resep-kanan');
const hiddenJenisTransaksi = document.getElementById('jenis-transaksi-value');
const formDataResep = document.getElementById('form-data-resep');
const inputNamaDokter = document.getElementById('nama-dokter');
const inputIdDokter = document.getElementById('id-dokter');
const inputAlamatLembaga = document.getElementById('alamat-lembaga');

function getJenisTransaksiAktif() {
    return hiddenJenisTransaksi?.value || 'non_resep';
}

function sinkronkanJenisTransaksi(nilai) {
    const isResep = nilai === 'resep';

    // 1. Perbarui hidden input untuk pengiriman form ke backend
    if (hiddenJenisTransaksi) {
        hiddenJenisTransaksi.value = isResep ? 'resep' : 'non_resep';
    }

    // 2. Sinkronkan radio button kiri dan kanan dua arah
    if (radioNonResepKiri) radioNonResepKiri.checked = !isResep;
    if (radioResepKiri) radioResepKiri.checked = isResep;
    if (radioNonResepKanan) radioNonResepKanan.checked = !isResep;
    if (radioResepKanan) radioResepKanan.checked = isResep;

    // 3. Tampilkan atau sembunyikan form data resep dokter
    if (formDataResep) {
        formDataResep.classList.toggle('hidden', !isResep);
    }

    // 4. Atur status required dan bersihkan data resep jika Non Resep
    if (inputNamaDokter) inputNamaDokter.required = isResep;
    if (inputIdDokter) inputIdDokter.required = isResep;
    if (inputAlamatLembaga) inputAlamatLembaga.required = isResep;

    if (!isResep) {
        if (inputNamaDokter) inputNamaDokter.value = '';
        if (inputIdDokter) inputIdDokter.value = '';
        if (inputAlamatLembaga) inputAlamatLembaga.value = '';
    }

    // 5. Perbarui tampilan daftar barang
    renderDaftarBarang(cariInput ? cariInput.value : '');
}

// Pasang event listener sinkronisasi dua arah untuk semua radio button
[radioNonResepKiri, radioResepKiri, radioNonResepKanan, radioResepKanan].forEach(radio => {
    if (radio) {
        radio.addEventListener('change', () => {
            if (radio.checked) {
                sinkronkanJenisTransaksi(radio.value);
            }
        });
    }
});

// Pelanggan elements
const pelangganSearchInput = document.getElementById('pencarian-pelanggan');
const pelangganSearchHasil = document.getElementById('hasil-pencarian-pelanggan');
const selectedPelangganIdInput = document.getElementById('selected-pelanggan-id');
const pelangganNamaInput = document.getElementById('pelanggan-nama');
const pelangganTeleponInput = document.getElementById('pelanggan-telepon');
const selectedPelangganNama = document.getElementById('selected-pelanggan-nama');
const selectedPelangganMemberId = document.getElementById('selected-pelanggan-member-id');
const btnResetPelanggan = document.getElementById('btn-reset-pelanggan');
const badgeDiskonMember = document.getElementById('badge-diskon-member');
const labelDiskonPercent = document.getElementById('label-diskon-percent');

// Modal elements
const modalDaftarMember = document.getElementById('modal-daftar-member');
const btnTambahMember = document.getElementById('btn-tambah-member');
const btnCancelMember = document.getElementById('btn-cancel-member');
const formDaftarMember = document.getElementById('form-daftar-member');
const memberNamaInput = document.getElementById('member-nama');
const memberTeleponInput = document.getElementById('member-telepon');
const memberStatusInput = document.getElementById('member-status');
const memberError = document.getElementById('member-error');

let cart = {}; // { barang_id: { nama, harga, stok, jumlah } }
let selectedPelanggan = null;

function formatRupiah(angka) {
    return 'Rp ' + Math.round(angka).toLocaleString('id-ID');
}

const infoJumlahBarang = document.getElementById('info-jumlah-barang');

function renderDaftarBarang(filter = '') {
    const query = (typeof filter === 'string' ? filter : (cariInput ? cariInput.value : '')).trim().toLowerCase();

    // Cari berdasarkan nama barang ATAU kode apotek
    const hasil = query
        ? barangs.filter(b => {
            const cariNama = b.nama.toLowerCase().includes(query);
            const cariKode = b.kode_apotek && b.kode_apotek.toLowerCase().includes(query);
            return cariNama || cariKode;
          })
        : barangs;

    // Update info jumlah barang
    if (infoJumlahBarang) {
        if (query) {
            infoJumlahBarang.textContent = hasil.length > 0
                ? `${hasil.length} barang ditemukan`
                : '';
        } else {
            infoJumlahBarang.textContent = `${barangs.length} barang tersedia`;
        }
    }

    daftarBarang.innerHTML = hasil.map(b => `
        <button type="button" data-id="${b.id}"
            class="btn-pilih-barang w-full text-left border border-gray-150 rounded-lg px-2.5 py-2 text-[11px] hover:bg-blue-50 flex justify-between items-center transition-colors gap-2">
            <span class="min-w-0 flex-1">
                <span class="block font-medium text-gray-800 truncate">${b.nama}</span>
                ${b.kode_apotek ? `<span class="font-mono text-[9px] text-slate-400">${b.kode_apotek}</span>` : ''}
                ${b.butuh_resep ? '<span class="text-[9px] bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider ml-1">Resep</span>' : ''}
                ${b.diskon_custom_percent > 0 ? `<span class="text-[9px] bg-red-50 text-red-700 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider ml-1">Promo ${b.diskon_custom_percent}%</span>` : ''}
            </span>
            <span class="text-gray-500 font-medium shrink-0 text-right">
                <span class="block">Stok <strong class="text-gray-700">${b.stok}</strong></span>
                <strong class="text-blue-700 text-[10px]">${formatRupiah(b.harga ?? 0)}</strong>
            </span>
        </button>
    `).join('') || '<p class="text-xs text-gray-400 py-4 text-center">Barang tidak ditemukan.</p>';
}

// Inisialisasi kondisi awal sesuai jenis transaksi aktif
const initialJenis = hiddenJenisTransaksi?.value || 'non_resep';
sinkronkanJenisTransaksi(initialJenis);

function renderKeranjang() {
    const ids = Object.keys(cart);
    keranjangKosong.style.display = ids.length === 0 ? 'block' : 'none';

    const diskonMemberPercent = (selectedPelanggan && selectedPelanggan.is_member && selectedPelanggan.member_aktif) ? selectedPelanggan.diskon_percent : 0;

    keranjangItems.innerHTML = ids.map(id => {
        const item = cart[id];
        const diskonCustomPercent = item.diskon_custom_percent || 0;
        const totalDiskonPercent = Math.min(50, diskonMemberPercent + diskonCustomPercent);
        const nominalDiskon = totalDiskonPercent > 0 ? Math.round((item.harga * item.jumlah) * (totalDiskonPercent / 100)) : 0;
        const subtotal = (item.harga * item.jumlah) - nominalDiskon;
        return `
            <div class="border border-gray-150 rounded-lg p-3 text-xs bg-gray-50">
                <div class="flex justify-between items-start mb-1.5">
                    <span class="font-semibold text-gray-800">${item.nama}</span>
                    <button type="button" class="text-red-500 hover:text-red-700 font-bold text-sm btn-hapus-cart" data-id="${id}">&times;</button>
                </div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="text-gray-500">Qty:</span>
                    <input type="number" min="1" max="${item.stok}" value="${item.jumlah}"
                        class="input-jumlah form-input !w-16 px-2 py-0.5 text-xs text-right" data-id="${id}">
                    <span class="text-gray-400 text-[10px] font-mono">(${formatRupiah(item.harga)}/item)</span>
                </div>
                ${totalDiskonPercent > 0 ? `
                    <div class="text-[10px] space-y-0.5 bg-white border border-gray-100 rounded-lg p-2 mt-1">
                        ${diskonMemberPercent > 0 ? `<div class="text-gray-500">Diskon Member: <span class="font-bold text-gray-700">${diskonMemberPercent}%</span></div>` : ''}
                        ${diskonCustomPercent > 0 ? `<div class="text-gray-500">Diskon Promo: <span class="font-bold text-gray-700">${diskonCustomPercent}%</span></div>` : ''}
                        <div class="font-bold text-green-600">Diterapkan: ${totalDiskonPercent}% (-${formatRupiah(nominalDiskon)})</div>
                    </div>
                ` : ''}
                <div class="text-right text-gray-800 mt-2 font-bold text-xs">${formatRupiah(subtotal)}</div>
                <input type="hidden" name="items[${id}][barang_id]" value="${id}">
                <input type="hidden" name="items[${id}][jumlah]" value="${item.jumlah}">
            </div>
        `;
    }).join('');

    const total = ids.reduce((sum, id) => {
        const item = cart[id];
        const diskonCustomPercent = item.diskon_custom_percent || 0;
        const totalDiskonPercent = Math.min(50, diskonMemberPercent + diskonCustomPercent);
        const nominalDiskon = totalDiskonPercent > 0 ? Math.round((item.harga * item.jumlah) * (totalDiskonPercent / 100)) : 0;
        return sum + (item.harga * item.jumlah) - nominalDiskon;
    }, 0);
    totalDisplay.textContent = formatRupiah(total);
    
    currentTotal = total;
    updateMetodePembayaran();
}

// Payment and Change Calculator
let currentTotal = 0;
const metodePembayaran = document.getElementById('metode-pembayaran');
const bagianPembayaranCash = document.getElementById('bagian-pembayaran-cash');
const bagianJatuhTempo = document.getElementById('bagian-jatuh-tempo');
const inputJatuhTempo = document.getElementById('input-jatuh-tempo');
const infoPembayaranPiutang = document.getElementById('info-pembayaran-piutang');
const bagianPembayaranQris = document.getElementById('bagian-pembayaran-qris');
const bagianPembayaranDebit = document.getElementById('bagian-pembayaran-debit');
const qrisLunas = document.getElementById('qris-lunas');
const debitLunas = document.getElementById('debit-lunas');
const inputBayar = document.getElementById('input-bayar');
const barisKembalian = document.getElementById('baris-kembalian');
const displayKembalian = document.getElementById('display-kembalian');
const labelKembalian = document.getElementById('label-kembalian');
const submitButton = form.querySelector('button[type="submit"]');
const opsiPiutang = metodePembayaran.querySelector('option[value="piutang"]');

function pelangganMemberAktif() {
    return !!(selectedPelanggan && selectedPelanggan.is_member && selectedPelanggan.member_aktif);
}

function updateMetodePembayaran() {
    const metode = metodePembayaran.value;
    const adaKeranjang = Object.keys(cart).length > 0;
    const memberAktif = pelangganMemberAktif();

    if (opsiPiutang) {
        opsiPiutang.disabled = !memberAktif;
    }

    if (metode === 'piutang' && !memberAktif) {
        metodePembayaran.value = 'cash';
        updateMetodePembayaran();
        return;
    }

    bagianPembayaranCash.classList.toggle('hidden', metode !== 'cash');
    bagianPembayaranQris.classList.toggle('hidden', metode !== 'qris');
    bagianPembayaranDebit.classList.toggle('hidden', metode !== 'debit');
    bagianJatuhTempo.classList.toggle('hidden', metode !== 'piutang');
    infoPembayaranPiutang.classList.toggle('hidden', metode !== 'piutang');
    barisKembalian.classList.toggle('hidden', metode !== 'cash');

    if (metode !== 'piutang' && inputJatuhTempo) {
        inputJatuhTempo.value = '';
    }

    if (metode !== 'qris' && qrisLunas) qrisLunas.checked = false;
    if (metode !== 'debit' && debitLunas) debitLunas.checked = false;

    if (metode === 'cash') {
        hitungKembalian();
    } else if (metode === 'piutang') {
        submitButton.disabled = !adaKeranjang || !memberAktif;
    } else {
        submitButton.disabled = !adaKeranjang;
    }
}

function hitungKembalian() {
    if (metodePembayaran.value !== 'cash') return;

    const valStr = inputBayar.value.trim();

    if (!valStr) {
        displayKembalian.textContent = 'Masukkan jumlah pembayaran';
        displayKembalian.className = 'text-xs text-gray-400';
        labelKembalian.textContent = 'Kembalian:';
        submitButton.disabled = true;
        return;
    }

    const bayar = parseFloat(valStr) || 0;
    const selisih = bayar - currentTotal;

    if (selisih < 0) {
        displayKembalian.textContent = '- ' + formatRupiah(Math.abs(selisih));
        displayKembalian.className = 'text-xs font-bold text-red-600';
        labelKembalian.textContent = 'Kurang:';
        submitButton.disabled = true;
    } else if (selisih === 0) {
        displayKembalian.textContent = formatRupiah(0);
        displayKembalian.className = 'text-xs font-bold text-green-600';
        labelKembalian.textContent = 'Pas:';
        submitButton.disabled = Object.keys(cart).length === 0;
    } else {
        displayKembalian.textContent = formatRupiah(selisih);
        displayKembalian.className = 'text-xs font-bold text-blue-600';
        labelKembalian.textContent = 'Kembalian:';
        submitButton.disabled = Object.keys(cart).length === 0;
    }
}

inputBayar.addEventListener('input', hitungKembalian);
metodePembayaran.addEventListener('change', updateMetodePembayaran);

// Pelanggan Search Logic
pelangganSearchInput.addEventListener('input', () => {
    const nilaiAsli = pelangganSearchInput.value.trim();
    const v = nilaiAsli.toLowerCase();

    // Jika user mengetik pelanggan baru dan belum memilih hasil pencarian,
    // simpan nama yang diketik untuk diproses oleh backend.
    if (selectedPelangganIdInput.value === '') {
        pelangganNamaInput.value = nilaiAsli;
        pelangganTeleponInput.value = '';
    }
    if (!v) {
        pelangganSearchHasil.innerHTML = '';
        pelangganSearchHasil.classList.add('hidden');
        return;
    }

    const hasil = pelanggans.filter(p => 
        p.nama.toLowerCase().includes(v) || 
        (p.member_id && p.member_id.toLowerCase().includes(v)) || 
        (p.telepon && p.telepon.toLowerCase().includes(v))
    );

    if (hasil.length > 0) {
        pelangganSearchHasil.innerHTML = hasil.map(p => `
           <button type="button" data-id="${p.id}" class="btn-select-pelanggan w-full text-left px-3.5 py-2.5 text-xs hover:bg-blue-50 border-b border-gray-150 last:border-0 flex justify-between items-center transition-colors">
                <div>
                    <strong class="text-gray-800 font-medium">${p.nama}</strong>
                    ${p.telepon ? `<span class="text-gray-500 block text-[10px] font-mono mt-0.5">Telp: ${p.telepon}</span>` : ''}
                </div>
                <div>
                    ${
                        p.is_member
                            ? `<span class="bg-green-50 text-green-700 px-2 py-0.5 rounded-full font-mono text-[9px] font-bold">
                                ${p.member_id ?? '-'} · MEMBER
                            </span>`
                            : `<span class="bg-red-50 text-red-700 px-2 py-0.5 rounded-full font-mono text-[9px] font-bold">
                                BELUM MEMBER
                            </span>`
                    }
                </div>
            </button>
        `).join('');
        pelangganSearchHasil.classList.remove('hidden');
    } else {
        pelangganSearchHasil.innerHTML = '<p class="text-xs text-gray-400 p-3 text-center">Member tidak ditemukan.</p>';
        pelangganSearchHasil.classList.remove('hidden');
    }
});

// Click result pelanggan
pelangganSearchHasil.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-select-pelanggan');
    if (!btn) return;

    const id = parseInt(btn.dataset.id);
    const pelanggan = pelanggans.find(p => p.id === id);
    if (pelanggan) {
        selectPelanggan(pelanggan);
    }
    pelangganSearchHasil.innerHTML = '';
    pelangganSearchHasil.classList.add('hidden');
    pelangganSearchInput.value = '';
});

// Close dropdown if click outside
document.addEventListener('click', (e) => {
    if (!pelangganSearchInput.contains(e.target) && !pelangganSearchHasil.contains(e.target)) {
        pelangganSearchHasil.classList.add('hidden');
    }
});

function selectPelanggan(pelanggan) {
    selectedPelanggan = pelanggan;
    selectedPelangganIdInput.value = pelanggan.id;
    pelangganNamaInput.value = pelanggan.nama || '';
    pelangganTeleponInput.value = pelanggan.telepon || '';
    
    if (pelanggan.is_member) {
        selectedPelangganMemberId.textContent = `(${pelanggan.member_id})`;
        badgeDiskonMember.classList.remove('hidden');
        labelDiskonPercent.textContent = pelanggan.diskon_percent;
        if (!pelanggan.member_aktif) {
            badgeDiskonMember.className = 'mt-2 badge-neutral inline-block';
            badgeDiskonMember.firstChild.textContent = 'Member TIDAK AKTIF - Diskon: ';
        } else {
            badgeDiskonMember.className = 'mt-2 badge-success inline-block';
            badgeDiskonMember.firstChild.textContent = 'Diskon Member Aktif: ';
        }
    } else {
        selectedPelangganMemberId.textContent = '';
        badgeDiskonMember.classList.add('hidden');
        labelDiskonPercent.textContent = '0';
    }
    
    btnResetPelanggan.classList.remove('hidden');
    renderKeranjang();
}

btnResetPelanggan.addEventListener('click', () => {
    selectedPelanggan = null;

    selectedPelangganIdInput.value = '';
    pelangganNamaInput.value = '';
    pelangganTeleponInput.value = '';

    pelangganSearchInput.value = '';

    selectedPelangganNama.textContent = 'Umum';
    selectedPelangganMemberId.textContent = '';

    badgeDiskonMember.classList.add('hidden');
    labelDiskonPercent.textContent = '0';

    btnResetPelanggan.classList.add('hidden');

    renderKeranjang();
});

// Modal Logic
btnTambahMember.addEventListener('click', () => {
    memberError.classList.add('hidden');
    memberError.textContent = '';
    // prefill name if some custom search was typed and it's not a phone/member id
    const searchText = pelangganSearchInput.value.trim();
    if (searchText && isNaN(searchText) && !searchText.startsWith('MBR-')) {
        memberNamaInput.value = searchText;
    } else {
        memberNamaInput.value = '';
    }
    memberTeleponInput.value = '';
    memberStatusInput.value = '';
    if (memberCustomDiscountInput) memberCustomDiscountInput.value = '';
    modalDaftarMember.classList.remove('hidden');
});

btnCancelMember.addEventListener('click', () => {
    modalDaftarMember.classList.add('hidden');
    if (memberCustomDiscountInput) memberCustomDiscountInput.value = '';
});

const btnCloseMemberX = document.getElementById('btn-close-member-x');
if (btnCloseMemberX) {
    btnCloseMemberX.addEventListener('click', () => {
        modalDaftarMember.classList.add('hidden');
        if (memberCustomDiscountInput) memberCustomDiscountInput.value = '';
    });
}

const memberCustomDiscountInput = document.getElementById('member-custom-discount');

formDaftarMember.addEventListener('submit', (e) => {
    e.preventDefault();
    memberError.classList.add('hidden');
    memberError.textContent = '';

    const nama = memberNamaInput.value.trim();
    const telepon = memberTeleponInput.value.trim();
    const statusMember = memberStatusInput.value;

    if (!statusMember) {
        memberError.classList.remove('hidden');
        memberError.textContent = 'Silakan pilih jenis member.';
        return;
    }

    // Validasi custom diskon
    const customDiscountRaw = memberCustomDiscountInput ? memberCustomDiscountInput.value.trim() : '';
    let customDiscountValue = null;
    if (customDiscountRaw !== '') {
        const parsed = parseFloat(customDiscountRaw);
        if (isNaN(parsed) || parsed < 0 || parsed > 100) {
            memberError.classList.remove('hidden');
            memberError.textContent = 'Custom Diskon harus berupa angka antara 0 dan 100.';
            return;
        }
        customDiscountValue = parsed;
    }

    const payload = {
        nama: nama,
        telepon: telepon,
        status_member: statusMember,
    };

    if (customDiscountValue !== null) {
        payload.custom_discount_percentage = customDiscountValue;
    }

    axios.post('{{ route("pelanggan.register-member") }}', payload)
    .then(response => {
        if (response.data.success) {
            const memberObj = response.data.member;
            
            // Check if member already exists in local list, if so update it, otherwise insert new
            const idx = pelanggans.findIndex(p => p.id === memberObj.id);
            if (idx !== -1) {
                pelanggans[idx] = memberObj;
            } else {
                pelanggans.push(memberObj);
            }

            selectPelanggan(memberObj);
            modalDaftarMember.classList.add('hidden');
            // Reset field custom diskon setelah berhasil
            if (memberCustomDiscountInput) memberCustomDiscountInput.value = '';
        }
    })
    .catch(error => {
        memberError.classList.remove('hidden');
        if (error.response && error.response.data && error.response.data.message) {
            memberError.textContent = error.response.data.message;
        } else if (error.response && error.response.data && error.response.data.errors) {
            const firstError = Object.values(error.response.data.errors)[0];
            memberError.textContent = Array.isArray(firstError) ? firstError[0] : firstError;
        } else {
            memberError.textContent = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        }
    });
});

cariInput.addEventListener('input', () => renderDaftarBarang(cariInput.value));

daftarBarang.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-pilih-barang');
    if (!btn) return;

    const id = btn.dataset.id;
    const barang = barangs.find(b => String(b.id) === id);

    if (cart[id]) {
        if (cart[id].jumlah < barang.stok) cart[id].jumlah++;
    } else {
        cart[id] = { 
            nama: barang.nama, 
            harga: barang.harga ?? 0, 
            stok: barang.stok, 
            jumlah: 1,
            diskon_custom_percent: barang.diskon_custom_percent ?? 0
        };
    }
    renderKeranjang();
});

keranjangItems.addEventListener('input', (e) => {
    const id = e.target.dataset.id;
    if (!id || !cart[id]) return;

    if (e.target.classList.contains('input-jumlah')) {
        let v = parseInt(e.target.value) || 1;
        cart[id].jumlah = Math.min(Math.max(v, 1), cart[id].stok);
    }

    renderKeranjang();
}); 

keranjangItems.addEventListener('click', (e) => {
    if (e.target.classList.contains('btn-hapus-cart')) {
        delete cart[e.target.dataset.id];
        renderKeranjang();
    }
});

form.addEventListener('submit', (e) => {
    if (Object.keys(cart).length === 0) {
        e.preventDefault();
        alert('Keranjang masih kosong.');
        return;
    }

    const jenisAktif = getJenisTransaksiAktif();

    if (jenisAktif === 'resep') {
        if (!inputNamaDokter || !inputNamaDokter.value.trim()) {
            e.preventDefault();
            alert('Nama Dokter wajib diisi untuk transaksi Resep.');
            if (inputNamaDokter) inputNamaDokter.focus();
            return;
        }
        if (!inputIdDokter || !inputIdDokter.value.trim()) {
            e.preventDefault();
            alert('ID Dokter wajib diisi untuk transaksi Resep.');
            if (inputIdDokter) inputIdDokter.focus();
            return;
        }
        if (!inputAlamatLembaga || !inputAlamatLembaga.value.trim()) {
            e.preventDefault();
            alert('Alamat Lembaga / Klinik wajib diisi untuk transaksi Resep.');
            if (inputAlamatLembaga) inputAlamatLembaga.focus();
            return;
        }
    } else {
        // Non Resep: pastikan field dokter tidak berisi data
        if (inputNamaDokter) inputNamaDokter.value = '';
        if (inputIdDokter) inputIdDokter.value = '';
        if (inputAlamatLembaga) inputAlamatLembaga.value = '';
    }
    
    const metode = metodePembayaran.value;

    if (metode === 'cash') {
        const valStr = inputBayar.value.trim();
        if (!valStr) {
            e.preventDefault();
            alert('Masukkan nominal pembayaran terlebih dahulu.');
            return;
        }

        const bayar = parseFloat(valStr) || 0;
        if (bayar < currentTotal) {
            e.preventDefault();
            alert('Pembayaran masih kurang.');
            return;
        }
    }

    if (metode === 'piutang') {
        if (!pelangganMemberAktif()) {
            e.preventDefault();
            alert('Metode pembayaran Piutang hanya dapat digunakan oleh Member.');
            return;
        }

        if (inputJatuhTempo && inputJatuhTempo.value) {
            const inputTgl = form.querySelector('input[name="tanggal"]')?.value;
            if (inputTgl && inputJatuhTempo.value < inputTgl) {
                e.preventDefault();
                alert('Tanggal jatuh tempo tidak boleh lebih awal dari tanggal transaksi.');
                return;
            }
        }
    }

    const btn = form.querySelector('button[type="submit"]');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = 'Menyimpan Transaksi...';
    }
});

// Keyboard Shortcuts Listener (F2, F4, F8, Escape)
document.addEventListener('keydown', (e) => {
    if (e.key === 'F2') {
        e.preventDefault();
        const input = document.getElementById('cari-barang');
        if (input) {
            input.focus();
            input.select();
        }
    } else if (e.key === 'F4') {
        e.preventDefault();
        const input = document.getElementById('pencarian-pelanggan');
        if (input) {
            input.focus();
            input.select();
        }
    } else if (e.key === 'F8') {
        e.preventDefault();
        const input = document.getElementById('input-bayar');
        if (input) {
            input.focus();
            input.select();
        }
    } else if (e.key === 'Escape') {
        if (modalDaftarMember && !modalDaftarMember.classList.contains('hidden')) {
            modalDaftarMember.classList.add('hidden');
        }
    }
});

renderDaftarBarang();
renderKeranjang();
</script>
@endsection


<style>
    .transaksi-card {
        background-color: #ffffff !important;
        border: 1px solid #dbeafe !important;
        border-radius: 12px !important;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.06) !important;
    }
</style>

<style>
    /* Garis biru untuk jenis transaksi yang dipilih */
    label:has(> input[id^="jenis-"]:checked) > span,
    label:has(> input[name^="jenis_transaksi"]:checked) > span {
        border-color: #2563eb !important;
    }

    /* Garis abu-abu untuk yang tidak dipilih */
    label:has(> input[id^="jenis-"]:not(:checked)) > span,
    label:has(> input[name^="jenis_transaksi"]:not(:checked)) > span {
        border-color: #d1d5db;
    }
</style>

<style>
    .jenis-option {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;

        width: 100%;
        min-width: 0;
        padding: 10px 12px;

        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #ffffff;

        color: #475569;
        font-size: 12px;
        font-weight: 600;

        cursor: pointer;
        transition: all 0.15s ease;
    }

    /* Radio tetap terlihat dan berada di dalam tombol */
    .jenis-option input[type="radio"] {
        width: 14px;
        height: 14px;
        margin: 0;
        flex-shrink: 0;
        accent-color: #2563eb;
        cursor: pointer;
    }

    /* Jarak dan ukuran ikon */
    .jenis-option .jenis-icon {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    /* Tombol yang dipilih */
    .jenis-option:has(input:checked) {
        border-color: #2563eb;
        background: #eff6ff;
        color: #1d4ed8;
    }

    /* Hover */
    .jenis-option:hover {
        border-color: #93c5fd;
    }
</style>

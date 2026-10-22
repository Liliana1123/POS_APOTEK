@extends('layouts.app')
@section('title', 'Pengaturan Apotek')

@section('content')
<!-- Page Header -->
<x-page-header title="Pengaturan Apotek" subtitle="Konfigurasi identitas apotek, perizinan farmasi resmi, dan format kop dokumen cetak." />

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    <!-- LEFT COLUMN: Pratinjau Identitas & Struk -->
    <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-20">
        <!-- Kartu Pratinjau Identitas -->
        <div class="card-base !p-0 overflow-hidden border border-slate-200/90 shadow-2xs">
            <!-- Header Kartu Berwarna Biru dengan Font Putih Tegas -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-3.5 flex items-center justify-between !text-white">
                <h3 class="!text-white text-xs font-bold uppercase tracking-wider font-sans" style="color: #ffffff !important;">
                    Pratinjau Identitas
                </h3>
                <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold !text-white bg-white/20 px-2 py-0.5 rounded-full backdrop-blur-xs" style="color: #ffffff !important;">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    Aktif
                </span>
            </div>

            <div class="p-5">
                <div class="flex flex-col items-center text-center pb-2">
                    <!-- Logo Display -->
                    <div class="w-20 h-20 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center p-2 mb-3 overflow-hidden shadow-2xs">
                        <img id="preview-logo-img"
                             src="{{ $apotek->logo ? asset('storage/' . $apotek->logo) : '' }}"
                             alt="Logo Apotek"
                             class="w-full h-full object-contain {{ $apotek->logo ? '' : 'hidden' }}">
                        <div id="preview-logo-placeholder" class="text-slate-400 flex flex-col items-center justify-center {{ $apotek->logo ? 'hidden' : '' }}">
                            <x-heroicon-o-photo class="w-7 h-7 text-slate-300 mb-0.5" />
                            <span class="text-[10px] text-slate-400 font-medium">Tanpa Logo</span>
                        </div>
                    </div>

                    <h4 id="preview-nama-apotek" class="text-sm font-bold text-slate-900 tracking-tight">
                        {{ $apotek->nama_apotek ?: 'Nama Apotek' }}
                    </h4>

                    <div class="mt-1 space-y-0.5 text-xs text-slate-500">
                        <p id="preview-owner">
                            Owner: <span class="font-medium text-slate-700">{{ $apotek->nama_pemilik ?: '-' }}</span>
                        </p>
                        <p id="preview-pj">
                            PJ: <span class="font-medium text-slate-700">{{ $apotek->nama_apoteker_pj ?: '-' }}</span>
                        </p>
                    </div>
                </div>

                <!-- Structured Metadata List -->
                <div class="mt-4 pt-4 border-t border-slate-100 space-y-2 text-xs">
                    <div class="flex justify-between items-start gap-2 py-1 border-b border-slate-50">
                        <span class="text-slate-400 font-medium shrink-0">Alamat</span>
                        <span id="preview-alamat" class="text-right text-slate-700 font-medium leading-relaxed max-w-[70%]">
                            {{ $apotek->alamat_lengkap ?: 'Alamat belum diatur' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center gap-2 py-1 border-b border-slate-50">
                        <span class="text-slate-400 font-medium shrink-0">Telepon</span>
                        <span id="preview-telepon" class="text-right text-slate-700 font-medium">
                            {{ $apotek->telepon ?: '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center gap-2 py-1 border-b border-slate-50">
                        <span class="text-slate-400 font-medium shrink-0">Email</span>
                        <span id="preview-email" class="text-right text-slate-700 font-medium truncate max-w-[70%]">
                            {{ $apotek->email ?: '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center gap-2 py-1 border-b border-slate-50">
                        <span class="text-slate-400 font-medium shrink-0">Izin SIA</span>
                        <span id="preview-sia" class="text-right text-slate-700 font-mono text-[11px]">
                            {{ $apotek->no_izin_sia ?: '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center gap-2 py-1">
                        <span class="text-slate-400 font-medium shrink-0">Izin SIPA</span>
                        <span id="preview-sipa" class="text-right text-slate-700 font-mono text-[11px]">
                            {{ $apotek->no_sipa ?: '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Pratinjau Kop Struk -->
        <div class="card-base !p-0 overflow-hidden border border-slate-200/90 shadow-2xs">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-3.5 flex items-center justify-between !text-white">
                <h3 class="!text-white text-xs font-bold uppercase tracking-wider font-sans" style="color: #ffffff !important;">
                    Pratinjau Kop Struk
                </h3>
                <span class="text-[10px] !text-white font-mono bg-white/20 px-2 py-0.5 rounded backdrop-blur-xs" style="color: #ffffff !important;">Thermal 58/80mm</span>
            </div>

            <div class="p-5">
                <div class="bg-[#fafaf7] border border-dashed border-slate-300 rounded-xl p-4 font-mono text-xs text-slate-700 shadow-2xs">
                    <div class="text-center pb-2.5 border-b border-dashed border-slate-300 space-y-0.5">
                        <img id="receipt-logo"
                             src="{{ $apotek->logo ? asset('storage/' . $apotek->logo) : '' }}"
                             alt="Logo"
                             class="w-8 h-8 object-contain mx-auto mb-1 {{ $apotek->logo ? '' : 'hidden' }}">
                        <p id="receipt-nama" class="font-bold text-slate-900 uppercase text-xs">
                            {{ $apotek->nama_apotek ?: 'NAMA APOTEK' }}
                        </p>
                        <p id="receipt-alamat" class="text-[10px] text-slate-500 leading-tight">
                            {{ $apotek->alamat_lengkap ?: 'Alamat Apotek' }}
                        </p>
                        <p id="receipt-kontak" class="text-[10px] text-slate-500">
                            Telp: {{ $apotek->telepon ?: '-' }}
                        </p>
                        <div class="text-[9px] text-slate-400 pt-0.5 leading-tight">
                            <span>SIA: <span id="receipt-sia">{{ $apotek->no_izin_sia ?: '-' }}</span></span>
                            <span class="mx-1">•</span>
                            <span>SIPA: <span id="receipt-sipa">{{ $apotek->no_sipa ?: '-' }}</span></span>
                        </div>
                    </div>

                    <div class="py-1.5 border-b border-dashed border-slate-300 text-[10px] text-slate-400 flex justify-between">
                        <span>No: TRX-0001</span>
                        <span>{{ date('d/m/Y H:i') }}</span>
                    </div>

                    <p class="pt-2 text-[10px] text-slate-400 text-center italic">
                        Format kop di atas otomatis tercetak pada struk kasir & faktur.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: Formulir Pengaturan -->
    <div class="lg:col-span-8">
        <form action="{{ route('pengaturan.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Profil & Identitas Apotek -->
            <div class="card-base !p-0 overflow-hidden border border-slate-200/90 shadow-2xs">
                <!-- Header Kartu Berwarna Biru dengan Font Putih Tegas -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-3.5 !text-white">
                    <h3 class="!text-white text-xs font-bold uppercase tracking-wider font-sans" style="color: #ffffff !important;">
                        Profil & Kontak Apotek
                    </h3>
                    <p class="text-xs !text-blue-100 mt-0.5" style="color: #dbeafe !important;">Informasi identitas umum, nama pemilik, dan saluran komunikasi resmi apotek.</p>
                </div>

                <div class="p-5 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="input-nama-apotek" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nama Apotek <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <input type="text"
                                   id="input-nama-apotek"
                                   name="nama_apotek"
                                   value="{{ old('nama_apotek', $apotek->nama_apotek) }}"
                                   required
                                   placeholder="Contoh: Apotek Sehat Sentosa"
                                   class="form-input bg-slate-50/40 hover:bg-white focus:bg-white transition-colors @error('nama_apotek') error @enderror">
                            @error('nama_apotek') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="input-nama-pemilik" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nama Pemilik / Owner (PSA)
                            </label>
                            <input type="text"
                                   id="input-nama-pemilik"
                                   name="nama_pemilik"
                                   value="{{ old('nama_pemilik', $apotek->nama_pemilik) }}"
                                   placeholder="Contoh: H. Hendra Wijaya, S.E."
                                   class="form-input bg-slate-50/40 hover:bg-white focus:bg-white transition-colors @error('nama_pemilik') error @enderror">
                            @error('nama_pemilik') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="input-telepon" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nomor Telepon / WhatsApp
                            </label>
                            <input type="text"
                                   id="input-telepon"
                                   name="telepon"
                                   value="{{ old('telepon', $apotek->telepon) }}"
                                   placeholder="Contoh: 08123456789 / (021) 555-0123"
                                   class="form-input bg-slate-50/40 hover:bg-white focus:bg-white transition-colors @error('telepon') error @enderror">
                            @error('telepon') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="input-email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Alamat Email Resmi
                            </label>
                            <input type="email"
                                   id="input-email"
                                   name="email"
                                   value="{{ old('email', $apotek->email) }}"
                                   placeholder="Contoh: info@apoteksehat.com"
                                   class="form-input bg-slate-50/40 hover:bg-white focus:bg-white transition-colors @error('email') error @enderror">
                            @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Panel Inset: Rincian Alamat Apotek -->
                    <div class="bg-slate-50/70 border border-slate-200/80 rounded-xl p-4 space-y-3">
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 font-sans">
                                Rincian Alamat Operasional
                            </h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">Format alamat lengkap untuk kop surat, faktur penjualan, dan struk kasir.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5">
                            <div class="sm:col-span-2 md:col-span-3">
                                <label for="input-alamat-jalan" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Alamat Jalan / Gedung / RT-RW
                                </label>
                                <input type="text"
                                       id="input-alamat-jalan"
                                       name="alamat_jalan"
                                       value="{{ old('alamat_jalan', $apotek->alamat_jalan) }}"
                                       placeholder="Contoh: Jl. Jend. Sudirman No. 45"
                                       class="form-input bg-white @error('alamat_jalan') error @enderror">
                                @error('alamat_jalan') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="input-kelurahan" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Kelurahan / Desa
                                </label>
                                <input type="text"
                                       id="input-kelurahan"
                                       name="kelurahan"
                                       value="{{ old('kelurahan', $apotek->kelurahan) }}"
                                       placeholder="Contoh: Menteng"
                                       class="form-input bg-white @error('kelurahan') error @enderror">
                                @error('kelurahan') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="input-kecamatan" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Kecamatan
                                </label>
                                <input type="text"
                                       id="input-kecamatan"
                                       name="kecamatan"
                                       value="{{ old('kecamatan', $apotek->kecamatan) }}"
                                       placeholder="Contoh: Menteng"
                                       class="form-input bg-white @error('kecamatan') error @enderror">
                                @error('kecamatan') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="input-kota" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Kota / Kabupaten
                                </label>
                                <input type="text"
                                       id="input-kota"
                                       name="kota"
                                       value="{{ old('kota', $apotek->kota) }}"
                                       placeholder="Contoh: Jakarta Pusat"
                                       class="form-input bg-white @error('kota') error @enderror">
                                @error('kota') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="input-provinsi" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Provinsi
                                </label>
                                <input type="text"
                                       id="input-provinsi"
                                       name="provinsi"
                                       value="{{ old('provinsi', $apotek->provinsi) }}"
                                       placeholder="Contoh: DKI Jakarta"
                                       class="form-input bg-white @error('provinsi') error @enderror">
                                @error('provinsi') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="input-kode-pos" class="block text-xs font-semibold text-slate-700 mb-1">
                                    Kode Pos
                                </label>
                                <input type="text"
                                       id="input-kode-pos"
                                       name="kode_pos"
                                       value="{{ old('kode_pos', $apotek->kode_pos) }}"
                                       placeholder="Contoh: 10310"
                                       class="form-input bg-white @error('kode_pos') error @enderror">
                                @error('kode_pos') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Koordinat GPS -->
                        <div class="pt-3 border-t border-slate-200/70">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-2">Koordinat GPS (Opsional)</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label for="input-latitude" class="block text-[11px] font-semibold text-slate-600 mb-1">
                                        Latitude
                                    </label>
                                    <input type="number"
                                           id="input-latitude"
                                           name="latitude"
                                           value="{{ old('latitude', $apotek->latitude) }}"
                                           placeholder="Contoh: -6.2088"
                                           step="any"
                                           min="-90"
                                           max="90"
                                           class="form-input bg-white @error('latitude') error @enderror">
                                    @error('latitude') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="input-longitude" class="block text-[11px] font-semibold text-slate-600 mb-1">
                                        Longitude
                                    </label>
                                    <input type="number"
                                           id="input-longitude"
                                           name="longitude"
                                           value="{{ old('longitude', $apotek->longitude) }}"
                                           placeholder="Contoh: 106.8451"
                                           step="any"
                                           min="-180"
                                           max="180"
                                           class="form-input bg-white @error('longitude') error @enderror">
                                    @error('longitude') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Legalitas & Tenaga Kefarmasian -->
            <div class="card-base !p-0 overflow-hidden border border-slate-200/90 shadow-2xs">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-3.5 !text-white">
                    <h3 class="!text-white text-xs font-bold uppercase tracking-wider font-sans" style="color: #ffffff !important;">
                        Legalitas & Tenaga Kefarmasian
                    </h3>
                    <p class="text-xs !text-blue-100 mt-0.5" style="color: #dbeafe !important;">Nomor izin operasional apotek dan izin praktik apoteker penanggung jawab.</p>
                </div>

                <div class="p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <label for="input-apoteker-pj" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Apoteker Penanggung Jawab
                            </label>
                            <input type="text"
                                   id="input-apoteker-pj"
                                   name="nama_apoteker_pj"
                                   value="{{ old('nama_apoteker_pj', $apotek->nama_apoteker_pj) }}"
                                   placeholder="Contoh: apt. Budi Santoso, S.Farm."
                                   class="form-input bg-slate-50/40 hover:bg-white focus:bg-white transition-colors @error('nama_apoteker_pj') error @enderror">
                            @error('nama_apoteker_pj') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="input-no-sia" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nomor Izin SIA
                            </label>
                            <input type="text"
                                   id="input-no-sia"
                                   name="no_izin_sia"
                                   value="{{ old('no_izin_sia', $apotek->no_izin_sia) }}"
                                   placeholder="Contoh: 503/012/SIA/DPMPTSP/2024"
                                   class="form-input font-mono text-xs bg-slate-50/40 hover:bg-white focus:bg-white transition-colors @error('no_izin_sia') error @enderror">
                            @error('no_izin_sia') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="input-no-sipa" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Nomor SIPA
                            </label>
                            <input type="text"
                                   id="input-no-sipa"
                                   name="no_sipa"
                                   value="{{ old('no_sipa', $apotek->no_sipa) }}"
                                   placeholder="Contoh: 19850101/SIPA_32.73/2023/2001"
                                   class="form-input font-mono text-xs bg-slate-50/40 hover:bg-white focus:bg-white transition-colors @error('no_sipa') error @enderror">
                            @error('no_sipa') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Logo Apotek -->
            <div class="card-base !p-0 overflow-hidden border border-slate-200/90 shadow-2xs">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-3.5 !text-white">
                    <h3 class="!text-white text-xs font-bold uppercase tracking-wider font-sans" style="color: #ffffff !important;">
                        Logo Apotek
                    </h3>
                    <p class="text-xs !text-blue-100 mt-0.5" style="color: #dbeafe !important;">Logo digunakan pada header aplikasi, kartu member, dan bukti cetak faktur.</p>
                </div>

                <div class="p-5">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="w-16 h-16 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center p-1.5 shrink-0 overflow-hidden shadow-2xs">
                            <img id="form-logo-preview"
                                 src="{{ $apotek->logo ? asset('storage/' . $apotek->logo) : '' }}"
                                 alt="Logo"
                                 class="w-full h-full object-contain {{ $apotek->logo ? '' : 'hidden' }}">
                            <div id="form-logo-placeholder" class="text-slate-300 {{ $apotek->logo ? 'hidden' : '' }}">
                                <x-heroicon-o-photo class="w-6 h-6" />
                            </div>
                        </div>

                        <div class="flex-1 w-full space-y-1">
                            <input type="file"
                                   id="input-logo-file"
                                   name="logo"
                                   accept="image/png,image/jpeg,image/webp,image/jpg"
                                   class="form-input file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                            @error('logo') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            <p class="text-[11px] text-slate-400">
                                Format: PNG, JPG, WEBP. Maksimal 2 MB. Kosongkan jika tidak ingin mengubah logo saat ini.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Action Footer Card -->
            <div class="card-base bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 p-4 rounded-xl shadow-2xs">
                <span class="text-xs text-slate-500 text-center sm:text-left">
                    Perubahan informasi akan langsung terbarui di seluruh modul dokumen dan laporan.
                </span>
                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <button type="button"
                            id="btn-reset"
                            class="btn-secondary">
                        Clear
                    </button>
                    <button type="submit" class="btn-primary flex items-center gap-1.5">
                        <x-heroicon-o-check class="w-4 h-4" />
                        <span>Simpan Pengaturan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Real-time Synchronizer Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const inputNama = document.getElementById('input-nama-apotek');
    const inputPemilik = document.getElementById('input-nama-pemilik');
    const inputTelepon = document.getElementById('input-telepon');
    const inputEmail = document.getElementById('input-email');
    const inputApotekerPj = document.getElementById('input-apoteker-pj');
    const inputSia = document.getElementById('input-no-sia');
    const inputSipa = document.getElementById('input-no-sipa');
    const inputLogo = document.getElementById('input-logo-file');

    const inputAlamatJalan = document.getElementById('input-alamat-jalan');
    const inputKelurahan = document.getElementById('input-kelurahan');
    const inputKecamatan = document.getElementById('input-kecamatan');
    const inputKota = document.getElementById('input-kota');
    const inputProvinsi = document.getElementById('input-provinsi');
    const inputKodePos = document.getElementById('input-kode-pos');

    const prevNama = document.getElementById('preview-nama-apotek');
    const prevOwner = document.getElementById('preview-owner');
    const prevAlamat = document.getElementById('preview-alamat');
    const prevTelepon = document.getElementById('preview-telepon');
    const prevEmail = document.getElementById('preview-email');
    const prevPj = document.getElementById('preview-pj');
    const prevSia = document.getElementById('preview-sia');
    const prevSipa = document.getElementById('preview-sipa');

    const prevLogoImg = document.getElementById('preview-logo-img');
    const prevLogoPlaceholder = document.getElementById('preview-logo-placeholder');
    const formLogoPreview = document.getElementById('form-logo-preview');
    const formLogoPlaceholder = document.getElementById('form-logo-placeholder');

    const receiptNama = document.getElementById('receipt-nama');
    const receiptAlamat = document.getElementById('receipt-alamat');
    const receiptKontak = document.getElementById('receipt-kontak');
    const receiptSia = document.getElementById('receipt-sia');
    const receiptSipa = document.getElementById('receipt-sipa');
    const receiptLogo = document.getElementById('receipt-logo');

    function compileAddress() {
        const jalan = inputAlamatJalan ? inputAlamatJalan.value.trim() : '';
        const kel = inputKelurahan ? inputKelurahan.value.trim() : '';
        const kec = inputKecamatan ? inputKecamatan.value.trim() : '';
        const kota = inputKota ? inputKota.value.trim() : '';
        const prov = inputProvinsi ? inputProvinsi.value.trim() : '';
        const pos = inputKodePos ? inputKodePos.value.trim() : '';

        const parts = [];
        if (jalan) parts.push(jalan);
        if (kel) parts.push('Kel. ' + kel);
        if (kec) parts.push('Kec. ' + kec);
        if (kota) parts.push(kota);
        if (prov) parts.push(prov);
        if (pos) parts.push(pos);

        const full = parts.join(', ');
        if (prevAlamat) prevAlamat.textContent = full || 'Alamat belum diatur';
        if (receiptAlamat) receiptAlamat.textContent = full || 'Alamat Apotek';
    }

    [inputAlamatJalan, inputKelurahan, inputKecamatan, inputKota, inputProvinsi, inputKodePos].forEach(el => {
        if (el) el.addEventListener('input', compileAddress);
    });

    if (inputNama) {
        inputNama.addEventListener('input', (e) => {
            const val = e.target.value.trim() || 'Nama Apotek';
            if (prevNama) prevNama.textContent = val;
            if (receiptNama) receiptNama.textContent = val.toUpperCase();
        });
    }

    if (inputPemilik) {
        inputPemilik.addEventListener('input', (e) => {
            const val = e.target.value.trim() || '-';
            if (prevOwner) prevOwner.innerHTML = 'Owner: <span class="font-medium text-slate-700">' + val + '</span>';
        });
    }

    if (inputTelepon) {
        inputTelepon.addEventListener('input', (e) => {
            const val = e.target.value.trim();
            if (prevTelepon) prevTelepon.textContent = val || '-';
            if (receiptKontak) receiptKontak.textContent = 'Telp: ' + (val || '-');
        });
    }

    if (inputEmail) {
        inputEmail.addEventListener('input', (e) => {
            const val = e.target.value.trim();
            if (prevEmail) prevEmail.textContent = val || '-';
        });
    }

    if (inputApotekerPj) {
        inputApotekerPj.addEventListener('input', (e) => {
            const val = e.target.value.trim() || '-';
            if (prevPj) prevPj.innerHTML = 'PJ: <span class="font-medium text-slate-700">' + val + '</span>';
        });
    }

    if (inputSia) {
        inputSia.addEventListener('input', (e) => {
            const val = e.target.value.trim() || '-';
            if (prevSia) prevSia.textContent = val;
            if (receiptSia) receiptSia.textContent = val;
        });
    }

    if (inputSipa) {
        inputSipa.addEventListener('input', (e) => {
            const val = e.target.value.trim() || '-';
            if (prevSipa) prevSipa.textContent = val;
            if (receiptSipa) receiptSipa.textContent = val;
        });
    }

    if (inputLogo) {
        inputLogo.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    if (prevLogoImg) {
                        prevLogoImg.src = e.target.result;
                        prevLogoImg.classList.remove('hidden');
                    }
                    if (prevLogoPlaceholder) prevLogoPlaceholder.classList.add('hidden');
                    if (formLogoPreview) {
                        formLogoPreview.src = e.target.result;
                        formLogoPreview.classList.remove('hidden');
                    }
                    if (formLogoPlaceholder) formLogoPlaceholder.classList.add('hidden');
                    if (receiptLogo) {
                        receiptLogo.src = e.target.result;
                        receiptLogo.classList.remove('hidden');
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    const btnReset = document.getElementById('btn-reset');
    if (btnReset) {
        btnReset.addEventListener('click', (e) => {
            e.preventDefault();
            const konfirmasi = confirm('Apakah Anda yakin ingin mengosongkan seluruh kolom form pengaturan ini?');
            if (konfirmasi) {
                document.querySelectorAll('input[name]:not([type="file"]):not([name="_token"]):not([name="_method"]), textarea[name]').forEach(el => {
                    el.value = '';
                    el.dispatchEvent(new Event('input'));
                });

                if (inputLogo) inputLogo.value = '';

                if (prevLogoImg) {
                    prevLogoImg.src = '';
                    prevLogoImg.classList.add('hidden');
                }
                if (prevLogoPlaceholder) prevLogoPlaceholder.classList.remove('hidden');
                if (formLogoPreview) {
                    formLogoPreview.src = '';
                    formLogoPreview.classList.add('hidden');
                }
                if (formLogoPlaceholder) formLogoPlaceholder.classList.remove('hidden');
                if (receiptLogo) {
                    receiptLogo.src = '';
                    receiptLogo.classList.add('hidden');
                }
            }
        });
    }
});
</script>
@endsection
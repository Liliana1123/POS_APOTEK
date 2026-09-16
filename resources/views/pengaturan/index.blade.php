@extends('layouts.app')
@section('title', 'Pengaturan Apotek')

@section('content')
<!-- Page Header -->
<x-page-header title="Pengaturan Apotek" subtitle="Konfigurasi identitas apotek, perizinan farmasi, dan informasi pada dokumen cetak." />

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    <!-- LEFT COLUMN: Live Preview & Receipt Simulation -->
    <div class="lg:col-span-4 space-y-6">
        <!-- Live Identity Summary Card -->
        <div class="card-base">
            <h3 class="text-sm font-semibold text-gray-900 pb-3 border-b border-gray-100">
                Pratinjau Identitas
            </h3>

            <div class="pt-4 flex flex-col items-center text-center">
                <!-- Logo Display -->
                <div class="w-20 h-20 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center p-2 mb-3 overflow-hidden">
                    <img id="preview-logo-img"
                         src="{{ $apotek->logo ? asset('storage/' . $apotek->logo) : '' }}"
                         alt="Logo Apotek"
                         class="w-full h-full object-contain {{ $apotek->logo ? '' : 'hidden' }}">
                    <div id="preview-logo-placeholder" class="text-gray-400 flex flex-col items-center justify-center {{ $apotek->logo ? 'hidden' : '' }}">
                        <x-heroicon-o-photo class="w-7 h-7 text-gray-300 mb-0.5" />
                        <span class="text-[10px] text-gray-400 font-medium">Tanpa Logo</span>
                    </div>
                </div>

                <h4 id="preview-nama-apotek" class="text-sm font-bold text-gray-900">
                    {{ $apotek->nama_apotek ?: 'Nama Apotek' }}
                </h4>

                <div class="mt-1 space-y-0.5 text-xs text-gray-500">
                    <p id="preview-owner">
                        Owner: <span class="font-medium text-gray-700">{{ $apotek->nama_pemilik ?: '-' }}</span>
                    </p>
                    <p id="preview-pj">
                        PJ: <span class="font-medium text-gray-700">{{ $apotek->nama_apoteker_pj ?: '-' }}</span>
                    </p>
                </div>
            </div>

            <!-- Contact & Address Meta -->
            <div class="mt-4 pt-4 border-t border-gray-100 space-y-2.5 text-xs text-gray-600">
                <div class="flex items-start gap-2.5">
                    <x-heroicon-o-map-pin class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" />
                    <span id="preview-alamat" class="leading-relaxed">{{ $apotek->alamat ?: 'Alamat belum diatur' }}</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <x-heroicon-o-phone class="w-4 h-4 text-gray-400 shrink-0" />
                    <span id="preview-telepon">{{ $apotek->telepon ?: 'Telepon belum diatur' }}</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <x-heroicon-o-envelope class="w-4 h-4 text-gray-400 shrink-0" />
                    <span id="preview-email" class="truncate">{{ $apotek->email ?: 'Email belum diatur' }}</span>
                </div>
            </div>
        </div>

        <!-- Receipt / Struk Simulation Card -->
        <div class="card-base">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900">Pratinjau Kop Struk</h3>
                <span class="text-[10px] text-gray-400 font-mono">Thermal 58/80mm</span>
            </div>

            <div class="mt-3 bg-gray-50 border border-gray-200 rounded-lg p-3.5 font-mono text-xs text-gray-700">
                <div class="text-center pb-2.5 border-b border-dashed border-gray-300 space-y-0.5">
                    <p id="receipt-nama" class="font-bold text-gray-900 uppercase">
                        {{ $apotek->nama_apotek ?: 'NAMA APOTEK' }}
                    </p>
                    <p id="receipt-alamat" class="text-[10px] text-gray-500 leading-tight">
                        {{ $apotek->alamat ?: 'Alamat Apotek' }}
                    </p>
                    <p id="receipt-kontak" class="text-[10px] text-gray-500">
                        Telp: {{ $apotek->telepon ?: '-' }}
                    </p>
                    <div class="text-[9px] text-gray-400 pt-0.5 leading-tight">
                        <span>SIA: <span id="receipt-sia">{{ $apotek->no_izin_sia ?: '-' }}</span></span>
                        <span class="mx-1">•</span>
                        <span>SIPA: <span id="receipt-sipa">{{ $apotek->no_sipa ?: '-' }}</span></span>
                    </div>
                </div>

                <div class="py-1.5 border-b border-dashed border-gray-300 text-[10px] text-gray-400 flex justify-between">
                    <span>No: TRX-0001</span>
                    <span>{{ date('d/m/Y H:i') }}</span>
                </div>

                <p class="pt-2 text-[10px] text-gray-400 text-center italic">
                    Format kop di atas otomatis tercetak pada struk kasir & faktur.
                </p>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: Single Unified Settings Form -->
    <div class="lg:col-span-8">
        <form action="{{ route('pengaturan.update') }}" method="POST" enctype="multipart/form-data" class="card-base space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Profil & Kepemilikan -->
            <div>
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-900">Profil & Kontak Apotek</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Informasi identitas umum, nama pemilik, dan saluran komunikasi resmi.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="input-nama-apotek" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Nama Apotek <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="input-nama-apotek"
                               name="nama_apotek"
                               value="{{ old('nama_apotek', $apotek->nama_apotek) }}"
                               required
                               placeholder="Contoh: Apotek Sehat Sentosa"
                               class="form-input @error('nama_apotek') error @enderror">
                        @error('nama_apotek') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="input-nama-pemilik" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Nama Pemilik / Owner (PSA)
                        </label>
                        <input type="text"
                               id="input-nama-pemilik"
                               name="nama_pemilik"
                               value="{{ old('nama_pemilik', $apotek->nama_pemilik) }}"
                               placeholder="Contoh: H. Hendra Wijaya, S.E."
                               class="form-input @error('nama_pemilik') error @enderror">
                        @error('nama_pemilik') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="input-telepon" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Nomor Telepon / WhatsApp
                        </label>
                        <input type="text"
                               id="input-telepon"
                               name="telepon"
                               value="{{ old('telepon', $apotek->telepon) }}"
                               placeholder="Contoh: 08123456789 / (021) 555-0123"
                               class="form-input @error('telepon') error @enderror">
                        @error('telepon') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="input-email" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Alamat Email
                        </label>
                        <input type="email"
                               id="input-email"
                               name="email"
                               value="{{ old('email', $apotek->email) }}"
                               placeholder="Contoh: info@apoteksehat.com"
                               class="form-input @error('email') error @enderror">
                        @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="input-alamat" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Alamat Lengkap
                        </label>
                        <textarea id="input-alamat"
                                  name="alamat"
                                  rows="2"
                                  placeholder="Alamat jalan, nomor gedung, kelurahan, kecamatan, kota/kabupaten"
                                  class="form-input @error('alamat') error @enderror">{{ old('alamat', $apotek->alamat) }}</textarea>
                        @error('alamat') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- Section 2: Legalitas & Apoteker PJ -->
            <div>
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-900">Legalitas & Tenaga Kefarmasian</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Nomor izin operasional apotek dan izin praktik apoteker penanggung jawab.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="input-no-sia" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Nomor Izin SIA (Surat Izin Apotek)
                        </label>
                        <input type="text"
                               id="input-no-sia"
                               name="no_izin_sia"
                               value="{{ old('no_izin_sia', $apotek->no_izin_sia) }}"
                               placeholder="Contoh: 503/012/SIA/DPMPTSP/2024"
                               class="form-input @error('no_izin_sia') error @enderror">
                        @error('no_izin_sia') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="input-apoteker-pj" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Nama Apoteker PJ (Apt.)
                        </label>
                        <input type="text"
                               id="input-apoteker-pj"
                               name="nama_apoteker_pj"
                               value="{{ old('nama_apoteker_pj', $apotek->nama_apoteker_pj) }}"
                               placeholder="Contoh: apt. Budi Santoso, S.Farm."
                               class="form-input @error('nama_apoteker_pj') error @enderror">
                        @error('nama_apoteker_pj') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="input-no-sipa" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Nomor SIPA (Surat Izin Praktik Apoteker)
                        </label>
                        <input type="text"
                               id="input-no-sipa"
                               name="no_sipa"
                               value="{{ old('no_sipa', $apotek->no_sipa) }}"
                               placeholder="Contoh: 19850101/SIPA_32.73/2023/2001"
                               class="form-input @error('no_sipa') error @enderror">
                        @error('no_sipa') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- Section 3: Logo Apotek -->
            <div>
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-900">Logo Apotek</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Logo digunakan pada header aplikasi, kartu member, dan bukti cetak faktur.</p>
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="w-16 h-16 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center p-1.5 shrink-0 overflow-hidden">
                        <img id="form-logo-preview"
                             src="{{ $apotek->logo ? asset('storage/' . $apotek->logo) : '' }}"
                             alt="Logo"
                             class="w-full h-full object-contain {{ $apotek->logo ? '' : 'hidden' }}">
                        <div id="form-logo-placeholder" class="text-gray-300 {{ $apotek->logo ? 'hidden' : '' }}">
                            <x-heroicon-o-photo class="w-6 h-6" />
                        </div>
                    </div>

                    <div class="flex-1 w-full space-y-1">
                        <input type="file"
                               id="input-logo-file"
                               name="logo"
                               accept="image/png,image/jpeg,image/webp,image/jpg"
                               class="form-input file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                        @error('logo') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        <p class="text-[11px] text-gray-400">
                            Format: PNG, JPG, WEBP. Maksimal 2 MB. Kosongkan jika tidak ingin mengubah logo saat ini.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Action Footer -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="button"
                        id="btn-reset"
                        class="btn-secondary"
                        onclick="resetFormToInitial()">
                    Clear
                </button>
                <button type="submit" class="btn-primary flex items-center gap-1.5">
                    <x-heroicon-o-check class="w-4 h-4" />
                    <span>Simpan Pengaturan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Real-time Synchronizer Script -->
<script>
const __INITIAL_FORM_STATE = {};

document.addEventListener('DOMContentLoaded', () => {
    // Capture initial values for reset functionality
    document.querySelectorAll('input[name]:not([type="file"]), textarea[name]').forEach(el => {
        __INITIAL_FORM_STATE[el.name] = el.value;
    });

    const inputNama = document.getElementById('input-nama-apotek');
    const inputPemilik = document.getElementById('input-nama-pemilik');
    const inputAlamat = document.getElementById('input-alamat');
    const inputTelepon = document.getElementById('input-telepon');
    const inputEmail = document.getElementById('input-email');
    const inputApotekerPj = document.getElementById('input-apoteker-pj');
    const inputSia = document.getElementById('input-no-sia');
    const inputSipa = document.getElementById('input-no-sipa');
    const inputLogo = document.getElementById('input-logo-file');

    const prevNama = document.getElementById('preview-nama-apotek');
    const prevOwner = document.getElementById('preview-owner');
    const prevAlamat = document.getElementById('preview-alamat');
    const prevTelepon = document.getElementById('preview-telepon');
    const prevEmail = document.getElementById('preview-email');
    const prevPj = document.getElementById('preview-pj');
    const prevLogoImg = document.getElementById('preview-logo-img');
    const prevLogoPlaceholder = document.getElementById('preview-logo-placeholder');
    const formLogoPreview = document.getElementById('form-logo-preview');
    const formLogoPlaceholder = document.getElementById('form-logo-placeholder');

    const receiptNama = document.getElementById('receipt-nama');
    const receiptAlamat = document.getElementById('receipt-alamat');
    const receiptKontak = document.getElementById('receipt-kontak');
    const receiptSia = document.getElementById('receipt-sia');
    const receiptSipa = document.getElementById('receipt-sipa');

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
            if (prevOwner) prevOwner.innerHTML = 'Owner: <span class="font-medium text-gray-700">' + val + '</span>';
        });
    }

    if (inputAlamat) {
        inputAlamat.addEventListener('input', (e) => {
            const val = e.target.value.trim();
            if (prevAlamat) prevAlamat.textContent = val || 'Alamat belum diatur';
            if (receiptAlamat) receiptAlamat.textContent = val || 'Alamat Apotek';
        });
    }

    if (inputTelepon) {
        inputTelepon.addEventListener('input', (e) => {
            const val = e.target.value.trim();
            if (prevTelepon) prevTelepon.textContent = val || 'Telepon belum diatur';
            if (receiptKontak) receiptKontak.textContent = 'Telp: ' + (val || '-');
        });
    }

    if (inputEmail) {
        inputEmail.addEventListener('input', (e) => {
            const val = e.target.value.trim();
            if (prevEmail) prevEmail.textContent = val || 'Email belum diatur';
        });
    }

    if (inputApotekerPj) {
        inputApotekerPj.addEventListener('input', (e) => {
            const val = e.target.value.trim() || '-';
            if (prevPj) prevPj.innerHTML = 'PJ: <span class="font-medium text-gray-700">' + val + '</span>';
        });
    }

    if (inputSia) {
        inputSia.addEventListener('input', (e) => {
            if (receiptSia) receiptSia.textContent = e.target.value.trim() || '-';
        });
    }

    if (inputSipa) {
        inputSipa.addEventListener('input', (e) => {
            if (receiptSipa) receiptSipa.textContent = e.target.value.trim() || '-';
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
                };
                reader.readAsDataURL(file);
            }
        });
    }

    window.resetFormToInitial = function () {
        document.querySelectorAll('input[name]:not([type="file"]), textarea[name]').forEach(el => {
            const initial = __INITIAL_FORM_STATE[el.name];
            if (initial !== undefined) {
                el.value = initial;
                el.dispatchEvent(new Event('input'));
            }
        });

        if (inputLogo) inputLogo.value = '';
    };
});
</script>
@endsection

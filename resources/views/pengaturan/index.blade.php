@extends('layouts.app')
@section('title', 'Pengaturan Apotek')

@section('content')
<!-- Page Header -->
<x-page-header title="Pengaturan Apotek" subtitle="Konfigurasi identitas apotek untuk kop nota, struk transaksi, dan laporan resmi.">
    <a href="{{ route('dashboard') }}" class="btn-secondary flex items-center gap-1.5">
        <x-heroicon-o-arrow-left class="w-4 h-4" />
        <span>Kembali</span>
    </a>
</x-page-header>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    <!-- LEFT COLUMN: Live Identity Card & Receipt Preview -->
    <div class="lg:col-span-4 xl:col-span-4 space-y-6">
        <!-- Live Pharmacy Identity Card -->
        <div class="card-base relative overflow-hidden">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-building-storefront class="w-4 h-4 text-blue-600" />
                    <span class="text-xs font-bold text-gray-800 uppercase tracking-wider">Identitas Apotek</span>
                </div>
                <x-badge type="success">Aktif</x-badge>
            </div>

            <div class="flex flex-col items-center text-center">
                <!-- Logo Preview Container -->
                <div class="relative w-24 h-24 mb-3 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 flex items-center justify-center p-2 overflow-hidden shadow-xs group">
                    <img id="preview-logo-img" 
                         src="{{ $apotek->logo ? asset('storage/' . $apotek->logo) : '' }}" 
                         alt="Logo Apotek" 
                         class="w-full h-full object-contain transition-transform duration-200 group-hover:scale-105 {{ $apotek->logo ? '' : 'hidden' }}">
                    <div id="preview-logo-placeholder" class="flex flex-col items-center justify-center text-gray-400 {{ $apotek->logo ? 'hidden' : '' }}">
                        <x-heroicon-o-photo class="w-8 h-8 text-gray-300 mb-1" />
                        <span class="text-[10px] font-medium">Belum Ada Logo</span>
                    </div>
                </div>

                <h3 id="preview-nama-apotek" class="text-sm font-bold text-gray-900 leading-snug">
                    {{ $apotek->nama_apotek ?: 'Nama Apotek' }}
                </h3>
                <div class="flex flex-col gap-0.5 mt-1">
                    <p id="preview-owner" class="text-caption text-slate-700 font-medium">
                        Owner: {{ $apotek->nama_pemilik ?: '-' }}
                    </p>
                    <p id="preview-pj" class="text-caption text-blue-600 font-medium">
                        PJ: {{ $apotek->nama_apoteker_pj ?: '-' }}
                    </p>
                </div>
            </div>

            <!-- Detail List -->
            <div class="mt-4 pt-3 border-t border-gray-100 space-y-2.5 text-xs">
                <div class="flex items-start gap-2 text-gray-600">
                    <x-heroicon-o-map-pin class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" />
                    <span id="preview-alamat" class="line-clamp-2">{{ $apotek->alamat ?: 'Alamat belum diatur' }}</span>
                </div>
                <div class="flex items-center gap-2 text-gray-600">
                    <x-heroicon-o-phone class="w-4 h-4 text-gray-400 shrink-0" />
                    <span id="preview-telepon">{{ $apotek->telepon ?: 'No. Telepon belum diatur' }}</span>
                </div>
                <div class="flex items-center gap-2 text-gray-600">
                    <x-heroicon-o-envelope class="w-4 h-4 text-gray-400 shrink-0" />
                    <span id="preview-email" class="truncate">{{ $apotek->email ?: 'Email belum diatur' }}</span>
                </div>
            </div>
        </div>

        <!-- Receipt / Struk Simulation Card -->
        <div class="card-base bg-gradient-to-b from-white to-slate-50 border-slate-200">
            <div class="flex items-center justify-between pb-2.5 border-b border-gray-200/80 mb-3">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-document-text class="w-4 h-4 text-slate-600" />
                    <span class="text-xs font-bold text-gray-800 uppercase tracking-wider">Simulasi Kop Struk</span>
                </div>
                <span class="text-[10px] text-gray-400 font-mono">58/80 mm</span>
            </div>

            <!-- Mini Thermal Paper Look -->
            <div class="bg-white border border-gray-200 rounded-lg p-3 font-mono text-[11px] text-gray-700 shadow-2xs space-y-1">
                <div class="text-center pb-2 border-b border-dashed border-gray-300">
                    <p id="receipt-nama" class="font-bold text-xs uppercase tracking-wide text-gray-900">
                        {{ $apotek->nama_apotek ?: 'NAMA APOTEK' }}
                    </p>
                    <p id="receipt-alamat" class="text-[10px] text-gray-500 mt-0.5 leading-tight">
                        {{ $apotek->alamat ?: 'Alamat Apotek' }}
                    </p>
                    <p id="receipt-kontak" class="text-[10px] text-gray-500">
                        Telp: {{ $apotek->telepon ?: '-' }}
                    </p>
                    <div class="text-[9px] text-gray-400 mt-1 leading-tight">
                        <span>SIA: <span id="receipt-sia">{{ $apotek->no_izin_sia ?: '-' }}</span></span>
                        <span class="mx-1">•</span>
                        <span>SIPA: <span id="receipt-sipa">{{ $apotek->no_sipa ?: '-' }}</span></span>
                    </div>
                </div>

                <div class="py-1.5 border-b border-dashed border-gray-300 text-[10px] text-gray-500 flex justify-between">
                    <span>No: TRX-SAMPLE-01</span>
                    <span>{{ date('d/m/Y H:i') }}</span>
                </div>

                <div class="pt-1 text-[10px] text-gray-400 text-center italic">
                    (Tampilan kop di atas otomatis terpasang pada nota kasir)
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: Settings Form -->
    <div class="lg:col-span-8 xl:col-span-8">
        <form action="{{ route('pengaturan.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Profil & Kontak Bisnis -->
            <div class="card-base">
                <div class="flex items-center gap-2 pb-3 border-b border-gray-100 mb-4">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">
                        1
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-900">Profil & Kontak Apotek</h4>
                        <p class="text-caption">Informasi umum nama, kepemilikan, dan saluran komunikasi apotek</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 font-sans">
                            Nama Apotek <span class="text-red-500 font-bold">*</span>
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
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 font-sans">
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
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 font-sans">Nomor Telepon / WhatsApp</label>
                        <input type="text" 
                               id="input-telepon"
                               name="telepon" 
                               value="{{ old('telepon', $apotek->telepon) }}" 
                               placeholder="Contoh: 08123456789 / (021) 555-0123"
                               class="form-input @error('telepon') error @enderror">
                        @error('telepon') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 font-sans">Alamat Email</label>
                        <input type="email" 
                               id="input-email"
                               name="email" 
                               value="{{ old('email', $apotek->email) }}" 
                               placeholder="Contoh: kontak@apoteksehat.com"
                               class="form-input @error('email') error @enderror">
                        @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 font-sans">Alamat Lengkap</label>
                        <textarea id="input-alamat"
                                  name="alamat" 
                                  rows="2" 
                                  placeholder="Alamat jalan, nomor gedung, kelurahan, kecamatan, kota/kabupaten"
                                  class="form-input @error('alamat') error @enderror">{{ old('alamat', $apotek->alamat) }}</textarea>
                        @error('alamat') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Legalitas & Penanggung Jawab -->
            <div class="card-base">
                <div class="flex items-center gap-2 pb-3 border-b border-gray-100 mb-4">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                        2
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-900">Legalitas & Apoteker Penanggung Jawab</h4>
                        <p class="text-caption">Nomor izin operasional dan praktisi farmasi penanggung jawab</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 font-sans">Nomor Izin SIA (Surat Izin Apotek)</label>
                        <input type="text" 
                               id="input-no-sia"
                               name="no_izin_sia" 
                               value="{{ old('no_izin_sia', $apotek->no_izin_sia) }}" 
                               placeholder="Contoh: 503/012/SIA/DPMPTSP/2024"
                               class="form-input @error('no_izin_sia') error @enderror">
                        @error('no_izin_sia') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 font-sans">Nama Apoteker PJ (Apt.)</label>
                        <input type="text" 
                               id="input-apoteker-pj"
                               name="nama_apoteker_pj" 
                               value="{{ old('nama_apoteker_pj', $apotek->nama_apoteker_pj) }}" 
                               placeholder="Contoh: apt. Budi Santoso, S.Farm."
                               class="form-input @error('nama_apoteker_pj') error @enderror">
                        @error('nama_apoteker_pj') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 font-sans">Nomor SIPA (Surat Izin Praktik Apoteker)</label>
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

            <!-- Section 3: Branding & Logo Apotek -->
            <div class="card-base">
                <div class="flex items-center gap-2 pb-3 border-b border-gray-100 mb-4">
                    <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-xs">
                        3
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-900">Logo & Branding Apotek</h4>
                        <p class="text-caption">Logo akan ditampilkan pada header aplikasi, kartu member, dan faktur cetak</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="flex-1 w-full">
                            <input type="file" 
                                   id="input-logo-file"
                                   name="logo" 
                                   accept="image/png,image/jpeg,image/webp,image/jpg" 
                                   class="form-input file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                            @error('logo') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            <p class="text-caption text-gray-500 mt-1.5 flex items-center gap-1">
                                <x-heroicon-o-information-circle class="w-3.5 h-3.5 text-gray-400 inline shrink-0" />
                                <span>Format file: PNG, JPG, atau WEBP. Ukuran maksimal 2 MB. Biarkan kosong jika tidak ingin mengubah logo.</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('pengaturan.index') }}" class="btn-secondary">
                    Batal
                </a>
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
document.addEventListener('DOMContentLoaded', () => {
    // Input elements
    const inputNama = document.getElementById('input-nama-apotek');
    const inputPemilik = document.getElementById('input-nama-pemilik');
    const inputAlamat = document.getElementById('input-alamat');
    const inputTelepon = document.getElementById('input-telepon');
    const inputEmail = document.getElementById('input-email');
    const inputApotekerPj = document.getElementById('input-apoteker-pj');
    const inputSia = document.getElementById('input-no-sia');
    const inputSipa = document.getElementById('input-no-sipa');
    const inputLogo = document.getElementById('input-logo-file');

    // Preview targets
    const prevNama = document.getElementById('preview-nama-apotek');
    const prevOwner = document.getElementById('preview-owner');
    const prevAlamat = document.getElementById('preview-alamat');
    const prevTelepon = document.getElementById('preview-telepon');
    const prevEmail = document.getElementById('preview-email');
    const prevPj = document.getElementById('preview-pj');
    const prevLogoImg = document.getElementById('preview-logo-img');
    const prevLogoPlaceholder = document.getElementById('preview-logo-placeholder');

    // Receipt targets
    const receiptNama = document.getElementById('receipt-nama');
    const receiptAlamat = document.getElementById('receipt-alamat');
    const receiptKontak = document.getElementById('receipt-kontak');
    const receiptSia = document.getElementById('receipt-sia');
    const receiptSipa = document.getElementById('receipt-sipa');

    // Sync input events
    if (inputNama) {
        inputNama.addEventListener('input', (e) => {
            const val = e.target.value.trim() || 'Nama Apotek';
            prevNama.textContent = val;
            receiptNama.textContent = val.toUpperCase();
        });
    }

    if (inputPemilik) {
        inputPemilik.addEventListener('input', (e) => {
            const val = e.target.value.trim();
            prevOwner.textContent = 'Owner: ' + (val || '-');
        });
    }

    if (inputAlamat) {
        inputAlamat.addEventListener('input', (e) => {
            const val = e.target.value.trim();
            prevAlamat.textContent = val || 'Alamat belum diatur';
            receiptAlamat.textContent = val || 'Alamat Apotek';
        });
    }

    if (inputTelepon) {
        inputTelepon.addEventListener('input', (e) => {
            const val = e.target.value.trim();
            prevTelepon.textContent = val || 'No. Telepon belum diatur';
            receiptKontak.textContent = 'Telp: ' + (val || '-');
        });
    }

    if (inputEmail) {
        inputEmail.addEventListener('input', (e) => {
            const val = e.target.value.trim();
            prevEmail.textContent = val || 'Email belum diatur';
        });
    }

    if (inputApotekerPj) {
        inputApotekerPj.addEventListener('input', (e) => {
            const val = e.target.value.trim();
            prevPj.textContent = 'PJ: ' + (val || '-');
        });
    }

    if (inputSia) {
        inputSia.addEventListener('input', (e) => {
            receiptSia.textContent = e.target.value.trim() || '-';
        });
    }

    if (inputSipa) {
        inputSipa.addEventListener('input', (e) => {
            receiptSipa.textContent = e.target.value.trim() || '-';
        });
    }

    // Dynamic Image Preview
    if (inputLogo) {
        inputLogo.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    prevLogoImg.src = e.target.result;
                    prevLogoImg.classList.remove('hidden');
                    prevLogoPlaceholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endsection
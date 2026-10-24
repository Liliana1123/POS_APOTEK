@extends('layouts.app')
@section('title', 'WhatsApp Monitor')

@section('content')
<x-page-header
    title="WhatsApp Monitor"
    subtitle="Pantau riwayat pengiriman pesan WhatsApp kepada member dan perkembangan follow-up."
/>

@php
    $hasActiveFilter = !empty($search)
        || !empty($tanggalAwal)
        || !empty($tanggalAkhir)
        || (!empty($kategori) && $kategori !== 'semua')
        || (!empty($status) && $status !== 'semua')
        || (!empty($statusFollowUp) && $statusFollowUp !== 'semua')
        || request()->filled('search')
        || request()->filled('tanggal_awal')
        || request()->filled('tanggal_akhir')
        || (request()->filled('kategori') && request('kategori') !== 'semua')
        || (request()->filled('status') && request('status') !== 'semua')
        || (request()->filled('status_follow_up') && request('status_follow_up') !== 'semua');
@endphp

<!-- Filter & Search Card -->
<x-card-filter
    :action="route('whatsapp-monitor.index')"
    :reset-url="route('whatsapp-monitor.index')"
    :grid="true"
    grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-7"
    :live-search="true"
    :has-reset="$hasActiveFilter">
    @if(request()->filled('per_page'))
        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
    @endif

    <div class="relative sm:col-span-2 lg:col-span-2">
        <input
            type="text"
            name="search"
            id="filterSearchInput"
            value="{{ $search }}"
            placeholder="Cari member, nomor, pesan..."
            class="form-input pr-8"
            autocomplete="off"
            aria-label="Pencarian"
            title="Pencarian"
        >
        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
        </span>
    </div>

    <div class="flex items-center gap-1.5">
        <input
            type="date"
            name="tanggal_awal"
            id="filterTanggalAwalInput"
            value="{{ $tanggalAwal }}"
            class="form-input min-w-0"
            aria-label="Tanggal Awal"
            title="Tanggal Awal"
        >
        <span class="text-sm text-gray-400">-</span>
        <input
            type="date"
            name="tanggal_akhir"
            id="filterTanggalAkhirInput"
            value="{{ $tanggalAkhir }}"
            class="form-input min-w-0"
            aria-label="Tanggal Akhir"
            title="Tanggal Akhir"
        >
    </div>

    <select name="kategori" id="filterKategoriSelect" class="form-input" aria-label="Jenis Pesan" title="Jenis Pesan">
        <option value="semua" @selected(empty($kategori) || $kategori === 'semua')>Jenis: Semua</option>
        <option value="pengingat_piutang" @selected($kategori === 'pengingat_piutang')>Pengingat Piutang</option>
        <option value="promosi_diskon" @selected($kategori === 'promosi_diskon')>Promosi / Diskon</option>
        <option value="ucapan_apresiasi" @selected($kategori === 'ucapan_apresiasi')>Ucapan & Apresiasi</option>
        <option value="informasi_produk" @selected($kategori === 'informasi_produk')>Informasi Produk</option>
        <option value="informasi_pengumuman" @selected($kategori === 'informasi_pengumuman')>Pengumuman</option>
        <option value="pesan_custom" @selected($kategori === 'pesan_custom')>Pesan Custom</option>
    </select>

    <select name="status" id="filterStatusSelect" class="form-input" aria-label="Status Pengiriman" title="Status Pengiriman">
        <option value="semua" @selected(empty($status) || $status === 'semua')>Status: Semua</option>
        <option value="terkirim" @selected($status === 'terkirim')>Terkirim</option>
        <option value="menunggu" @selected($status === 'menunggu')>Menunggu</option>
        <option value="gagal" @selected($status === 'gagal')>Gagal</option>
        <option value="dibatalkan" @selected($status === 'dibatalkan')>Dibatalkan</option>
    </select>

    <select name="status_follow_up" id="filterStatusFollowUpSelect" class="form-input" aria-label="Status Follow-up" title="Status Follow-up">
        <option value="semua" @selected(empty($statusFollowUp) || $statusFollowUp === 'semua')>Follow-up: Semua</option>
        @foreach($statusFollowUpOptions as $val => $lbl)
            <option value="{{ $val }}" @selected($statusFollowUp === $val)>{{ $lbl }}</option>
        @endforeach
    </select>
</x-card-filter>

<!-- Table Custom Wrapper (Pola Penerimaan Barang) -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-full">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="text-center w-20">Aksi</th>
                    <th scope="col" class="text-center w-12">No.</th>
                    <th scope="col" class="whitespace-nowrap">Tanggal Kirim</th>
                    <th scope="col" class="whitespace-nowrap">Nama Member</th>
                    <th scope="col" class="whitespace-nowrap">No. WhatsApp</th>
                    <th scope="col" class="whitespace-nowrap">Jenis Pesan</th>
                    <th scope="col" class="text-center whitespace-nowrap">Status Pengiriman</th>
                    <th scope="col" class="text-center whitespace-nowrap">Status Follow-up</th>
                    <th scope="col" class="whitespace-nowrap">Terakhir Update</th>
                    <th scope="col" class="whitespace-nowrap">Dikirim Oleh</th>
                </tr>
            </thead>
            <tbody class="table-custom-body">
                @forelse($logs as $index => $log)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}" id="row-log-{{ $log->id }}">
                        <!-- Aksi (Detail & Follow-up) -->
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Tombol Detail --}}
                                <button
                                    type="button"
                                    onclick="openWhatsAppDetailModal({{ $log->id }})"
                                    title="Lihat detail pengiriman"
                                    class="btn-secondary !p-1.5 btn-action-detail cursor-pointer"
                                    style="color: #2563EB;">
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </button>

                                {{-- Tombol Update Follow-up --}}
                                <button
                                    type="button"
                                    onclick="openFollowUpModal({{ $log->id }})"
                                    title="Update Status Follow-up"
                                    class="btn-secondary !p-1.5 btn-action-followup cursor-pointer hover:bg-emerald-50 hover:border-emerald-300"
                                    style="color: #059669;">
                                    <x-heroicon-o-chat-bubble-bottom-center-text class="w-4 h-4" />
                                </button>
                            </div>
                        </td>

                        <!-- Nomor Urut -->
                        <td class="text-center font-medium text-gray-500">
                            {{ $logs->firstItem() + $index }}
                        </td>

                        <!-- Tanggal Kirim -->
                        <td class="font-medium text-gray-800 whitespace-nowrap">
                            @tanggalWaktuId($log->waktu_kirim)
                        </td>

                        <!-- Nama Member -->
                        <td class="font-semibold text-gray-800 whitespace-nowrap">
                            {{ $log->nama_member ?: ($log->pelanggan?->nama ?? '-') }}
                        </td>

                        <!-- No. WhatsApp -->
                        <td class="font-mono text-gray-600 whitespace-nowrap">
                            {{ $log->nomor_whatsapp ?: ($log->pelanggan?->telepon ?? '-') }}
                        </td>

                        <!-- Jenis Pesan -->
                        <td class="whitespace-nowrap text-gray-800">
                            {{ $log->kategori_label }}
                        </td>

                        <!-- Status Pengiriman (Aktual dari sistem / WhatsApp provider) -->
                        <td class="text-center whitespace-nowrap">
                            @if($log->status === 'terkirim')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Terkirim
                                </span>
                            @elseif($log->status === 'menunggu')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                    Menunggu
                                </span>
                            @elseif($log->status === 'gagal')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-800 border border-red-200">
                                    Gagal
                                </span>
                            @elseif($log->status === 'dibatalkan')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                    Dibatalkan
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-700">
                                    {{ ucfirst($log->status) }}
                                </span>
                            @endif
                        </td>

                        <!-- Status Follow-up (Terpisah dari status pengiriman) -->
                        <td class="text-center whitespace-nowrap">
                            <span id="row-followup-badge-{{ $log->id }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $log->status_follow_up_badge_class }}">
                                {{ $log->status_follow_up_label }}
                            </span>
                        </td>

                        <!-- Terakhir Update Follow-up -->
                        <td class="whitespace-nowrap text-xs text-gray-600 font-mono">
                            <span id="row-followup-time-{{ $log->id }}">
                                @tanggalWaktuId($log->waktu_follow_up)
                            </span>
                        </td>

                        <!-- Dikirim Oleh -->
                        <td class="text-gray-700 whitespace-nowrap">
                            {{ $log->user?->name ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-12 text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center mb-3">
                                    <x-heroicon-o-chat-bubble-left-right class="w-6 h-6 opacity-70" />
                                </div>
                                <h3 class="text-sm font-bold text-gray-800">Belum ada riwayat pengiriman</h3>
                                <p class="text-xs text-gray-500 mt-1 max-w-sm">
                                    Riwayat pengiriman pesan WhatsApp akan muncul secara otomatis saat admin mengirim pesan perorangan maupun massal.
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-pagination :paginator="$logs" />

<!-- ========================================================================= -->
<!-- MODAL 1: Detail Pengiriman WhatsApp (Dua Kolom) -->
<!-- ========================================================================= -->
<div
    id="modalDetailWhatsApp"
    class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 overflow-y-auto transition-opacity"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modalDetailWhatsAppTitle">
    
    <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-4xl my-auto overflow-hidden animate-in fade-in zoom-in duration-150">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-gray-50/60">
            <h2 id="modalDetailWhatsAppTitle" class="text-base font-bold text-gray-900 font-sans">
                Detail Pengiriman WhatsApp
            </h2>
            <button
                type="button"
                onclick="closeWhatsAppDetailModal()"
                class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-200/60 transition cursor-pointer"
                title="Tutup">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <!-- Modal Body (2 Kolom Seimbang) -->
        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-5 text-xs text-gray-700">
            
            <!-- Kolom 1: Kartu Member & Isi Pesan -->
            <div class="flex flex-col justify-between rounded-xl border border-gray-200 p-4 bg-white shadow-2xs">
                <div>
                    <!-- Header Kartu Member -->
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-xs">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M20.45 3.55A11.9 11.9 0 0012.01 0C5.39 0 .02 5.37.02 12c0 2.11.55 4.18 1.6 6L0 24l6.19-1.62a11.97 11.97 0 005.82 1.5h.01c6.62 0 12-5.37 12-12 0-3.21-1.25-6.23-3.57-8.38zM12.02 21.88h-.01a9.92 9.92 0 01-5.06-1.39l-.36-.21-3.76.99 1-3.66-.23-.38a9.93 9.93 0 01-1.52-5.23c0-5.48 4.46-9.94 9.95-9.94 2.65 0 5.15 1.03 7.03 2.91a9.88 9.88 0 012.91 7.03c0 5.48-4.46 9.94-9.94 9.94zm5.45-7.44c-.3-.15-1.77-.87-2.04-.97-.28-.1-.48-.15-.68.15-.2.3-.78.97-.95 1.17-.18.2-.35.22-.65.07-.3-.15-1.27-.47-2.42-1.5a9.05 9.05 0 01-1.68-2.08c-.18-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.68-1.63-.93-2.24-.24-.58-.49-.5-.67-.51h-.58c-.2 0-.52.07-.79.37-.28.3-1.05 1.03-1.05 2.51s1.08 2.91 1.23 3.12c.15.2 2.12 3.24 5.14 4.54.72.31 1.28.5 1.72.64.72.23 1.38.2 1.9.12.58-.09 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 id="detailHeaderKategori" class="font-bold text-gray-900 text-sm">Pengingat Piutang</h4>
                            </div>
                        </div>
                        <div id="detailHeaderStatusBadge">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                Terkirim
                            </span>
                        </div>
                    </div>

                    <!-- Informasi Dasar Member -->
                    <dl class="space-y-1.5 text-xs">
                        <div class="grid grid-cols-3">
                            <dt class="text-gray-500 font-medium">Nama Member</dt>
                            <dd id="detailMemberNama" class="col-span-2 font-semibold text-gray-900">: -</dd>
                        </div>
                        <div class="grid grid-cols-3">
                            <dt class="text-gray-500 font-medium">No. WhatsApp</dt>
                            <dd id="detailMemberTelepon" class="col-span-2 font-mono text-gray-800">: -</dd>
                        </div>
                        <div class="grid grid-cols-3">
                            <dt class="text-gray-500 font-medium">Tanggal Kirim</dt>
                            <dd id="detailWaktuKirim" class="col-span-2 text-gray-800">: -</dd>
                        </div>
                        <div class="grid grid-cols-3">
                            <dt class="text-gray-500 font-medium">Dikirim Oleh</dt>
                            <dd id="detailPengirimNama" class="col-span-2 text-gray-800">: -</dd>
                        </div>
                        <div class="grid grid-cols-3">
                            <dt class="text-gray-500 font-medium">Template</dt>
                            <dd id="detailTemplateNama" class="col-span-2 text-gray-800">: -</dd>
                        </div>
                    </dl>

                    <!-- Isi Pesan -->
                    <div class="mt-4 pt-3 border-t border-gray-100">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                            Isi Pesan
                        </label>
                        <div id="detailIsiPesanBox" class="p-3 bg-gray-50 rounded-xl border border-gray-200/80 text-xs text-gray-800 font-sans leading-relaxed whitespace-pre-wrap max-h-56 overflow-y-auto">
                            <!-- Teks pesan dinamis -->
                        </div>
                    </div>
                </div>

                <!-- Tombol Salin Pesan -->
                <div class="mt-3 flex justify-end">
                    <button
                        type="button"
                        id="btnSalinPesan"
                        onclick="salinIsiPesan()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold shadow-2xs transition cursor-pointer">
                        <x-heroicon-o-clipboard-document class="w-3.5 h-3.5 text-gray-500" />
                        <span id="btnSalinPesanText">Salin Pesan</span>
                    </button>
                </div>
            </div>

            <!-- Kolom 2: Informasi Pengiriman & Status Follow-up -->
            <div class="rounded-xl border border-gray-200 p-4 bg-white shadow-2xs space-y-4">
                <!-- Sub-kartu 1: Status Pengiriman WhatsApp -->
                <div>
                    <h3 class="font-bold text-gray-900 text-xs uppercase tracking-wider mb-2">Status Pengiriman WhatsApp</h3>
                    <dl class="space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Status Pengiriman</dt>
                            <dd id="detailInfoStatusBadge">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                    Terkirim
                                </span>
                            </dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Waktu Respon</dt>
                            <dd id="detailWaktuRespon" class="font-medium text-gray-800">-</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 mb-0.5">Keterangan Pengiriman</dt>
                            <dd id="detailKeterangan" class="text-gray-700 bg-gray-50 rounded-lg p-2 border border-gray-200/70 font-mono text-[11px] leading-relaxed break-words">
                                Pesan berhasil dikirim ke nomor WhatsApp.
                            </dd>
                        </div>
                    </dl>
                </div>

                <hr class="border-gray-100">

                <!-- Sub-kartu 2: Perkembangan Follow-up -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold text-gray-900 text-xs uppercase tracking-wider">Perkembangan Follow-up</h3>
                        <button
                            type="button"
                            onclick="openFollowUpFromDetail()"
                            class="text-[11px] font-semibold text-emerald-600 hover:text-emerald-700 underline cursor-pointer">
                            Perbarui Status
                        </button>
                    </div>
                    <dl class="space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Status Follow-up</dt>
                            <dd id="detailInfoFollowUpBadge">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-700">
                                    Belum ditindaklanjuti
                                </span>
                            </dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Terakhir Update</dt>
                            <dd id="detailWaktuFollowUp" class="font-medium text-gray-800">-</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Petugas Follow-up</dt>
                            <dd id="detailFollowUpUser" class="font-medium text-gray-800">-</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 mb-0.5">Catatan Terakhir</dt>
                            <dd id="detailCatatanFollowUp" class="text-gray-700 bg-gray-50 rounded-lg p-2 border border-gray-200/70 font-sans text-xs leading-relaxed break-words">
                                -
                            </dd>
                        </div>
                    </dl>
                </div>

                <hr class="border-gray-100">

                <!-- Sub-kartu 3: Detail Transaksi / Faktur Terkait -->
                <div>
                    <h3 class="font-bold text-gray-900 text-xs uppercase tracking-wider mb-2">Detail Transaksi / Faktur</h3>
                    <dl class="space-y-1.5 text-xs">
                        <div class="grid grid-cols-3">
                            <dt class="text-gray-500">Faktur Terkait</dt>
                            <dd id="detailFakturInfo" class="col-span-2 text-gray-800 break-words">-</dd>
                        </div>
                        <div class="grid grid-cols-3">
                            <dt class="text-gray-500">Total Tagihan</dt>
                            <dd id="detailTotalTagihan" class="col-span-2 font-semibold text-gray-900">-</dd>
                        </div>
                    </dl>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-5 py-3 border-t border-gray-200 bg-gray-50/80 flex items-center justify-end">
            <button
                type="button"
                onclick="closeWhatsAppDetailModal()"
                class="btn-secondary py-1.5 px-4 cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: Update Status Follow-up & Riwayat Lengkap -->
<!-- ========================================================================= -->
<div
    id="modalFollowUpWhatsApp"
    class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 overflow-y-auto transition-opacity"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modalFollowUpWhatsAppTitle">
    
    <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-4xl my-auto overflow-hidden animate-in fade-in zoom-in duration-150">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-gray-50/60">
            <div>
                <h2 id="modalFollowUpWhatsAppTitle" class="text-base font-bold text-gray-900 font-sans">
                    Update Status Follow-up Member
                </h2>
                <p class="text-xs text-gray-500 mt-0.5" id="modalFollowUpSubtitle">
                    Catat perkembangan tindak lanjut komunikasi dengan member.
                </p>
            </div>
            <button
                type="button"
                onclick="closeFollowUpModal()"
                class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-200/60 transition cursor-pointer"
                title="Tutup">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <!-- Modal Body (2 Kolom: Form Input Kiri, Riwayat Kanan) -->
        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-5 text-xs text-gray-700">
            
            <!-- Kolom Kiri: Form Update Follow-up -->
            <div class="rounded-xl border border-gray-200 p-4 bg-white shadow-2xs flex flex-col justify-between">
                <div>
                    <!-- Ringkasan Singkat Pesan Pengiriman -->
                    <div class="pb-3 mb-3 border-b border-gray-100">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="font-bold text-sm text-gray-900" id="fuMemberNama">-</span>
                            <span id="fuStatusPengirimanBadge" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                Terkirim
                            </span>
                        </div>
                        <div class="text-gray-500 text-[11px] space-y-0.5">
                            <p>No. WA: <span class="font-mono text-gray-700" id="fuMemberTelepon">-</span></p>
                            <p>Kategori: <span class="font-medium text-gray-700" id="fuKategoriLabel">-</span> • Dikirim: <span id="fuWaktuKirim">-</span></p>
                        </div>
                    </div>

                    <!-- Isi Pesan Pengiriman -->
                    <div class="mb-3">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1 font-sans">
                            Isi Pesan Terkirim
                        </label>
                        <div id="fuIsiPesanBox" class="p-2.5 bg-gray-50 rounded-lg border border-gray-200 text-xs text-gray-700 font-sans leading-relaxed whitespace-pre-wrap max-h-24 overflow-y-auto">
                            -
                        </div>
                    </div>

                    <!-- Status Saat Ini -->
                    <div class="mb-4 bg-gray-50 rounded-lg p-2.5 border border-gray-200/70 flex items-center justify-between">
                        <span class="text-gray-500 font-medium">Status Follow-up Saat Ini:</span>
                        <span id="fuStatusSaatIniBadge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-700">
                            Belum ditindaklanjuti
                        </span>
                    </div>

                    <!-- Form Pembaruan -->
                    <form id="formUpdateFollowUp" onsubmit="submitFollowUpForm(event)" class="space-y-3.5">
                        <input type="hidden" id="fuLogId" value="">

                        <!-- Dropdown Status Baru -->
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-600 mb-1 font-sans">
                                Pilih Status Follow-up Baru <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="fuSelectStatus"
                                name="status_follow_up"
                                required
                                class="form-input text-xs w-full">
                                <option value="" disabled>-- Pilih Status --</option>
                                @foreach($statusFollowUpOptions as $val => $lbl)
                                    <option value="{{ $val }}">{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Catatan Follow-up -->
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-600 mb-1 font-sans">
                                Catatan Hasil Tindak Lanjut (Opsional)
                            </label>
                            <textarea
                                id="fuTextareaCatatan"
                                name="catatan"
                                rows="3"
                                maxlength="1000"
                                placeholder="Contoh: Member berjanji akan melunasi piutang tanggal 25 saat gajian..."
                                class="form-input text-xs w-full resize-none leading-relaxed"></textarea>
                            <div class="flex justify-between items-center text-[10px] text-gray-400 mt-1">
                                <span>Beri catatan detail hasil komunikasi bila diperlukan.</span>
                                <span id="fuCharCounter">0/1000</span>
                            </div>
                        </div>

                        <!-- Tombol Simpan -->
                        <div class="pt-2">
                            <button
                                type="submit"
                                id="fuBtnSubmit"
                                class="btn-primary w-full py-2 flex items-center justify-center gap-2 cursor-pointer shadow-xs !bg-emerald-600 hover:!bg-emerald-700 border-emerald-600">
                                <x-heroicon-o-check class="w-4 h-4" />
                                <span id="fuBtnSubmitText">Simpan Status Follow-up</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Riwayat Perkembangan Follow-up (Timeline) -->
            <div class="rounded-xl border border-gray-200 p-4 bg-white shadow-2xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-2 mb-3 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900 text-xs uppercase tracking-wider">
                            Riwayat Tindak Lanjut
                        </h3>
                        <span class="text-[11px] text-gray-400 font-mono" id="fuHistoryCount">
                            0 riwayat
                        </span>
                    </div>

                    <!-- Timeline List Container -->
                    <div
                        id="fuHistoryContainer"
                        class="space-y-3 max-h-80 overflow-y-auto pr-1">
                        <!-- Item riwayat dinamis -->
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 text-[10px] text-gray-400">
                    <p>Setiap perubahan status akan dicatat sebagai riwayat baru dan tidak akan menghapus catatan terdahulu.</p>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-5 py-3 border-t border-gray-200 bg-gray-50/80 flex justify-end">
            <button
                type="button"
                onclick="closeFollowUpModal()"
                class="btn-secondary py-1.5 px-4 cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>



@push('scripts')
<script>
let currentDetailPesanText = '';
let currentFollowUpLogId = null;

// Character counter untuk textarea catatan
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('fuTextareaCatatan');
    const counter = document.getElementById('fuCharCounter');
    if (textarea && counter) {
        textarea.addEventListener('input', function() {
            counter.textContent = `${this.value.length}/1000`;
        });
    }
});

// =========================================================================
// 1. DETAIL PENGIRIMAN MODAL
// =========================================================================
function openWhatsAppDetailModal(logId) {
    const modal = document.getElementById('modalDetailWhatsApp');
    if (!modal) return;

    currentFollowUpLogId = logId;

    // Reset isi modal sementara
    document.getElementById('detailMemberNama').textContent = ': Memuat...';
    document.getElementById('detailMemberTelepon').textContent = ': -';
    document.getElementById('detailWaktuKirim').textContent = ': -';
    document.getElementById('detailPengirimNama').textContent = ': -';
    document.getElementById('detailTemplateNama').textContent = ': -';
    document.getElementById('detailIsiPesanBox').textContent = 'Memuat isi pesan...';
    document.getElementById('detailWaktuRespon').textContent = '-';
    document.getElementById('detailKeterangan').textContent = '-';
    document.getElementById('detailWaktuFollowUp').textContent = '-';
    document.getElementById('detailFollowUpUser').textContent = '-';
    document.getElementById('detailCatatanFollowUp').textContent = '-';
    document.getElementById('detailFakturInfo').textContent = '-';
    document.getElementById('detailTotalTagihan').textContent = '-';

    modal.classList.remove('hidden');

    const url = `{{ url('whatsapp-monitor') }}/${encodeURIComponent(logId)}`;

    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(async response => {
        if (!response.ok) {
            throw new Error('Gagal mengambil data detail pengiriman WhatsApp.');
        }
        return response.json();
    })
    .then(result => {
        if (!result.status || !result.data) {
            throw new Error(result.message || 'Data tidak valid.');
        }

        const data = result.data;
        currentDetailPesanText = data.isi_pesan || '';

        document.getElementById('detailHeaderKategori').textContent = data.kategori_label || 'Pesan WhatsApp';
        document.getElementById('detailMemberNama').textContent = ': ' + (data.nama_member || '-');
        document.getElementById('detailMemberTelepon').textContent = ': ' + (data.nomor_whatsapp || '-');
        document.getElementById('detailWaktuKirim').textContent = ': ' + (data.waktu_kirim || '-');
        document.getElementById('detailPengirimNama').textContent = ': ' + (data.pengirim_nama || '-');
        document.getElementById('detailTemplateNama').textContent = ': ' + (data.nama_template || '-');
        document.getElementById('detailIsiPesanBox').textContent = data.isi_pesan || '(Pesan kosong)';

        // Status Pengiriman
        const statusBadgeHtml = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold ${data.status_badge_class || 'bg-gray-100 text-gray-700'}">${data.status_label || data.status}</span>`;
        document.getElementById('detailHeaderStatusBadge').innerHTML = statusBadgeHtml;
        document.getElementById('detailInfoStatusBadge').innerHTML = statusBadgeHtml;

        document.getElementById('detailWaktuRespon').textContent = data.waktu_respon || '-';
        document.getElementById('detailKeterangan').textContent = data.keterangan || '-';

        // Status Follow-up
        const followUpBadgeHtml = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold ${data.status_follow_up_badge_class || 'bg-gray-100 text-gray-700'}">${data.status_follow_up_label || 'Belum ditindaklanjuti'}</span>`;
        document.getElementById('detailInfoFollowUpBadge').innerHTML = followUpBadgeHtml;
        document.getElementById('detailWaktuFollowUp').textContent = data.waktu_follow_up || '-';
        document.getElementById('detailFollowUpUser').textContent = data.follow_up_user_nama || '-';
        document.getElementById('detailCatatanFollowUp').textContent = data.catatan_follow_up || '-';

        document.getElementById('detailFakturInfo').textContent = data.faktur_info || '-';
        document.getElementById('detailTotalTagihan').textContent = data.total_tagihan_formatted || '-';
    })
    .catch(err => {
        document.getElementById('detailIsiPesanBox').textContent = 'Terjadi kesalahan saat memuat detail: ' + err.message;
    });
}

function closeWhatsAppDetailModal() {
    const modal = document.getElementById('modalDetailWhatsApp');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function openFollowUpFromDetail() {
    if (!currentFollowUpLogId) return;
    closeWhatsAppDetailModal();
    openFollowUpModal(currentFollowUpLogId);
}

// =========================================================================
// 2. MODAL UPDATE STATUS FOLLOW-UP & RIWAYAT
// =========================================================================
function openFollowUpModal(logId) {
    const modal = document.getElementById('modalFollowUpWhatsApp');
    if (!modal) return;

    currentFollowUpLogId = logId;
    document.getElementById('fuLogId').value = logId;

    // Reset Form
    document.getElementById('fuMemberNama').textContent = 'Memuat...';
    document.getElementById('fuMemberTelepon').textContent = '-';
    document.getElementById('fuKategoriLabel').textContent = '-';
    document.getElementById('fuWaktuKirim').textContent = '-';
    document.getElementById('fuIsiPesanBox').textContent = '-';
    document.getElementById('fuTextareaCatatan').value = '';
    document.getElementById('fuCharCounter').textContent = '0/1000';
    document.getElementById('fuHistoryContainer').innerHTML = '<div class="py-6 text-center text-gray-400">Memuat riwayat follow-up...</div>';

    modal.classList.remove('hidden');

    const url = `{{ url('whatsapp-monitor') }}/${encodeURIComponent(logId)}/follow-up`;

    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(async response => {
        if (!response.ok) {
            throw new Error('Gagal memuat data follow-up.');
        }
        return response.json();
    })
    .then(result => {
        if (!result.status || !result.data) {
            throw new Error(result.message || 'Data tidak valid.');
        }

        const data = result.data;

        document.getElementById('fuMemberNama').textContent = data.nama_member || '-';
        document.getElementById('fuMemberTelepon').textContent = data.nomor_whatsapp || '-';
        document.getElementById('fuKategoriLabel').textContent = data.kategori_label || '-';
        document.getElementById('fuWaktuKirim').textContent = data.waktu_kirim || '-';
        document.getElementById('fuIsiPesanBox').textContent = data.isi_pesan || '(Pesan kosong)';

        // Badge Status Pengiriman
        document.getElementById('fuStatusPengirimanBadge').className = `inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold ${data.status_pengiriman_badge_class || 'bg-gray-100 text-gray-700'}`;
        document.getElementById('fuStatusPengirimanBadge').textContent = data.status_pengiriman_label || data.status_pengiriman;

        // Badge Status Follow-up Saat Ini
        document.getElementById('fuStatusSaatIniBadge').className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold ${data.status_follow_up_badge_class || 'bg-gray-100 text-gray-700'}`;
        document.getElementById('fuStatusSaatIniBadge').textContent = data.status_follow_up_label || 'Belum ditindaklanjuti';

        // Set value dropdown ke status saat ini
        const select = document.getElementById('fuSelectStatus');
        if (select) {
            select.value = data.status_follow_up || 'belum_ditindaklanjuti';
        }

        // Render Riwayat
        renderFollowUpHistory(data.histories || []);
    })
    .catch(err => {
        document.getElementById('fuHistoryContainer').innerHTML = `<div class="p-3 bg-red-50 text-red-600 rounded-lg text-xs">Gagal memuat: ${err.message}</div>`;
    });
}

function closeFollowUpModal() {
    const modal = document.getElementById('modalFollowUpWhatsApp');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function renderFollowUpHistory(histories) {
    const container = document.getElementById('fuHistoryContainer');
    const counter = document.getElementById('fuHistoryCount');
    if (!container) return;

    if (counter) {
        counter.textContent = `${histories.length} riwayat`;
    }

    if (!histories || histories.length === 0) {
        container.innerHTML = `
            <div class="py-8 text-center text-gray-400">
                <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <p class="font-medium text-xs text-gray-600">Belum ada riwayat follow-up</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Pilih status baru dan simpan untuk mencatat perkembangan tindak lanjut pertama.</p>
            </div>
        `;
        return;
    }

    let html = '';
    histories.forEach((item, idx) => {
        const isLatest = idx === 0;
        html += `
            <div class="p-3 rounded-xl border ${isLatest ? 'border-emerald-200 bg-emerald-50/40' : 'border-gray-200 bg-gray-50/50'} relative">
                ${isLatest ? '<span class="absolute top-2 right-2 text-[9px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">Terbaru</span>' : ''}
                <div class="flex items-center gap-1.5 flex-wrap text-xs mb-1.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600">${item.status_sebelum_label || 'Awal'}</span>
                    <span class="text-gray-400 font-bold">&rarr;</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold ${item.status_sesudah_badge_class}">${item.status_sesudah_label}</span>
                </div>
                ${item.catatan && item.catatan !== '-' ? `<p class="text-xs text-gray-800 font-sans my-1.5 bg-white p-2 rounded-lg border border-gray-200/60 leading-relaxed break-words">${item.catatan}</p>` : ''}
                <div class="flex items-center justify-between text-[10px] text-gray-500 mt-1">
                    <span>${item.waktu}</span>
                    <span class="font-medium text-gray-700">Oleh: ${item.admin_nama}</span>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

// =========================================================================
// 3. SUBMIT FORM UPDATE FOLLOW-UP (TRANSACTIONAL)
// =========================================================================
function submitFollowUpForm(event) {
    event.preventDefault();

    if (!currentFollowUpLogId) return;

    const select = document.getElementById('fuSelectStatus');
    const textarea = document.getElementById('fuTextareaCatatan');
    const btnSubmit = document.getElementById('fuBtnSubmit');
    const btnText = document.getElementById('fuBtnSubmitText');

    const statusValue = select.value;
    const catatanValue = textarea.value.trim();

    if (!statusValue) {
        showToast('Pilih salah satu status follow-up.', 'error');
        return;
    }

    // Disable button & loading state
    btnSubmit.disabled = true;
    btnSubmit.classList.add('opacity-70', 'cursor-not-allowed');
    btnText.textContent = 'Menyimpan...';

    const url = `{{ url('whatsapp-monitor') }}/${encodeURIComponent(currentFollowUpLogId)}/follow-up`;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            status_follow_up: statusValue,
            catatan: catatanValue
        })
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Gagal menyimpan status follow-up.');
            throw new Error(errorMsg);
        }
        return data;
    })
    .then(result => {
        if (!result.status || !result.data) {
            throw new Error(result.message || 'Respons tidak valid dari server.');
        }

        const resData = result.data;

        // 1. Update UI Status Saat Ini pada modal
        document.getElementById('fuStatusSaatIniBadge').className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold ${resData.status_follow_up_badge_class}`;
        document.getElementById('fuStatusSaatIniBadge').textContent = resData.status_follow_up_label;

        // 2. Kosongkan textarea catatan
        textarea.value = '';
        document.getElementById('fuCharCounter').textContent = '0/1000';

        // 3. Update Baris Tabel secara instan (tanpa reload halaman)
        const rowBadge = document.getElementById(`row-followup-badge-${currentFollowUpLogId}`);
        if (rowBadge) {
            rowBadge.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold ${resData.status_follow_up_badge_class}`;
            rowBadge.textContent = resData.status_follow_up_label;
        }

        const rowTime = document.getElementById(`row-followup-time-${currentFollowUpLogId}`);
        if (rowTime) {
            rowTime.textContent = resData.waktu_follow_up;
        }

        // 4. Perbarui daftar riwayat pada modal
        const container = document.getElementById('fuHistoryContainer');
        if (resData.new_history) {
            // Muat ulang daftar lengkap agar timeline konsisten
            fetch(`{{ url('whatsapp-monitor') }}/${encodeURIComponent(currentFollowUpLogId)}/follow-up`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(res => {
                if (res.status && res.data) {
                    renderFollowUpHistory(res.data.histories || []);
                }
            });
        }

        showToast(result.message || 'Status follow-up berhasil diperbarui.', 'success');
    })
    .catch(err => {
        showToast(err.message || 'Terjadi kesalahan sistem saat menyimpan follow-up.', 'error');
    })
    .finally(() => {
        btnSubmit.disabled = false;
        btnSubmit.classList.remove('opacity-70', 'cursor-not-allowed');
        btnText.textContent = 'Simpan Status Follow-up';
    });
}

// =========================================================================
// 4. UTILITIES & SALIN PESAN
// =========================================================================
function salinIsiPesan() {
    if (!currentDetailPesanText) return;

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(currentDetailPesanText).then(onCopySuccess).catch(fallbackCopy);
    } else {
        fallbackCopy();
    }
}

function fallbackCopy() {
    const textArea = document.createElement('textarea');
    textArea.value = currentDetailPesanText;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy');
        onCopySuccess();
    } catch (err) {
        alert('Gagal menyalin pesan: ' + err);
    }
    document.body.removeChild(textArea);
}

function onCopySuccess() {
    const btnText = document.getElementById('btnSalinPesanText');
    if (btnText) {
        const originalText = btnText.textContent;
        btnText.textContent = 'Tersalin!';
        setTimeout(() => {
            btnText.textContent = originalText;
        }, 2000);
    }
}

function showToast(message, type = 'success') {
    if (typeof window.showToast === 'function') {
        window.showToast(message, type);
    }
}

// Tutup modal jika tombol ESC ditekan
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeWhatsAppDetailModal();
        closeFollowUpModal();
    }
});
</script>
@endpush
@endsection

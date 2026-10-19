@extends('layouts.app')
@section('title', 'WhatsApp Monitor')

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-5">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 font-sans">WhatsApp Monitor</h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Pantau riwayat pengiriman pesan WhatsApp kepada member.</p>
    </div>
    <div class="flex items-center gap-2.5 flex-wrap">
        <div class="inline-flex items-center gap-2 bg-white border border-gray-200/90 rounded-xl px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-xs">
            <x-heroicon-o-calendar class="w-4 h-4 text-gray-500 shrink-0" />
            <span>
                @if($tanggalAwal && $tanggalAkhir)
                    {{ \Carbon\Carbon::parse($tanggalAwal)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($tanggalAkhir)->translatedFormat('d M Y') }}
                @elseif($tanggalAwal)
                    Mulai {{ \Carbon\Carbon::parse($tanggalAwal)->translatedFormat('d M Y') }}
                @elseif($tanggalAkhir)
                    Hingga {{ \Carbon\Carbon::parse($tanggalAkhir)->translatedFormat('d M Y') }}
                @else
                    {{ now()->translatedFormat('d M Y') }}
                @endif
            </span>
        </div>
        <div class="inline-flex items-center gap-2 bg-white border border-gray-200/90 rounded-xl px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-xs">
            <span class="w-2 h-2 rounded-full {{ $status === 'terkirim' ? 'bg-emerald-500' : ($status === 'gagal' ? 'bg-red-500' : ($status === 'menunggu' ? 'bg-blue-500' : ($status === 'dibatalkan' ? 'bg-gray-400' : 'bg-blue-600'))) }}"></span>
            <span>
                @if($status === 'terkirim') Terkirim
                @elseif($status === 'menunggu') Menunggu
                @elseif($status === 'gagal') Gagal
                @elseif($status === 'dibatalkan') Dibatalkan
                @else Semua Status
                @endif
            </span>
        </div>
    </div>
</div>

<!-- Panel Filter -->
<div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-4 sm:p-5 mb-5">
    <form id="filterWhatsAppForm" method="GET" action="{{ route('whatsapp-monitor.index') }}" class="space-y-4">
        <!-- Input Pencarian Utama -->
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <x-heroicon-o-magnifying-glass class="w-4 h-4" />
            </span>
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Cari nama member, nomor, atau isi pesan..."
                class="w-full pl-10 pr-4 py-2.5 bg-gray-50/70 border border-gray-200 rounded-xl text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
        </div>

        <!-- Filter Baris 2: Dropdown, Tanggal, & Tombol Aksi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 items-end">
            <!-- Filter Status Pengiriman -->
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1.5 font-sans">
                    Status Pengiriman
                </label>
                <select name="status" class="w-full py-2 px-3 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                    <option value="semua" @selected(empty($status) || $status === 'semua')>Semua Status</option>
                    <option value="terkirim" @selected($status === 'terkirim')>Terkirim</option>
                    <option value="menunggu" @selected($status === 'menunggu')>Menunggu / Diproses</option>
                    <option value="gagal" @selected($status === 'gagal')>Gagal</option>
                    <option value="dibatalkan" @selected($status === 'dibatalkan')>Dibatalkan</option>
                </select>
            </div>

            <!-- Filter Jenis Pesan -->
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1.5 font-sans">
                    Jenis Pesan
                </label>
                <select name="kategori" class="w-full py-2 px-3 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                    <option value="semua" @selected(empty($kategori) || $kategori === 'semua')>Semua Jenis Pesan</option>
                    <option value="pengingat_piutang" @selected($kategori === 'pengingat_piutang')>Pengingat Piutang</option>
                    <option value="promosi_diskon" @selected($kategori === 'promosi_diskon')>Promosi / Diskon</option>
                    <option value="ucapan_apresiasi" @selected($kategori === 'ucapan_apresiasi')>Ucapan & Apresiasi</option>
                    <option value="informasi_produk" @selected($kategori === 'informasi_produk')>Informasi Produk</option>
                    <option value="informasi_pengumuman" @selected($kategori === 'informasi_pengumuman')>Pengumuman</option>
                    <option value="pesan_custom" @selected($kategori === 'pesan_custom')>Pesan Custom</option>
                </select>
            </div>

            <!-- Tanggal Awal -->
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1.5 font-sans">
                    Tanggal Awal
                </label>
                <div class="relative">
                    <input
                        type="date"
                        name="tanggal_awal"
                        value="{{ $tanggalAwal }}"
                        class="w-full py-2 px-3 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
            </div>

            <!-- Tanggal Akhir -->
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1.5 font-sans">
                    Tanggal Akhir
                </label>
                <div class="relative">
                    <input
                        type="date"
                        name="tanggal_akhir"
                        value="{{ $tanggalAkhir }}"
                        class="w-full py-2 px-3 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
            </div>

            <!-- Tombol Terapkan Filter -->
            <div>
                <input type="hidden" name="per_page" id="filterPerPageInput" value="{{ $perPage }}">
                <button
                    type="submit"
                    class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition cursor-pointer">
                    <x-heroicon-o-funnel class="w-4 h-4 shrink-0" />
                    <span>Terapkan Filter</span>
                </button>
            </div>

            <!-- Tombol Reset -->
            <div>
                <a
                    href="{{ route('whatsapp-monitor.index') }}"
                    class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-4 bg-white hover:bg-gray-50 border border-gray-200 text-gray-600 hover:text-gray-800 rounded-xl text-xs font-semibold shadow-xs transition">
                    <x-heroicon-o-arrow-path class="w-4 h-4 shrink-0 text-gray-500" />
                    <span>Reset</span>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Ringkasan Statistik KPI Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 mb-5">
    <!-- Card Terkirim -->
    <div class="bg-white rounded-2xl border border-gray-200/90 p-4 shadow-xs flex items-center justify-between">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-xs shrink-0">
                <svg class="w-5 h-5 -rotate-45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500">Terkirim</p>
                <div class="flex items-baseline gap-2 mt-0.5">
                    <span class="text-2xl font-bold text-gray-900 font-sans tracking-tight">{{ $stats['terkirim'] }}</span>
                    <span class="text-xs font-semibold text-gray-400">({{ $stats['terkirim_pct'] }}%)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Menunggu -->
    <div class="bg-white rounded-2xl border border-gray-200/90 p-4 shadow-xs flex items-center justify-between">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-full bg-blue-500 text-white flex items-center justify-center shadow-xs shrink-0">
                <x-heroicon-o-clock class="w-5 h-5 stroke-[2.5]" />
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500">Menunggu</p>
                <div class="flex items-baseline gap-2 mt-0.5">
                    <span class="text-2xl font-bold text-gray-900 font-sans tracking-tight">{{ $stats['menunggu'] }}</span>
                    <span class="text-xs font-semibold text-gray-400">({{ $stats['menunggu_pct'] }}%)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Gagal -->
    <div class="bg-white rounded-2xl border border-gray-200/90 p-4 shadow-xs flex items-center justify-between">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-full bg-red-500 text-white flex items-center justify-center shadow-xs shrink-0">
                <x-heroicon-o-x-mark class="w-5 h-5 stroke-[2.5]" />
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500">Gagal</p>
                <div class="flex items-baseline gap-2 mt-0.5">
                    <span class="text-2xl font-bold text-gray-900 font-sans tracking-tight">{{ $stats['gagal'] }}</span>
                    <span class="text-xs font-semibold text-gray-400">({{ $stats['gagal_pct'] }}%)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Dibatalkan -->
    <div class="bg-white rounded-2xl border border-gray-200/90 p-4 shadow-xs flex items-center justify-between">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-full bg-slate-500 text-white flex items-center justify-center shadow-xs shrink-0">
                <svg class="w-5 h-5 stroke-[2.5]" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="12" cy="12" r="9"></circle>
                    <line x1="5.6" y1="5.6" x2="18.4" y2="18.4"></line>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500">Dibatalkan</p>
                <div class="flex items-baseline gap-2 mt-0.5">
                    <span class="text-2xl font-bold text-gray-900 font-sans tracking-tight">{{ $stats['dibatalkan'] }}</span>
                    <span class="text-xs font-semibold text-gray-400">({{ $stats['dibatalkan_pct'] }}%)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Info Periode -->
    <div class="bg-blue-50/70 rounded-2xl border border-blue-200/80 p-4 shadow-xs flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
            <x-heroicon-o-information-circle class="w-5 h-5" />
        </div>
        <p class="text-xs text-blue-900 leading-snug font-medium">
            Total <strong>{{ $stats['total'] }}</strong> pengiriman pesan WhatsApp dalam periode yang dipilih.
        </p>
    </div>
</div>

<!-- Tabel Riwayat WhatsApp -->
<div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="bg-blue-50/70 border-b border-gray-200 text-gray-600 font-semibold tracking-wide">
                    <th class="py-3 px-3 text-center w-14">Aksi</th>
                    <th class="py-3 px-3 text-center w-14">No.</th>
                    <th class="py-3 px-4 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1">
                            Tanggal Kirim
                            <x-heroicon-o-chevron-up-down class="w-3.5 h-3.5 text-gray-400" />
                        </span>
                    </th>
                    <th class="py-3 px-4 whitespace-nowrap">Nama Member</th>
                    <th class="py-3 px-4 whitespace-nowrap">No. WhatsApp</th>
                    <th class="py-3 px-4 whitespace-nowrap">Jenis Pesan</th>
                    <th class="py-3 px-4 text-center whitespace-nowrap">Status Pengiriman</th>
                    <th class="py-3 px-4 whitespace-nowrap">Dikirim Oleh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @forelse($logs as $index => $log)
                    <tr class="hover:bg-blue-50/40 transition-colors">
                        <!-- Aksi Detail -->
                        <td class="py-3 px-3 text-center">
                            <button
                                type="button"
                                onclick="openWhatsAppDetailModal({{ $log->id }})"
                                title="Lihat detail pengiriman"
                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-600 hover:text-white transition shadow-2xs cursor-pointer">
                                <x-heroicon-o-eye class="w-4 h-4" />
                            </button>
                        </td>

                        <!-- Nomor Urut -->
                        <td class="py-3 px-3 text-center font-medium text-gray-500">
                            {{ $logs->firstItem() + $index }}
                        </td>

                        <!-- Tanggal Kirim -->
                        <td class="py-3 px-4 font-medium text-gray-800 whitespace-nowrap">
                            {{ $log->waktu_kirim ? $log->waktu_kirim->translatedFormat('d M Y, H:i') : '-' }}
                        </td>

                        <!-- Nama Member -->
                        <td class="py-3 px-4 font-semibold text-gray-900 whitespace-nowrap">
                            {{ $log->nama_member }}
                        </td>

                        <!-- No. WhatsApp -->
                        <td class="py-3 px-4 font-mono text-gray-600 whitespace-nowrap">
                            {{ $log->nomor_whatsapp }}
                        </td>

                        <!-- Jenis Pesan -->
                        <td class="py-3 px-4 whitespace-nowrap text-gray-800">
                            {{ $log->kategori_label }}
                        </td>

                        <!-- Status Pengiriman -->
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @if($log->status === 'terkirim')
                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Terkirim
                                </span>
                            @elseif($log->status === 'menunggu')
                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                    Menunggu
                                </span>
                            @elseif($log->status === 'gagal')
                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-800 border border-red-200">
                                    Gagal
                                </span>
                            @elseif($log->status === 'dibatalkan')
                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                    Dibatalkan
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700">
                                    {{ ucfirst($log->status) }}
                                </span>
                            @endif
                        </td>

                        <!-- Dikirim Oleh -->
                        <td class="py-3 px-4 text-gray-700 whitespace-nowrap">
                            {{ $log->user?->name ?? 'aku admin' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center">
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

    <!-- Table Footer: Per-page Selector & Pagination -->
    <div class="p-4 border-t border-gray-200/80 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2 text-gray-600">
            <span>Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} data</span>
            <span class="text-gray-300">|</span>
            <label class="flex items-center gap-1.5">
                <span>Per halaman:</span>
                <select
                    id="selectPerPage"
                    onchange="changePerPage(this.value)"
                    class="py-1 px-2 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="10" @selected($perPage == 10)>10</option>
                    <option value="25" @selected($perPage == 25)>25</option>
                    <option value="50" @selected($perPage == 50)>50</option>
                    <option value="100" @selected($perPage == 100)>100</option>
                </select>
            </label>
        </div>

        <div>
            {{ $logs->links('vendor.pagination.apotek') }}
        </div>
    </div>
</div>

<!-- Modal Popup Detail Pengiriman WhatsApp -->
<div
    id="modalDetailWhatsApp"
    class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 overflow-y-auto transition-opacity"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modalDetailWhatsAppTitle">
    
    <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-5xl my-auto overflow-hidden animate-in fade-in zoom-in duration-150">
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

        <!-- Modal Body (3 Kolom sesuai acuan gambar referensi) -->
        <div class="p-5 sm:p-6 grid grid-cols-1 lg:grid-cols-12 gap-5 text-xs text-gray-700">
            
            <!-- Kolom 1: Kartu Member & Isi Pesan (lg:col-span-5) -->
            <div class="lg:col-span-5 flex flex-col justify-between rounded-xl border border-gray-200 p-4 bg-white shadow-2xs">
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

            <!-- Kolom 2: Informasi Pengiriman & Status Respons (lg:col-span-4) -->
            <div class="lg:col-span-4 rounded-xl border border-gray-200 p-4 bg-white shadow-2xs space-y-4">
                <!-- Sub-kartu 1: Informasi Pengiriman -->
                <div>
                    <h3 class="font-bold text-gray-900 text-xs uppercase tracking-wider mb-2">Informasi Pengiriman</h3>
                    <dl class="space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Status</dt>
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
                            <dt class="text-gray-500 mb-0.5">Keterangan</dt>
                            <dd id="detailKeterangan" class="text-gray-700 bg-gray-50 rounded-lg p-2 border border-gray-200/70 font-mono text-[11px] leading-relaxed break-words">
                                Pesan berhasil dikirim ke nomor WhatsApp.
                            </dd>
                        </div>
                    </dl>
                </div>

                <hr class="border-gray-100">

                <!-- Sub-kartu 2: Detail Pesan & Transaksi -->
                <div>
                    <h3 class="font-bold text-gray-900 text-xs uppercase tracking-wider mb-2">Detail Pesan</h3>
                    <dl class="space-y-1.5 text-xs">
                        <div class="grid grid-cols-3">
                            <dt class="text-gray-500">Jenis Pesan</dt>
                            <dd id="detailInfoKategori" class="col-span-2 font-medium text-gray-800">-</dd>
                        </div>
                        <div class="grid grid-cols-3">
                            <dt class="text-gray-500">Template</dt>
                            <dd id="detailInfoTemplate" class="col-span-2 text-gray-800">-</dd>
                        </div>
                        <div class="grid grid-cols-3">
                            <dt class="text-gray-500">Faktur Dipilih</dt>
                            <dd id="detailFakturInfo" class="col-span-2 font-mono text-gray-800">-</dd>
                        </div>
                        <div class="grid grid-cols-3">
                            <dt class="text-gray-500">Total Tagihan</dt>
                            <dd id="detailTotalTagihan" class="col-span-2 font-semibold text-gray-900">-</dd>
                        </div>
                    </dl>
                </div>

                <hr class="border-gray-100">

                <!-- Sub-kartu 3: Pengirim -->
                <div>
                    <h3 class="font-bold text-gray-900 text-xs uppercase tracking-wider mb-2">Pengirim</h3>
                    <dl class="space-y-1.5 text-xs">
                        <div class="grid grid-cols-3">
                            <dt class="text-gray-500">Nama</dt>
                            <dd id="detailSenderNama" class="col-span-2 font-semibold text-gray-800">-</dd>
                        </div>
                        <div class="grid grid-cols-3">
                            <dt class="text-gray-500">Role</dt>
                            <dd id="detailSenderRole" class="col-span-2 text-gray-600">-</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Kolom 3: Panduan Status Pengiriman & Info (lg:col-span-3) -->
            <div class="lg:col-span-3 space-y-4">
                <!-- Panduan Status Pengiriman -->
                <div class="rounded-xl border border-gray-200 p-4 bg-white shadow-2xs">
                    <h3 class="font-bold text-gray-900 text-xs uppercase tracking-wider mb-3">Status Pengiriman</h3>
                    <div class="space-y-3">
                        <div class="flex items-start gap-2.5">
                            <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5 -rotate-45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <line x1="22" y1="2" x2="11" y2="13"></line>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                </svg>
                            </div>
                            <div>
                                <span class="inline-block font-semibold text-emerald-800 text-[11px]">Terkirim</span>
                                <p class="text-[11px] text-gray-500 leading-snug">Pesan berhasil terkirim ke nomor WhatsApp.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center shrink-0 mt-0.5">
                                <x-heroicon-o-clock class="w-3.5 h-3.5 stroke-[2.5]" />
                            </div>
                            <div>
                                <span class="inline-block font-semibold text-blue-800 text-[11px]">Menunggu</span>
                                <p class="text-[11px] text-gray-500 leading-snug">Pesan sedang diproses oleh sistem WhatsApp.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <div class="w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center shrink-0 mt-0.5">
                                <x-heroicon-o-x-mark class="w-3.5 h-3.5 stroke-[2.5]" />
                            </div>
                            <div>
                                <span class="inline-block font-semibold text-red-800 text-[11px]">Gagal</span>
                                <p class="text-[11px] text-gray-500 leading-snug">Pengiriman gagal. Lihat detail penyebabnya.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <div class="w-6 h-6 rounded-full bg-slate-500 text-white flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5 stroke-[2.5]" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <line x1="5.6" y1="5.6" x2="18.4" y2="18.4"></line>
                                </svg>
                            </div>
                            <div>
                                <span class="inline-block font-semibold text-slate-800 text-[11px]">Dibatalkan</span>
                                <p class="text-[11px] text-gray-500 leading-snug">Pengiriman dibatalkan oleh pengguna atau sistem.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catatan Sumber Data -->
                <div class="rounded-xl border border-blue-200 bg-blue-50/60 p-3.5 shadow-2xs flex items-start gap-2.5">
                    <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" />
                    <p class="text-[11px] text-blue-900 leading-relaxed">
                        Data yang ditampilkan diambil dari aktivitas pengiriman WhatsApp yang sebenarnya, baik dari pengiriman perorangan maupun massal.
                    </p>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-5 py-3 border-t border-gray-200 bg-gray-50/80 flex justify-end">
            <button
                type="button"
                onclick="closeWhatsAppDetailModal()"
                class="px-4 py-2 bg-white hover:bg-gray-100 border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentDetailPesanText = '';

function openWhatsAppDetailModal(logId) {
    const modal = document.getElementById('modalDetailWhatsApp');
    if (!modal) return;

    // Reset isi modal sementara
    document.getElementById('detailMemberNama').textContent = 'Memuat...';
    document.getElementById('detailMemberTelepon').textContent = '-';
    document.getElementById('detailWaktuKirim').textContent = '-';
    document.getElementById('detailPengirimNama').textContent = '-';
    document.getElementById('detailTemplateNama').textContent = '-';
    document.getElementById('detailIsiPesanBox').textContent = 'Memuat isi pesan...';
    document.getElementById('detailWaktuRespon').textContent = '-';
    document.getElementById('detailKeterangan').textContent = '-';
    document.getElementById('detailInfoKategori').textContent = '-';
    document.getElementById('detailInfoTemplate').textContent = '-';
    document.getElementById('detailFakturInfo').textContent = '-';
    document.getElementById('detailTotalTagihan').textContent = '-';
    document.getElementById('detailSenderNama').textContent = '-';
    document.getElementById('detailSenderRole').textContent = '-';

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

        // Badge Status Header & Info
        const statusBadgeHtml = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold ${data.status_badge_class || 'bg-gray-100 text-gray-700'}">${data.status_label || data.status}</span>`;
        document.getElementById('detailHeaderStatusBadge').innerHTML = statusBadgeHtml;
        document.getElementById('detailInfoStatusBadge').innerHTML = statusBadgeHtml;

        document.getElementById('detailWaktuRespon').textContent = data.waktu_respon || '-';
        document.getElementById('detailKeterangan').textContent = data.keterangan || '-';

        document.getElementById('detailInfoKategori').textContent = data.kategori_label || '-';
        document.getElementById('detailInfoTemplate').textContent = data.nama_template || '-';
        document.getElementById('detailFakturInfo').textContent = data.faktur_info || '-';
        document.getElementById('detailTotalTagihan').textContent = data.total_tagihan_formatted || '-';

        document.getElementById('detailSenderNama').textContent = data.pengirim_nama || '-';
        document.getElementById('detailSenderRole').textContent = data.pengirim_role || '-';
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

function changePerPage(val) {
    const input = document.getElementById('filterPerPageInput');
    if (input) {
        input.value = val;
    }
    const form = document.getElementById('filterWhatsAppForm');
    if (form) {
        form.submit();
    }
}

// Tutup modal jika tombol ESC ditekan
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeWhatsAppDetailModal();
    }
});
</script>
@endpush
@endsection

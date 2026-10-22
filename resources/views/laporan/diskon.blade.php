@extends('layouts.app')
@section('title', 'Laporan Audit Diskon Dan Promo')

@section('content')

@php
    $apotek = \Illuminate\Support\Facades\Cache::remember(
        'info_apotek',
        now()->addHours(6),
        fn () => \App\Models\InfoApotek::first()
    );
@endphp

{{-- ========================================================= --}}
{{-- TEMPLATE CETAK (KOP & JUDUL LAPORAN) --}}
{{-- ========================================================= --}}
<div class="print-only">

    {{-- KOP APOTEK --}}
    <div class="print-kop">

        {{-- LOGO --}}
        @if($apotek?->logo)
            <img
                src="{{ asset('storage/' . $apotek->logo) }}"
                alt="Logo {{ $apotek->nama_apotek }}"
                class="print-kop-logo"
            >
        @endif

        {{-- INFORMASI APOTEK --}}
        <div class="print-kop-info">

            <h1>
                {{ $apotek?->nama_apotek ?? 'POS Apotek' }}
            </h1>

            @if($apotek?->alamat)
                <p>
                    {{ $apotek->alamat }}
                </p>
            @endif

            @if($apotek?->telepon || $apotek?->email)
                <p>
                    @if($apotek?->telepon)
                        Telp: {{ $apotek->telepon }}
                    @endif

                    @if($apotek?->telepon && $apotek?->email)
                        &nbsp; | &nbsp;
                    @endif

                    @if($apotek?->email)
                        Email: {{ $apotek->email }}
                    @endif
                </p>
            @endif

            @if($apotek?->no_izin_sia || $apotek?->no_sipa)
                <p>
                    @if($apotek?->no_izin_sia)
                        SIA: {{ $apotek->no_izin_sia }}
                    @endif

                    @if($apotek?->no_izin_sia && $apotek?->no_sipa)
                        &nbsp;&nbsp;•&nbsp;&nbsp;
                    @endif

                    @if($apotek?->no_sipa)
                        SIPA: {{ $apotek->no_sipa }}
                    @endif
                </p>
            @endif

        </div>

    </div>

    {{-- JUDUL LAPORAN --}}
    <div class="print-report-title">
        <h2>
            LAPORAN PENGGUNAAN DISKON DAN PROMO
        </h2>
        <p>
            Periode: {{ $dari ? \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') : 'Semua Periode' }} {{ $sampai ? 's/d ' . \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') : '' }}
            @if(request('jenis'))
                &nbsp;•&nbsp; Jenis Diskon: {{ request('jenis') === 'member' ? 'Diskon Member' : 'Custom Promo' }}
            @endif
            @if(request('promo_id') && ($promoAktif = $promos->firstWhere('id', request('promo_id'))))
                &nbsp;•&nbsp; Promo: {{ $promoAktif->nama }}
            @endif
            @if(request('status_pelanggan'))
                &nbsp;•&nbsp; Status:
                {{
                    [
                        'pelanggan_tetap' => 'Member Pelanggan Tetap',
                        'keluarga_nakes' => 'Member Keluarga Nakes',
                        'member_only' => 'Member Only',
                        'umum' => 'Umum',
                    ][request('status_pelanggan')] ?? 'Semua Pelanggan'
                }}
            @endif
            &nbsp;•&nbsp; Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
        </p>
    </div>

</div>

<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 print:hidden">
    <div>
        <h1>Laporan Penggunaan Diskon dan Promo</h1>
        <p class="text-caption mt-1">Pantau riwayat audit dan diskon rupiah dari promo/membership.</p>
    </div>
    <div class="flex gap-2 shrink-0">
        <a href="{{ route('laporan.diskon', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn-secondary py-2 px-4 flex items-center justify-center gap-1.5">
            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
            Ekspor Excel
        </a>
        <button onclick="printReport()" class="btn-primary py-2 px-4">
            Cetak Laporan
        </button>
    </div>
</div>

<!-- Filter & Search Card -->
<x-card-filter
    :action="route('laporan.diskon')"
    :reset-url="route('laporan.diskon')"
    :grid="true"
    grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5"
    :has-reset="request()->anyFilled(['dari', 'sampai', 'jenis', 'promo_id', 'status_pelanggan'])">
    <div class="flex items-center gap-1.5">
        <input
            type="date"
            name="dari"
            value="{{ $dari }}"
            class="form-input min-w-0"
            aria-label="Tanggal Awal"
            title="Tanggal Awal"
        >
        <span class="text-sm text-gray-400">-</span>
        <input
            type="date"
            name="sampai"
            value="{{ $sampai }}"
            class="form-input min-w-0"
            aria-label="Tanggal Akhir"
            title="Tanggal Akhir"
        >
    </div>

    <select name="jenis" class="form-input" aria-label="Jenis Diskon" title="Jenis Diskon">
        <option value="">Jenis: Semua</option>
        <option value="member" @selected(request('jenis') === 'member')>Diskon Member</option>
        <option value="custom" @selected(request('jenis') === 'custom')>Custom Promo</option>
    </select>

    <select name="promo_id" class="form-input" aria-label="Promo Custom" title="Promo Custom">
        <option value="">Promo: Semua</option>
        @foreach ($promos as $promo)
            <option value="{{ $promo->id }}" @selected(request('promo_id') == $promo->id)>
                {{ $promo->nama }}
            </option>
        @endforeach
    </select>

    <select name="status_pelanggan" class="form-input" aria-label="Status Pelanggan" title="Status Pelanggan">
        <option value="">Pelanggan: Semua</option>
        <option value="pelanggan_tetap" @selected(request('status_pelanggan') === 'pelanggan_tetap')>Member Pelanggan Tetap</option>
        <option value="keluarga_nakes" @selected(request('status_pelanggan') === 'keluarga_nakes')>Member Keluarga Nakes</option>
        <option value="member_only" @selected(request('status_pelanggan') === 'member_only')>Member Only</option>
        <option value="umum" @selected(request('status_pelanggan') === 'umum')>Umum</option>
    </select>
</x-card-filter>

<!-- Table Card -->
<div class="table-custom-container print-table-wrapper">
    <div class="overflow-x-auto print-table-wrapper">
        <table class="table-custom table-diskon print-table w-full min-w-[55rem]">
            <thead class="table-custom-header">
                <tr>
                    <th class="text-center !px-3.5 py-3.5 text-[10px] !font-bold uppercase tracking-wider whitespace-nowrap">No. Invoice</th>
                    <th class="text-center !px-3.5 py-3.5 text-[10px] !font-bold uppercase tracking-wider whitespace-nowrap">Tanggal</th>
                    <th class="text-center !px-3.5 py-3.5 text-[10px] !font-bold uppercase tracking-wider whitespace-nowrap">Barang / Obat</th>
                    <th class="text-center !px-3.5 py-3.5 text-[10px] !font-bold uppercase tracking-wider whitespace-nowrap">Pelanggan</th>
                    <th class="text-center !px-3.5 py-3.5 text-[10px] !font-bold uppercase tracking-wider whitespace-nowrap">Jenis Diskon</th>
                    <th class="text-center !px-3.5 py-3.5 text-[10px] !font-bold uppercase tracking-wider whitespace-nowrap">Nama Promo</th>
                    <th class="text-center !px-3.5 py-3.5 text-[10px] !font-bold uppercase tracking-wider whitespace-nowrap">Persentase Diskon</th>
                    <th class="text-center !px-3.5 py-3.5 text-[10px] !font-bold uppercase tracking-wider whitespace-nowrap">Diskon Rupiah</th>
                </tr>
            </thead>
            <tbody class="table-custom-body">
                @forelse ($usages as $index => $usage)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="col-diskon-faktur text-center font-semibold text-gray-800 font-mono text-xs !px-2.5 py-3 break-words leading-snug">
                            <a href="{{ route('penjualan.show', ['penjualan' => $usage->penjualan_id, 'from' => 'laporan_diskon']) }}" class="text-blue-600 hover:underline print:text-gray-800">
                                {{ $usage->penjualan->no_faktur ?? 'Faktur Terhapus' }}
                            </a>
                        </td>
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">{{ $usage->created_at->format('d M Y H:i') }}</td>
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">{{ $usage->barang_nama }}</td>
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">
                            @php
                                $pelanggan = $usage->penjualan?->pelanggan;
                                $statusMember = trim((string) ($pelanggan?->status_member ?? ''));

                                $memberVariant = match ($statusMember) {
                                    'Member Pelanggan Tetap' => 'success',
                                    'Member Keluarga Nakes' => 'info',
                                    'Member Only' => 'warning',
                                    default => null,
                                };
                            @endphp

                            <div class="flex flex-col items-center justify-center gap-1 min-w-0"> 
                                <span class="font-semibold text-gray-800 text-xs text-center">
                                    {{ $pelanggan?->nama ?? 'Umum' }}
                                </span>

                                @if ($memberVariant)
                                    <span class="badge-{{ $memberVariant }} self-center py-0.5 px-1.5 text-[9px]">
                                        {{ $statusMember }}
                                    </span>
                                @else
                                    <span class="self-center text-[9px]">
                                        {{ (!$pelanggan || !$pelanggan->is_member) ? 'Umum' : ($statusMember ?: 'Member') }}
                                    </span>
                                @endif

                            </div>
                        </td>
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">
                            @if ($usage->jenis === 'member')
                                <span class="badge-info">Member</span>
                            @else
                                <span class="badge-neutral">Custom Promo</span>
                            @endif
                        </td>
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">{{ $usage->custom_discount_nama ?? '-' }}</td>
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">{{ $usage->persentase }}%</td>
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">Rp {{ number_format($usage->nominal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">
                                    @if(request()->anyFilled(['dari', 'sampai', 'jenis', 'promo_id', 'status_pelanggan']))
                                        Riwayat Diskon Tidak Ditemukan
                                    @else
                                        Riwayat Diskon Kosong
                                    @endif
                                </div>
                                <div class="empty-state-desc font-sans">
                                    @if(request()->anyFilled(['dari', 'sampai', 'jenis', 'promo_id', 'status_pelanggan']))
                                        Tidak ada data riwayat audit diskon yang cocok dengan filter kriteria Anda.
                                    @else
                                        Tidak ada data riwayat penggunaan diskon pada periode ini.
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50/50 border-t border-[#D1D5DB] font-bold text-xs">
                <tr>
                    <td colspan="7" class="!px-3 py-3.5 text-right uppercase tracking-wider text-gray-600">Total Diskon:</td>
                    <td class="col-diskon-hemat table-num !px-2.5 py-3.5 text-left text-emerald-700 font-mono text-sm font-bold whitespace-nowrap">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- ========================================================= --}}
{{-- TANDA TANGAN (HANYA MUNCUL SAAT CETAK) --}}
{{-- ========================================================= --}}
<div class="no-break print-signature">
    <div class="print-signature-grid">
        {{-- KIRI: PENANGGUNG JAWAB APOTEK --}}
        <div class="print-signature-box">
            <p class="print-signature-title">Mengetahui,</p>
            <p class="print-signature-role">Penanggung Jawab Apotek</p>

            <div class="print-signature-space"></div>

            <p class="print-signature-name">
                @if (!empty($apotek?->nama_apoteker_pj))
                    {{ $apotek->nama_apoteker_pj }}
                @else
                    (____________________)
                @endif
            </p>

            @if (!empty($apotek?->no_sipa))
                <p class="print-signature-sipa">
                    SIPA: {{ $apotek->no_sipa }}
                </p>
            @endif
        </div>

        {{-- KANAN: PEMBUAT LAPORAN --}}
        <div class="print-signature-box">
            <p class="print-signature-title">Dibuat oleh,</p>
            <p class="print-signature-role">Pembuat Laporan</p>

            <div class="print-signature-space"></div>

            <p class="print-signature-name">
                @if (!empty(auth()->user()?->name))
                    {{ auth()->user()->name }}
                @else
                    (____________________)
                @endif
            </p>
        </div>
    </div>
</div>

<script>
    function printReport() {
        document.querySelectorAll('.overflow-y-auto, .overflow-x-auto, .overflow-hidden, html, body').forEach(function (el) {
            el.scrollTop = 0;
            el.scrollLeft = 0;
        });
        window.scrollTo(0, 0);
        setTimeout(function () {
            window.print();
        }, 50);
    }

    window.addEventListener('beforeprint', function () {
        document.querySelectorAll('.overflow-y-auto, .overflow-x-auto, .overflow-hidden, html, body').forEach(function (el) {
            el.scrollTop = 0;
            el.scrollLeft = 0;
        });
        window.scrollTo(0, 0);
    });
</script>
@endsection

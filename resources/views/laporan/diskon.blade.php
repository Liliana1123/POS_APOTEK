@extends('layouts.app')
@section('title', 'Laporan Audit Diskon')

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
                &nbsp;•&nbsp; Status: {{ request('status_pelanggan') === 'member' ? 'Member' : 'Umum' }}
            @endif
            &nbsp;•&nbsp; Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
        </p>
    </div>

</div>

<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 print:hidden">
    <div>
        <h1>Laporan Penggunaan Diskon dan Promo</h1>
        <p class="text-caption mt-1">Pantau riwayat audit dan hemat rupiah dari promo/membership.</p>
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
<div class="card-base p-4 mb-6 print:hidden">
    <form method="GET" action="{{ route('laporan.diskon') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    TANGGAL AWAL
                </label>
                <input type="date" name="dari" value="{{ $dari }}" class="form-input">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    TANGGAL AKHIR
                </label>
                <input type="date" name="sampai" value="{{ $sampai }}" class="form-input">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Jenis Diskon
                </label>
                <select name="jenis" class="form-input">
                    <option value="">Semua Jenis</option>
                    <option value="member" @selected(request('jenis') === 'member')>Diskon Member</option>
                    <option value="custom" @selected(request('jenis') === 'custom')>Custom Promo</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Promo Custom
                </label>
                <select name="promo_id" class="form-input">
                    <option value="">Semua Promo</option>
                    @foreach ($promos as $promo)
                        <option value="{{ $promo->id }}" @selected(request('promo_id') == $promo->id)>
                            {{ $promo->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Status Pelanggan
                </label>
                <select name="status_pelanggan" class="form-input">
                    <option value="">Semua Pelanggan</option>
                    <option value="member" @selected(request('status_pelanggan') === 'member')>Member</option>
                    <option value="umum" @selected(request('status_pelanggan') === 'umum')>Umum</option>
                </select>
            </div>
        </div>
        
        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            @if(request()->anyFilled(['dari', 'sampai', 'jenis', 'promo_id', 'status_pelanggan']))
                <a href="{{ route('laporan.diskon') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                    Clear
                </a>
            @endif
            <button type="submit" class="btn-primary flex items-center gap-2">
                <x-heroicon-o-funnel class="w-4 h-4" />
                <span>Filter</span>
            </button>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="table-custom-container print-table-wrapper">
    <div class="overflow-x-auto print-table-wrapper">
        <table class="table-custom table-diskon print-table w-full min-w-[55rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="w-[11%] min-w-[6.5rem] col-diskon-tanggal text-center !px-2 py-3 whitespace-nowrap">Tanggal</th>
                    <th scope="col" class="w-[11%] min-w-[6.5rem] col-diskon-faktur !px-2.5 py-3 break-words">No. Faktur</th>
                    <th scope="col" class="w-[20%] min-w-[10rem] col-diskon-barang !px-3 py-3 break-words">Barang / Obat</th>
                    <th scope="col" class="w-[10%] min-w-[5.5rem] col-diskon-pelanggan !px-2.5 py-3 break-words">Pelanggan</th>
                    <th scope="col" class="w-[12%] min-w-[6.5rem] col-diskon-jenis text-center !px-2 py-3 break-words">Jenis Diskon</th>
                    <th scope="col" class="w-[18%] min-w-[8.5rem] col-diskon-nama !px-2.5 py-3 break-words">Nama Promo</th>
                    <th scope="col" class="w-[6%] min-w-[3.5rem] col-diskon-persentase text-center !px-1.5 py-3 whitespace-nowrap">Persentase</th>
                    <th scope="col" class="w-[12%] min-w-[6.5rem] col-diskon-hemat text-right !px-2.5 py-3 whitespace-nowrap">Hemat Rupiah</th>
                </tr>
            </thead>
            <tbody class="table-custom-body">
                @forelse ($usages as $index => $usage)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="col-diskon-tanggal text-center font-mono text-gray-600 text-xs !px-2 py-3 whitespace-nowrap">{{ $usage->created_at->format('d M Y H:i') }}</td>
                        <td class="col-diskon-faktur font-semibold text-gray-800 font-mono text-xs !px-2.5 py-3 break-words leading-snug">
                            <a href="{{ route('penjualan.show', $usage->penjualan_id) }}" class="text-blue-600 hover:underline print:text-gray-800">
                                {{ $usage->penjualan->no_faktur ?? 'Faktur Terhapus' }}
                            </a>
                        </td>
                        <td class="col-diskon-barang font-medium text-gray-800 text-xs !px-3 py-3 break-words leading-snug">{{ $usage->barang_nama }}</td>
                        <td class="col-diskon-pelanggan text-gray-700 font-medium text-xs !px-2.5 py-3 break-words leading-snug">
                            {{ $usage->penjualan->pelanggan->nama ?? 'Umum' }}
                            @if (isset($usage->penjualan->pelanggan) && $usage->penjualan->pelanggan->is_member)
                                <span class="badge-success ml-1.5 py-0.5 px-1 text-[8px] print:hidden">Member</span>
                            @endif
                        </td>
                        <td class="col-diskon-jenis text-center text-xs !px-1.5 py-3 break-words">
                            @if ($usage->jenis === 'member')
                                <span class="badge-info">Member</span>
                            @else
                                <span class="badge-neutral">Custom Promo</span>
                            @endif
                        </td>
                        <td class="col-diskon-nama text-gray-600 text-xs !px-2.5 py-3 break-words leading-snug">{{ $usage->custom_discount_nama ?? '-' }}</td>
                        <td class="col-diskon-persentase text-center font-bold text-gray-800 text-xs !px-1.5 py-3 whitespace-nowrap">{{ $usage->persentase }}%</td>
                        <td class="col-diskon-hemat table-num font-bold text-gray-800 font-mono text-right text-xs !px-2.5 py-3 whitespace-nowrap">Rp {{ number_format($usage->nominal, 0, ',', '.') }}</td>
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
                    <td colspan="7" class="!px-3 py-3.5 text-right uppercase tracking-wider text-gray-600">Total Hemat:</td>
                    <td class="col-diskon-hemat table-num !px-2.5 py-3.5 text-right text-emerald-700 font-mono text-sm font-bold whitespace-nowrap">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
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

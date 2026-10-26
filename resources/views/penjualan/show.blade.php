@extends('layouts.app')
@section('title', 'Struk Penjualan')

@section('content')
@php
    $backUrl = route('penjualan.create');
    if (request('from') === 'laporan_penjualan') {
        $backUrl = route('laporan.penjualan', request()->except('from'));
    } elseif (request('from') === 'laporan_diskon') {
        $backUrl = route('laporan.diskon');
    } elseif (request('from') === 'penjualan_index' || request('from') === 'riwayat' || request('from') === 'index') {
        $backUrl = route('penjualan.index');
    } elseif (request('from') === 'pelanggan' && isset($penjualan->pelanggan_id)) {
        $backUrl = route('pelanggan.show', $penjualan->pelanggan_id);
    }
@endphp

<x-page-header
    title="Struk Penjualan"
    subtitle="Cetak bukti pembayaran transaksi kasir: {{ $penjualan->no_faktur }}"
    :back-url="$backUrl"
    class="print:hidden">
    <select id="select-ukuran-kertas" onchange="changePaperSize(this.value)" class="text-xs border border-gray-300 rounded-lg px-2.5 py-2 bg-white text-gray-700 font-medium focus:ring-1 focus:ring-blue-500 focus:border-blue-500 cursor-pointer shadow-xs">
        <option value="58mm">Thermal 58 mm (EPPOS)</option>
        <option value="80mm">Thermal 80 mm (EPPOS)</option>
    </select>
    <button type="button" onclick="window.print()" class="btn-primary flex items-center gap-1.5 py-2 px-4">
        <x-heroicon-o-printer class="w-4 h-4" />
        <span>Print Struk</span>
    </button>
</x-page-header>

@php
    $apotek = \Illuminate\Support\Facades\Cache::remember(
        'info_apotek',
        now()->addHours(6),
        fn () => \App\Models\InfoApotek::first()
    );
@endphp

<div class="card-base print-struk max-w-sm mx-auto text-xs font-mono bg-white border border-gray-200" id="struk">
    <div class="text-center mb-3 pb-2.5 border-b border-dashed border-gray-300">
        @if($apotek?->logo)
            <div class="flex justify-center mb-1">
                <img src="{{ asset('storage/' . $apotek->logo) }}" alt="Logo" class="w-10 h-10 object-contain" onerror="this.parentElement.style.display='none'">
            </div>
        @endif
        <p class="struk-title font-bold text-sm uppercase tracking-wider text-gray-900">
            {{ $apotek?->nama_apotek ?? 'POS APOTEK' }}
        </p>
        <div class="struk-sub text-[10px] text-gray-500 mt-0.5 leading-tight space-y-0.5">
            @if($apotek?->alamat)
                <p>{{ $apotek->alamat }}</p>
            @endif
            @if($apotek?->telepon)
                <p>Telp: {{ $apotek->telepon }}</p>
            @endif
            @if($apotek?->no_izin_sia || $apotek?->no_sipa)
                <div class="text-[9px] text-gray-400 pt-0.5 leading-tight">
                    @if($apotek?->no_izin_sia)<span>SIA: {{ $apotek->no_izin_sia }}</span>@endif
                    @if($apotek?->no_izin_sia && $apotek?->no_sipa)<span class="mx-1">•</span>@endif
                    @if($apotek?->no_sipa)<span>SIPA: {{ $apotek->no_sipa }}</span>@endif
                </div>
            @endif
            @if($apotek?->nama_apoteker_pj)
                <p class="text-[9px] text-gray-400">PJ: {{ $apotek->nama_apoteker_pj }}</p>
            @endif
        </div>
    </div>

    <div class="struk-meta text-[10px] text-gray-600 mb-3 space-y-1 font-bold">
        <div class="struk-meta-row">
            <span class="struk-meta-label">No. Invoice</span>
            <span class="struk-meta-value font-mono font-bold text-gray-800">{{ $penjualan->no_faktur }}</span>
        </div>
        <div class="struk-meta-row">
            <span class="struk-meta-label">Tanggal</span>
            <span class="struk-meta-value font-mono font-bold text-gray-800">@tanggalWaktuId($penjualan->created_at ?: $penjualan->tanggal)</span>
        </div>
        <div class="struk-meta-row">
            <span class="struk-meta-label">Kasir</span>
            <span class="struk-meta-value font-mono font-bold text-gray-800">{{ $penjualan->user->name }}</span>
        </div>
        <div class="struk-meta-row">
            <span class="struk-meta-label">Pelanggan</span>
            <span class="struk-meta-value font-mono font-bold text-gray-800">
                {{ $penjualan->pelanggan->nama ?? 'Umum' }}
                @if (isset($penjualan->pelanggan) && $penjualan->pelanggan->is_member)
                    <span class="font-mono font-semibold">({{ $penjualan->pelanggan->member_id }})</span>
                @endif
            </span>
        </div>
        <div class="struk-meta-row">
            <span class="struk-meta-label">Metode Pembayaran</span>
            <span class="struk-meta-value font-mono font-bold text-gray-800">{{ $penjualan->metode_pembayaran }}</span>
        </div>
        @if (strtolower($penjualan->metode_pembayaran) === 'piutang' && $penjualan->due_date)
            <div class="struk-meta-row">
                <span class="struk-meta-label">Jatuh Tempo</span>
                <span class="struk-meta-value font-semibold">@tanggalId($penjualan->due_date)</span>
            </div>
        @endif
    </div>

    <div class="border-t border-dashed pt-2.5 space-y-2">
        @foreach ($penjualan->detail as $item)
            @php
                $satuan = $item->detailPenerimaan?->barang?->satuan?->nama;
            @endphp
            <div>
                <div class="struk-item font-semibold text-gray-800">
                    <span class="item-name block leading-tight">{{ $item->detailPenerimaan->barang->nama }}</span>
                    <div class="flex justify-between items-center mt-0.5">
                        <span class="struk-item-sub text-[10px] text-gray-500 font-normal">
                            {{ $item->jumlah }}{{ $satuan ? ' ' . $satuan : '' }} × @rupiah($item->harga_jual)
                            @if ($item->diskon > 0)
                                <span class="text-red-500"> (-{{ number_format($item->diskon, 0, ',', '.') }})</span>
                            @endif
                        </span>
                        <span class="struk-item-price text-right font-semibold">@rupiah($item->subtotal)</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @php
        $discountUsages = \App\Models\DiscountUsage::where('penjualan_id', $penjualan->id)->get();
        $diskonMember = $discountUsages->where('jenis', 'member')->sum('nominal');
        $diskonPromo = $discountUsages->where('jenis', 'custom')->sum('nominal');
        $totalDiskon = $diskonMember + $diskonPromo;
        
        // Fallback for legacy invoices before Fase 3 audit logging
        if ($totalDiskon == 0 && $penjualan->detail->sum('diskon') > 0) {
            $totalDiskon = $penjualan->detail->sum('diskon');
            if ($penjualan->pelanggan && $penjualan->pelanggan->is_member) {
                $diskonMember = $totalDiskon;
            } else {
                $diskonPromo = $totalDiskon;
            }
        }
    @endphp

    @if ($totalDiskon > 0)
        <div class="struk-discount border-t border-dashed mt-3 pt-3 text-[10px] text-gray-600 space-y-1">
            <div class="flex justify-between">
                <span>Subtotal Kotor</span>
                <span>@rupiah($penjualan->total + $totalDiskon)</span>
            </div>
            @if ($diskonMember > 0)
                <div class="flex justify-between text-green-600">
                    <span>Diskon Member</span>
                    <span>-@rupiah($diskonMember)</span>
                </div>
            @endif
            @if ($diskonPromo > 0)
                <div class="flex justify-between text-green-600">
                    <span>Diskon Promo</span>
                    <span>-@rupiah($diskonPromo)</span>
                </div>
            @endif

        </div>
    @endif

    <div class="struk-total border-t border-dashed mt-2.5 pt-2.5 font-bold text-gray-800">
        @if ($totalDiskon > 0)
            <div class="flex justify-between text-green-600 text-[10px] mb-1">
                <span>TOTAL DISKON</span>
                <span>-@rupiah($totalDiskon)</span>
            </div>
        @endif
        <div class="flex justify-between text-sm">
            <span>TOTAL AKHIR</span>
            <span>@rupiah($penjualan->total)</span>
        </div>
    </div>

    <p class="struk-footer text-center text-[16px] text-gray-400 mt-5 uppercase tracking-wider font-semibold">SEMOGA LEKAS SEMBUH</p>
</div>

<style id="base-thermal-print-style">
    /* ── On-screen preview container (non-print) ── */
    #struk {
        max-width: 400px;
    }
    #struk.struk-58mm {
        max-width: 360px;
    }
    #struk.struk-80mm {
        max-width: 440px;
    }

    /* On-screen: meta rows display as label : value pairs */
    .struk-meta-row {
        display: flex;
        justify-content: space-between;
        gap: 4px;
        align-items: baseline;
    }
    .struk-meta-label {
        flex-shrink: 0;
        color: #6b7280;
    }
    .struk-meta-value {
        text-align: right;
        word-break: break-word;
        overflow-wrap: break-word;
    }
    /* On-screen: item price sub-row */
    .struk-item-price {
        white-space: nowrap;
        flex-shrink: 0;
    }

    @media print {
        /*
         * KEY FIX: Use `size: auto` so Chrome does NOT downscale the page
         * content to fit a tiny 58mm canvas. The printer driver already knows
         * the paper roll width. We control the physical receipt width purely
         * via the #struk `width` property (in mm), which the printer respects
         * without browser-level scaling.
         */
        @page {
            size: auto;
            margin: 0;
        }

        /* 1. Global Reset */
        html,
        body {
            width: auto !important;
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
            overflow: visible !important;
            background: #ffffff !important;
            color: #000000 !important;
            margin: 0 !important;
            padding: 0 !important;
            display: block !important;
            position: static !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body > div,
        body > div.flex-1,
        div.flex-1.flex.flex-col,
        main {
            display: block !important;
            position: static !important;
            width: auto !important;
            max-width: none !important;
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
            overflow: visible !important;
            padding: 0 !important;
            margin: 0 !important;
            float: none !important;
            flex: none !important;
        }

        /* 2. Hide all non-printable layout elements */
        aside,
        #app-sidebar,
        #sidebar-backdrop,
        nav,
        header,
        footer,
        .app-global-footer,
        [role="navigation"],
        .print\:hidden,
        .no-print,
        #mobile-sidebar-toggle,
        form,
        button,
        .btn-primary,
        .btn-secondary,
        a,
        select,
        input,
        #toast-success,
        #toast-error,
        [role="status"],
        [role="alert"] {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            width: 0 !important;
            min-height: 0 !important;
            max-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            overflow: hidden !important;
            position: absolute !important;
            top: -9999px !important;
            left: -9999px !important;
        }

        /* 3. Base Struk Container */
        #struk,
        .print-struk {
            display: block !important;
            visibility: visible !important;
            position: static !important;
            top: auto !important;
            left: auto !important;
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
            overflow: visible !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            background: #ffffff !important;
            font-family: 'Courier New', Courier, monospace !important;
            color: #000000 !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            box-sizing: border-box !important;
            /* No transform, no zoom, no scale */
            transform: none !important;
            zoom: normal !important;
        }

        /* ── 58 mm Thermal (default / EPPOS EPS58M) ── */
        #struk,
        .print-struk,
        #struk.struk-58mm,
        #struk:not(.struk-80mm) {
            width: 58mm !important;
            max-width: 58mm !important;
            margin: 0 !important;
            padding: 2mm 2mm !important;
            font-size: 8pt !important;
            line-height: 1.35 !important;
        }

        #struk.struk-58mm .struk-title,
        #struk:not(.struk-80mm) .struk-title {
            font-size: 9.5pt !important;
            font-weight: bold !important;
            letter-spacing: 0.3px !important;
        }

        #struk.struk-58mm .struk-sub,
        #struk:not(.struk-80mm) .struk-sub {
            font-size: 6.5pt !important;
            line-height: 1.3 !important;
        }

        #struk.struk-58mm .struk-meta,
        #struk:not(.struk-80mm) .struk-meta {
            font-size: 7pt !important;
            line-height: 1.35 !important;
        }

        #struk.struk-58mm .struk-item,
        #struk:not(.struk-80mm) .struk-item {
            font-size: 7.5pt !important;
            line-height: 1.3 !important;
        }

        #struk.struk-58mm .struk-item-sub,
        #struk:not(.struk-80mm) .struk-item-sub {
            font-size: 6.5pt !important;
        }

        #struk.struk-58mm .struk-discount,
        #struk:not(.struk-80mm) .struk-discount {
            font-size: 6.5pt !important;
            line-height: 1.3 !important;
        }

        #struk.struk-58mm .struk-total,
        #struk:not(.struk-80mm) .struk-total {
            font-size: 9pt !important;
            font-weight: bold !important;
        }

        #struk.struk-58mm .struk-footer,
        #struk:not(.struk-80mm) .struk-footer {
            font-size: 6pt !important;
        }

        /* ── 80 mm Thermal (EPPOS EP-80) ── */
        #struk.struk-80mm {
            width: 80mm !important;
            max-width: 80mm !important;
            margin: 0 !important;
            padding: 2mm 3mm !important;
            font-size: 9pt !important;
            line-height: 1.35 !important;
        }

        #struk.struk-80mm .struk-title {
            font-size: 11pt !important;
            font-weight: bold !important;
            letter-spacing: 0.5px !important;
        }

        #struk.struk-80mm .struk-sub {
            font-size: 7.5pt !important;
            line-height: 1.3 !important;
        }

        #struk.struk-80mm .struk-meta {
            font-size: 8pt !important;
            line-height: 1.35 !important;
        }

        #struk.struk-80mm .struk-item {
            font-size: 8.5pt !important;
            line-height: 1.35 !important;
        }

        #struk.struk-80mm .struk-item-sub {
            font-size: 7.5pt !important;
        }

        #struk.struk-80mm .struk-discount {
            font-size: 7.5pt !important;
            line-height: 1.35 !important;
        }

        #struk.struk-80mm .struk-total {
            font-size: 10.5pt !important;
            font-weight: bold !important;
        }

        #struk.struk-80mm .struk-footer {
            font-size: 7pt !important;
        }

        /* Make all #struk descendants visible & clean */
        #struk * {
            visibility: visible !important;
            color: #000000 !important;
            background-color: transparent !important;
            box-shadow: none !important;
            text-shadow: none !important;
        }

        #struk img {
            display: block !important;
            margin: 0 auto 3px auto !important;
            max-width: 35px !important;
            height: auto !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .border-dashed {
            border-top: 1px dashed #000000 !important;
        }

        /* ── Prevent content clipping: word-break on all text ── */
        #struk,
        #struk * {
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            white-space: normal !important;
            overflow: visible !important;
        }

        /* ── Meta rows: label left, value right, no overflow ── */
        #struk .struk-meta-row {
            display: flex !important;
            justify-content: space-between !important;
            align-items: baseline !important;
            gap: 3mm !important;
            width: 100% !important;
        }
        #struk .struk-meta-label {
            flex-shrink: 0 !important;
            white-space: nowrap !important;
        }
        #struk .struk-meta-value {
            text-align: right !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            white-space: normal !important;
        }

        /* ── Item rows: name on top, qty+price below ── */
        #struk .struk-item {
            display: block !important;
        }
        #struk .item-name {
            display: block !important;
            width: 100% !important;
        }
        #struk .struk-item-price {
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            text-align: right !important;
        }
        .struk-meta-label {
            flex-shrink: 0;
            color: #6b7280;
        }

        .struk-meta-value {
            text-align: right;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        /* ── Total row ── */
        #struk .struk-total > div {
            display: flex !important;
            justify-content: space-between !important;
            width: 100% !important;
        }
        
    }
</style>

<script>
    function changePaperSize(size) {
        const struk = document.getElementById('struk');
        const select = document.getElementById('select-ukuran-kertas');
        const validSize = size === '80mm' ? '80mm' : '58mm';

        if (select && select.value !== validSize) {
            select.value = validSize;
        }

        if (struk) {
            struk.classList.remove('struk-58mm', 'struk-80mm');
            struk.classList.add('struk-' + validSize);
        }

        // NOTE: We do NOT inject a dynamic @page { size: Xmm } here.
        // Setting @page size to a tiny mm value causes Chrome to downscale
        // the entire page content to fit that canvas, making the receipt tiny.
        // Instead, @page { size: auto } is fixed in CSS and the physical width
        // is controlled solely by the #struk container width (58mm or 80mm).
        try {
            localStorage.setItem('pos_receipt_paper_size', validSize);
        } catch (e) {}
    }

    document.addEventListener('DOMContentLoaded', function () {
        let savedSize = '58mm';
        try {
            savedSize = localStorage.getItem('pos_receipt_paper_size') || '58mm';
        } catch (e) {}

        changePaperSize(savedSize);
    });

    window.addEventListener('beforeprint', function () {
        let size = '58mm';
        const select = document.getElementById('select-ukuran-kertas');
        if (select) {
            size = select.value;
        } else {
            try {
                size = localStorage.getItem('pos_receipt_paper_size') || '58mm';
            } catch (e) {}
        }
        changePaperSize(size);
    });
</script>
@endsection

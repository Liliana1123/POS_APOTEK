@extends('layouts.app')
@section('title', 'Struk Penjualan')

@section('content')
<div class="flex justify-between items-center mb-6 print:hidden">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Struk Penjualan</h1>
        <p class="text-xs text-gray-500 mt-0.5">Cetak bukti pembayaran transaksi kasir: <span class="font-mono font-bold">{{ $penjualan->no_faktur }}</span></p>
    </div>
    <div class="flex gap-2">
        <button onclick="window.print()" class="btn-primary">Print Struk</button>
        <a href="{{ route('penjualan.index') }}" class="btn-secondary">Kembali</a>
    </div>
</div>

<div class="card-base max-w-sm mx-auto text-xs font-mono bg-white border border-gray-200" id="struk">
    <div class="text-center mb-4">
        <p class="font-bold text-sm uppercase tracking-wider text-gray-800">POS APOTEK</p>
        <p class="text-[9px] text-gray-500 mt-0.5 uppercase tracking-widest font-semibold">Struk Pembelian Obat</p>
    </div>

    <div class="text-[10px] text-gray-600 mb-3 space-y-1">
        <div class="flex justify-between"><span>No. Faktur</span><span class="font-mono font-semibold text-gray-800">{{ $penjualan->no_faktur }}</span></div>
        <div class="flex justify-between"><span>Tanggal</span><span>{{ $penjualan->tanggal->format('d M Y H:i') }}</span></div>
        <div class="flex justify-between"><span>Kasir</span><span>{{ $penjualan->user->name }}</span></div>
        <div class="flex justify-between">
            <span>Pelanggan</span>
            <span>
                {{ $penjualan->pelanggan->nama ?? 'Umum' }}
                @if (isset($penjualan->pelanggan) && $penjualan->pelanggan->is_member)
                    <span class="font-mono text-blue-700 font-semibold">({{ $penjualan->pelanggan->member_id }})</span>
                @endif
            </span>
        </div>
    </div>

    <div class="border-t border-dashed pt-2.5 space-y-2">
        @foreach ($penjualan->detail as $item)
            <div>
                <div class="flex justify-between font-semibold text-gray-800">
                    <span>{{ $item->detailPenerimaan->barang->nama }}</span>
                    <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="text-[10px] text-gray-500 mt-0.5">
                    {{ $item->jumlah }} x Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                    @if ($item->diskon > 0)
                        <span class="text-red-500 font-medium ml-1">(Diskon -Rp {{ number_format($item->diskon, 0, ',', '.') }})</span>
                    @endif
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
        <div class="border-t border-dashed mt-3 pt-3 text-[10px] text-gray-600 space-y-1">
            <div class="flex justify-between">
                <span>Subtotal Kotor</span>
                <span>Rp {{ number_format($penjualan->total + $totalDiskon, 0, ',', '.') }}</span>
            </div>
            @if ($diskonMember > 0)
                <div class="flex justify-between text-green-600">
                    <span>Diskon Member</span>
                    <span>-Rp {{ number_format($diskonMember, 0, ',', '.') }}</span>
                </div>
            @endif
            @if ($diskonPromo > 0)
                <div class="flex justify-between text-green-600">
                    <span>Diskon Promo</span>
                    <span>-Rp {{ number_format($diskonPromo, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between font-bold text-gray-700">
                <span>Total Diskon</span>
                <span>-Rp {{ number_format($totalDiskon, 0, ',', '.') }}</span>
            </div>
        </div>
    @endif

    <div class="border-t border-dashed mt-2.5 pt-2.5 flex justify-between font-bold text-sm text-gray-800">
        <span>TOTAL AKHIR</span>
        <span>Rp {{ number_format($penjualan->total, 0, ',', '.') }}</span>
    </div>

    <p class="text-center text-[10px] text-gray-400 mt-5 uppercase tracking-wider font-semibold">Terima kasih atas kunjungan Anda</p>
</div>

<style>
    @media print {
        @page {
            margin: 0;
        }
        /* Hide layout structural elements */
        aside, nav, .print\:hidden, #mobile-sidebar-toggle, form, button, a {
            display: none !important;
        }
        body, html {
            background-color: white !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        /* Optimize wrapper for thermal receipt sizes (fit-content supports 58mm/80mm rolls automatically) */
        #struk {
            width: 72mm !important;
            max-width: 100% !important;
            margin: 0 auto !important;
            padding: 4mm !important;
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            font-family: 'Courier New', Courier, monospace !important;
            color: black !important;
            font-size: 11px !important;
            line-height: 1.3 !important;
        }
        #struk * {
            color: black !important;
            background-color: transparent !important;
        }
        .border-dashed {
            border-top: 1px dashed black !important;
        }
    }
</style>
@endsection

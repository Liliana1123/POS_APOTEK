@php
    $apotek = \Illuminate\Support\Facades\Cache::remember('info_apotek', now()->addHours(6), fn () => \App\Models\InfoApotek::first());
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Bukti Penerimaan - {{ $penerimaan->no_faktur }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #222;
            background: #fff;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 25px;
        }

        .kop-surat {
            position: relative;
            min-height: 90px;
            padding-bottom: 12px;
            border-bottom: 2px solid #222;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .kop-logo {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 75px;
            height: 75px;
            object-fit: contain;
            object-position: center;
        }

        .kop-info {
            width: 100%;
            text-align: center;
        }

        .kop-info h2 {
            margin: 0 0 5px;
            font-size: 18px;
            font-weight: bold;
            color: #222;
        }

        .kop-info p {
            margin: 3px 0;
            font-size: 12px;
            color: #555;
        }

        .kop-info h2 {
            margin: 0 0 5px;
            font-size: 22px;
            font-weight: bold;
            color: #222;
        }

        .kop-info p {
            margin: 2px 0;
            font-size: 12px;
            color: #444;
        }

        /* JUDUL DI BAWAH KOP */
        .judul-dokumen {
            text-align: center;
            margin-top: 18px;
        }

        .judul-dokumen h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }
        .info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 30px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            align-items: baseline;
        }

        .info-label {
            width: 135px;
            font-weight: bold;
            white-space: nowrap;
        }

        .info-colon {
            width: 12px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .info-value {
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            box-sizing: border-box;
        }

        th,
        td {
            border: 1px solid #777;
            padding: 7px;
        }

        /* Pastikan border kolom paling kanan / paling belakang dan baris paling akhir tidak hilang */
        table th:last-child,
        table td:last-child {
            border-right: 1px solid #777 !important;
        }

        table tr:last-child td {
            border-bottom: 1px solid #777 !important;
        }

        th {
            background: #f2f2f2;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total-section {
            margin-top: 15px;
            margin-left: auto;
            width: 320px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }

        .total-row.grand-total {
            border-top: 2px solid #222;
            margin-top: 5px;
            padding-top: 8px;
            font-size: 15px;
            font-weight: bold;
        }

        .payment-section {
            margin-top: 25px;
        }

        .payment-section h3 {
            margin-bottom: 8px;
        }

        @page {
            @top-left {
                text-align: left;
            }
            @top-center {
                content: none;
            }
            @top-right {
                text-align: right;
            }
            @bottom-left {
                text-align: left;
            }
            @bottom-center {
                content: none;
            }
            @bottom-right {
                text-align: right;
            }
        }

        .footer-ttd {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            width: 250px;
            text-align: center;
            font-size: 14px;
        }

        .signature-title {
            margin-bottom: 2px;
            font-weight: normal;
        }

        .signature-role {
            font-weight: bold;
        }

        .signature-space {
            height: 75px;
        }

        .signature-name {
            font-weight: bold;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 9px 15px;
            border: 0;
            border-radius: 5px;
            background: #222;
            color: #fff;
            cursor: pointer;
        }

        @media print {
            body {
                padding: 0;
            }

            .container {
                max-width: none;
                width: 100%;
                padding-right: 1px;
            }

            .print-button {
                display: none;
            }

            .no-break {
                -webkit-column-break-inside: avoid;
                page-break-inside: avoid;
                break-inside: avoid;
            }

            table th,
            table td {
                border: 1px solid #777 !important;
            }

            table th:last-child,
            table td:last-child {
                border-right: 1px solid #777 !important;
            }

            table tr:last-child td {
                border-bottom: 1px solid #777 !important;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <button type="button" class="print-button" onclick="window.print()">
        Print
    </button>

    <div class="header">

        {{-- KOP SURAT --}}
        <div class="kop-surat">

            {{-- LOGO APOTEK --}}
            @if($apotek?->logo)
                <img
                    src="{{ asset('storage/' . $apotek->logo) }}"
                    alt="Logo {{ $apotek->nama_apotek }}"
                    class="kop-logo"
                >
            @endif

            {{-- INFORMASI APOTEK --}}
            <div class="kop-info">

                <h2>
                    {{ $apotek?->nama_apotek ?? 'POS Apotek' }}
                </h2>

                @if($apotek?->alamat)
                    <p>{{ $apotek->alamat }}</p>
                @endif

                @if($apotek?->telepon || $apotek?->email)
                    <p>
                        @if($apotek?->telepon)
                            Telp. {{ $apotek->telepon }}
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

        {{-- JUDUL DOKUMEN DI BAWAH KOP --}}
        <div class="judul-dokumen">
            <h1>BUKTI PENERIMAAN BARANG</h1>
        </div>

    </div>

    <div class="info">

        <div class="info-item">
            <div class="info-label">No. Faktur</div>
            <div class="info-colon">:</div>
            <div class="info-value">
                {{ $penerimaan->no_faktur }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">Tanggal Terima</div>
            <div class="info-colon">:</div>
            <div class="info-value">
                {{ $penerimaan->tanggal?->format('d/m/Y') ?? '—' }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">Supplier</div>
            <div class="info-colon">:</div>
            <div class="info-value">
                {{ $penerimaan->supplier->nama ?? '—' }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">Telepon Supplier</div>
            <div class="info-colon">:</div>
            <div class="info-value">
                {{ $penerimaan->telepon_supplier ?? '—' }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">Dicatat Oleh</div>
            <div class="info-colon">:</div>
            <div class="info-value">
                {{ $penerimaan->user->name ?? '—' }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">Status Pembayaran</div>
            <div class="info-colon">:</div>
            <div class="info-value">
                {{ $penerimaan->lunas ? 'Lunas' : 'Belum Lunas' }}
            </div>
        </div>

        @if ($penerimaan->jatuh_tempo)
            <div class="info-item">
                <div class="info-label">Jatuh Tempo</div>
                <div class="info-colon">:</div>
                <div class="info-value">
                    {{ $penerimaan->jatuh_tempo->format('d/m/Y') }}
                </div>
            </div>
        @endif

        @if ($penerimaan->keterangan)
            <div class="info-item">
                <div class="info-label">Keterangan</div>
                <div class="info-colon">:</div>
                <div class="info-value">
                    {{ $penerimaan->keterangan }}
                </div>
            </div>
        @endif

    </div>
    
    <h3>Rincian Pesanan Masuk</h3>
    
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Barang</th>
                <th style="width: 80px;">Barcode</th>
                <th style="width: 75px;">No. Batch</th>
                <th style="width: 80px;">Expired</th>
                <th style="width: 50px;">Rak</th>
                <th style="width: 55px;">Satuan</th>
                <th style="width: 40px;">Qty</th>
                <th style="width: 85px;">Harga Beli</th>
                <th style="width: 90px;">Subtotal</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($penerimaan->detail as $index => $item)
                <tr>
                    <td class="text-center">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ $item->barang->nama ?? '—' }}
                    </td>

                    <td class="text-center">
                        {{ $item->barang->barcode ?? '—' }}
                    </td>

                    <td class="text-center">
                        {{ $item->no_batch }}
                    </td>

                    <td class="text-center">
                        {{ $item->expired_date?->format('d/m/Y') ?? '—' }}
                    </td>

                    <td class="text-center">
                        {{ $item->no_rak ?? '—' }}
                    </td>

                    <td class="text-center">
                        {{ $item->barang->satuan->nama ?? '—' }}
                    </td>

                    <td class="text-center">
                        {{ $item->jumlah }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($item->harga_beli * $item->jumlah, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">

        <div class="total-row">
            <span>Total Belanja</span>
            <span>
                Rp {{ number_format($penerimaan->totalFaktur(), 0, ',', '.') }}
            </span>
        </div>

        <div class="total-row">
            <span>PPN (11%)</span>
            <span>
                Rp {{ number_format($penerimaan->ppn, 0, ',', '.') }}
            </span>
        </div>

        <div class="total-row grand-total">
            <span>Total Tagihan</span>
            <span>
                Rp {{ number_format($penerimaan->totalTagihan(), 0, ',', '.') }}
            </span>
        </div>

        <div class="total-row">
            <span>Total Dibayar</span>
            <span>
                Rp {{ number_format($penerimaan->totalDibayar(), 0, ',', '.') }}
            </span>
        </div>

        @if ($penerimaan->sisaTagihan() > 0)
            <div class="total-row" style="color: #dc2626; font-weight: bold;">
                <span>Kekurangan Pembayaran</span>
                <span>
                    Rp {{ number_format($penerimaan->sisaTagihan(), 0, ',', '.') }}
                </span>
            </div>
        @endif

        @if ($penerimaan->kelebihanPembayaran() > 0)
            <div class="total-row" style="color: #dc2626; font-weight: bold;">
                <span>Kelebihan Pembayaran</span>
                <span>
                    Rp {{ number_format($penerimaan->kelebihanPembayaran(), 0, ',', '.') }}
                </span>
            </div>
        @endif

    </div>

    <div class="payment-section">

        <h3>Riwayat Pembayaran</h3>

        @if ($penerimaan->pembayaran->count())

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jumlah</th>
                        <th>Dicatat Oleh</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($penerimaan->pembayaran as $index => $pembayaran)

                        <tr>
                            <td class="text-center">
                                {{ $index + 1 }}
                            </td>

                            <td class="text-center">
                                {{ $pembayaran->tanggal_bayar?->format('d/m/Y') ?? '—' }}
                            </td>

                            <td class="text-right">
                                Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}
                            </td>

                            <td>
                                {{ $pembayaran->user->name ?? '—' }}
                            </td>

                            <td>
                                {{ $pembayaran->keterangan ?? '—' }}
                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>

        @else

            <p>Belum ada pembayaran.</p>

        @endif

        <div class="payment-section">

            <h3>Riwayat Penerimaan</h3>

            @if ($penerimaan->riwayatPenerimaan->count())

            <table>
                <thead>
                    <tr>
                        <th style="width: 35px;">No</th>
                        <th style="width: 85px;">Tanggal</th>
                        <th>Barang</th>
                        <th style="width: 45px;">Qty</th>
                        <th style="width: 110px;">Dicatat Oleh</th>
                        <th style="width: 150px;">Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($penerimaan->riwayatPenerimaan as $index => $riwayat)

                        <tr style="{{ $riwayat->jenis === 'pembatalan' ? 'color: #dc2626; font-weight: bold;' : '' }}">
                            <td class="text-center">
                                {{ $index + 1 }}
                            </td>

                            <td class="text-center">
                                {{ $riwayat->tanggal?->format('d/m/Y') ?? '—' }}
                            </td>

                            <td>
                                {{ $riwayat->detailPesanan->barang->nama ?? $riwayat->detailPenerimaan->barang->nama ?? '—' }}
                            </td>

                            <td class="text-center">
                                {{ number_format($riwayat->jumlah, 0, ',', '.') }}
                            </td>

                            <td>
                                {{ $riwayat->user->name ?? '—' }}
                            </td>

                            <td>
                                {{ $riwayat->keterangan ?? '—' }}
                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>

            @endif

        </div>

    </div>

    <div class="no-break">
        <div class="footer-ttd">

            <div class="signature">
                <div class="signature-title">Mengetahui,</div>
                <div class="signature-role">Supplier</div>

                <div class="signature-space"></div>

                <div class="signature-name">
                    @if (!empty($penerimaan->supplier?->nama))
                        ( {{ $penerimaan->supplier->nama }} )
                    @else
                        (__________________)
                    @endif
                </div>
            </div>

            <div class="signature">
                <div class="signature-title">Diterima oleh,</div>
                <div class="signature-role">Petugas Penerima</div>

                <div class="signature-space"></div>

                <div class="signature-name">
                    @if (!empty($penerimaan->user?->name))
                        ( {{ $penerimaan->user->name }} )
                    @else
                        (__________________)
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>

<script>
    window.addEventListener('load', function () {
        window.print();
    });
</script>

</body>
</html>
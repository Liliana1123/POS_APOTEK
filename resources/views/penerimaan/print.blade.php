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
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0 0 5px;
            font-size: 20px;
        }

        .header p {
            margin: 0;
            color: #666;
        }

        .info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 30px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            gap: 8px;
        }

        .info-label {
            width: 110px;
            font-weight: bold;
        }

        .info-value {
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 7px;
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

        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            width: 220px;
            text-align: center;
        }

        .signature-space {
            height: 70px;
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
            }

            .print-button {
                display: none;
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
        <h1>BUKTI PENERIMAAN BARANG</h1>
        <p>POS Apotek</p>
    </div>

    <div class="info">

        <div class="info-item">
            <div class="info-label">No. Faktur</div>
            <div class="info-value">
                {{ $penerimaan->no_faktur }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">Tanggal Terima</div>
            <div class="info-value">
                {{ $penerimaan->tanggal?->format('d/m/Y') ?? '—' }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">Supplier</div>
            <div class="info-value">
                {{ $penerimaan->supplier->nama ?? '—' }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">Telepon Supplier</div>
            <div class="info-value">
                {{ $penerimaan->telepon_supplier ?? '—' }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">Dicatat Oleh</div>
            <div class="info-value">
                {{ $penerimaan->user->name ?? '—' }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">Status Pembayaran</div>
            <div class="info-value">
                {{ $penerimaan->lunas ? 'Lunas' : 'Belum Lunas' }}
            </div>
        </div>

        @if ($penerimaan->jatuh_tempo)
            <div class="info-item">
                <div class="info-label">Jatuh Tempo</div>
                <div class="info-value">
                    {{ $penerimaan->jatuh_tempo->format('d/m/Y') }}
                </div>
            </div>
        @endif

        @if ($penerimaan->keterangan)
            <div class="info-item">
                <div class="info-label">Keterangan</div>
                <div class="info-value">
                    {{ $penerimaan->keterangan }}
                </div>
            </div>
        @endif

    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Barang</th>
                <th>Barcode</th>
                <th>No. Batch</th>
                <th>Expired</th>
                <th>Rak</th>
                <th>Satuan</th>
                <th>Jumlah</th>
                <th>Harga Beli</th>
                <th>Subtotal</th>
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

        <div class="total-row grand-total">
            <span>Total Faktur</span>
            <span>
                Rp {{ number_format($penerimaan->totalFaktur(), 0, ',', '.') }}
            </span>
        </div>

        <div class="total-row">
            <span>Total Dibayar</span>
            <span>
                Rp {{ number_format($penerimaan->totalDibayar(), 0, ',', '.') }}
            </span>
        </div>

        <div class="total-row">
            <span>Sisa Tagihan</span>
            <span>
                Rp {{ number_format($penerimaan->sisaTagihan(), 0, ',', '.') }}
            </span>
        </div>

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

    </div>

    <div class="footer">

        <div class="signature">
            <div>Supplier</div>

            <div class="signature-space"></div>

            <strong>
                {{ $penerimaan->supplier->nama ?? '—' }}
            </strong>
        </div>

        <div class="signature">
            <div>Petugas Penerimaan</div>

            <div class="signature-space"></div>

            <strong>
                {{ $penerimaan->user->name ?? '—' }}
            </strong>
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
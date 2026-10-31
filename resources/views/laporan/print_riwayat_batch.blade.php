@php
    $apotek = \Illuminate\Support\Facades\Cache::remember('info_apotek', now()->addHours(6), fn () => \App\Models\InfoApotek::first());
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Riwayat Penjualan Batch - {{ $batch->barang?->nama ?? 'Barang' }} (Batch: {{ $batch->no_batch ?? '—' }})</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            font-size: 12px;
            color: #222;
            background: #fff;
        }

        .container {
            max-width: 1020px;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 25px;
        }

        /* =========================
           KOP SURAT (SESUAI CETAK PENERIMAAN)
        ========================= */
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
            font-size: 22px;
            font-weight: bold;
            color: #222;
        }

        .kop-info p {
            margin: 2px 0;
            font-size: 12px;
            color: #444;
        }

        /* =========================
           JUDUL DOKUMEN DI BAWAH KOP
        ========================= */
        .judul-dokumen {
            text-align: center;
            margin-top: 18px;
        }

        .judul-dokumen h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }

        /* =========================
           INFORMASI IDENTITAS BARANG & BATCH
           (MENGGUNAKAN POLA .INFO CETAK PENERIMAAN)
        ========================= */
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
            width: 150px;
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

        .info-value-highlight {
            font-weight: bold;
            font-size: 13px;
            color: #111;
        }

        .info-value-batch {
            font-weight: bold;
            font-size: 13px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            letter-spacing: 0.5px;
        }

        /* =========================
           TABEL RIWAYAT PENJUALAN
        ========================= */
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

        .text-left {
            text-align: left;
        }

        /* =========================
           RINGKASAN TOTAL
        ========================= */
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

        /* =========================
           BAGIAN TANDA TANGAN
        ========================= */
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

        .signature-sipa {
            margin-top: 4px;
            font-size: 12px;
            color: #444;
        }

        /* =========================
           TOMBOL NON-PRINT
        ========================= */
        .action-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 8px;
            z-index: 100;
        }

        .print-button {
            padding: 9px 15px;
            border: 0;
            border-radius: 5px;
            background: #222;
            color: #fff;
            cursor: pointer;
            font-weight: bold;
            font-size: 12px;
        }

        .close-button {
            padding: 9px 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #fff;
            color: #222;
            cursor: pointer;
            font-size: 12px;
        }

        /* =========================
           PAGINATION & CETAK LANDSCAPE
        ========================= */
        @page {
            size: A4 landscape;
            margin: 10mm 15mm;
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

            .action-buttons,
            .print-button,
            .close-button {
                display: none;
            }

            .no-break {
                -webkit-column-break-inside: avoid;
                page-break-inside: avoid;
                break-inside: avoid;
            }

            table thead {
                display: table-header-group;
            }

            table tbody tr {
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

    {{-- TOMBOL AKSI NON-PRINT --}}
    <div class="action-buttons">
        <button type="button" class="print-button" onclick="window.print()">
            Print
        </button>
        <button type="button" class="close-button" onclick="window.close()">
            Tutup
        </button>
    </div>

    <div class="header">

        {{-- KOP SURAT (PERSIS DENGAN CETAK PENERIMAAN) --}}
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
            <h1>LAPORAN RIWAYAT PENJUALAN BATCH</h1>
        </div>

    </div>

    {{-- BAGIAN INFORMASI IDENTITAS BATCH (STRUKTUR SAMA DENGAN CETAK PENERIMAAN) --}}
    <div class="info">
        <div class="info-item">
            <div class="info-label">Nama Barang</div>
            <div class="info-colon">:</div>
            <div class="info-value info-value-highlight">
                {{ $batch->barang?->nama ?? '—' }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">Nomor Batch</div>
            <div class="info-colon">:</div>
            <div class="info-value info-value-batch">
                {{ $batch->no_batch ?? '—' }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">Kategori Produk</div>
            <div class="info-colon">:</div>
            <div class="info-value">
                {{ $batch->barang?->kategori?->nama ?: '—' }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">Nomor Rak</div>
            <div class="info-colon">:</div>
            <div class="info-value">
                {{ $batch->no_rak ?: '—' }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">Tanggal Kedaluwarsa</div>
            <div class="info-colon">:</div>
            <div class="info-value">
                {{ $batch->expired_date ? \Carbon\Carbon::parse($batch->expired_date)->translatedFormat('d/m/Y') : '—' }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">Waktu Dicetak</div>
            <div class="info-colon">:</div>
            <div class="info-value">
                {{ now()->translatedFormat('d/m/Y H:i') }} WIB
            </div>
        </div>
    </div>

    {{-- TABEL RIWAYAT PENJUALAN BATCH --}}
    <table>
        <thead>
            <tr>
                <th style="width: 35px;">No.</th>
                <th style="width: 140px;">Tanggal Penjualan</th>
                <th>Nama Pembeli / Pelanggan</th>
                <th style="width: 160px;">No. Invoice</th>
                <th style="width: 150px;">Petugas / Kasir</th>
                <th style="width: 120px;" class="text-right">Jumlah Terjual</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($data as $index => $item)
                <tr>
                    <td class="text-center">
                        {{ $item['no'] }}
                    </td>
                    <td class="text-center" style="white-space: nowrap;">
                        {{ $item['tanggal'] }}
                    </td>
                    <td class="text-left">
                        {{ $item['nama_pembeli'] }}
                    </td>
                    <td class="text-center" style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-weight: bold;">
                        {{ $item['no_invoice'] }}
                    </td>
                    <td class="text-left">
                        {{ $item['petugas'] }}
                    </td>
                    <td class="text-right" style="font-weight: bold;">
                        {{ number_format($item['jumlah'], 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; font-style: italic; color: #666;">
                        Belum ada riwayat penjualan untuk batch ini.
                    </td>
                </tr>
            @endforelse
        </tbody>

        @if(count($data) > 0)
            <tfoot>
                <tr style="font-weight: bold;">
                    <th colspan="5" class="text-right">Total Unit Terjual:</th>
                    <th class="text-right" style="font-weight: bold;">
                        {{ number_format($totalTerjual, 0, ',', '.') }}
                    </th>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- RINGKASAN TOTAL (STRUKTUR TOTAL-SECTION SESUAI CETAK PENERIMAAN) --}}
    <div class="total-section">
        <div class="total-row">
            <span>Total Transaksi Tercatat</span>
            <span>{{ number_format($totalTransaksi, 0, ',', '.') }} Transaksi</span>
        </div>
        <div class="total-row grand-total">
            <span>Total Unit Terjual</span>
            <span>{{ number_format($totalTerjual, 0, ',', '.') }} Unit</span>
        </div>
    </div>

    {{-- BAGIAN TANDA TANGAN (PERSIS DENGAN STRUKTUR CETAK PENERIMAAN) --}}
    <div class="no-break">
        <div class="footer-ttd">

            <div class="signature">
                <div class="signature-title">Mengetahui,</div>
                <div class="signature-role">Penanggung Jawab Apotek</div>

                <div class="signature-space"></div>

                <div class="signature-name">
                    @if (!empty($apotek?->nama_apoteker_pj))
                        ( {{ $apotek->nama_apoteker_pj }} )
                    @else
                        (__________________)
                    @endif
                </div>

                @if (!empty($apotek?->no_sipa))
                    <div class="signature-sipa">
                        SIPA: {{ $apotek->no_sipa }}
                    </div>
                @endif
            </div>

            <div class="signature">
                <div class="signature-title">Dibuat oleh,</div>
                <div class="signature-role">Petugas Laporan</div>

                <div class="signature-space"></div>

                <div class="signature-name">
                    @if (!empty(auth()->user()?->name))
                        ( {{ auth()->user()->name }} )
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
        setTimeout(function () {
            window.print();
        }, 300);
    });
</script>

</body>
</html>

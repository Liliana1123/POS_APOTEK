@php
    $apotek = \Illuminate\Support\Facades\Cache::remember(
        'info_apotek',
        now()->addHours(6),
        fn () => \App\Models\InfoApotek::first()
    );
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Barang Rusak - {{ $rusak->detailPenerimaan->barang->nama ?? '' }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 30px;
        }

        .header-apotek {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-apotek h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-apotek p {
            margin: 2px 0 0 0;
            font-size: 11px;
            color: #555;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 8px 5px;
            vertical-align: top;
        }

        .label {
            width: 180px;
            font-weight: bold;
        }

        .jumlah-rusak {
            color: #DC2626;
            font-weight: bold;
        }

        @media print {
            body {
                margin: 15px;
            }
        }
    </style>
</head>

<body>

    <div class="header-apotek">
        <h1>{{ $apotek?->nama_apotek ?? 'POS APOTEK' }}</h1>
        @if($apotek?->alamat)<p>{{ $apotek->alamat }}</p>@endif
        @if($apotek?->telepon || $apotek?->no_izin_sia)
            <p>
                @if($apotek?->telepon)Telp: {{ $apotek->telepon }}@endif
                @if($apotek?->telepon && $apotek?->no_izin_sia) • @endif
                @if($apotek?->no_izin_sia)SIA: {{ $apotek->no_izin_sia }}@endif
            </p>
        @endif
    </div>

    <h2>BUKTI BARANG RUSAK</h2>

    <table>
        <tr>
            <td class="label">Tanggal Lapor</td>
            <td>: {{ $rusak->tanggal?->format('d-m-Y') ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Nama Barang</td>
            <td>: {{ $rusak->detailPenerimaan->barang->nama ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">No. Batch</td>
            <td>: {{ $rusak->detailPenerimaan->no_batch ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">No. Rak</td>
            <td>: {{ $rusak->detailPenerimaan->no_rak ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Expired Date</td>
            <td>: {{ $rusak->detailPenerimaan->expired_date?->format('d-m-Y') ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Jumlah Rusak</td>
            <td class="jumlah-rusak">
                : {{ $rusak->jumlah }}
            </td>
        </tr>

        <tr>
            <td class="label">Keterangan</td>
            <td>: {{ $rusak->keterangan ?? '-' }}</td>
        </tr>
    </table>

    <script>
        window.onload = function () {
            window.print();
        };
    </script>

</body>
</html>
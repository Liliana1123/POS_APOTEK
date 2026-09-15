<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Barang Rusak</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 30px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
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
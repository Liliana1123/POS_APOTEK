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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Bukti Barang Rusak - {{ $rusak->detailPenerimaan->barang->nama ?? '' }}
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #222;
            background: #fff;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        /* =========================
           KOP SURAT
        ========================= */

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
            text-transform: uppercase;
        }

        .kop-info p {
            margin: 3px 0;

            font-size: 12px;
            color: #555;
        }

        /* =========================
           JUDUL DOKUMEN
        ========================= */

        .judul-dokumen {
            margin-top: 18px;
            text-align: center;
        }

        .judul-dokumen h1 {
            margin: 0;

            font-size: 20px;
            font-weight: bold;
            color: #222;
        }

        /* =========================
           DATA BARANG RUSAK
        ========================= */

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .detail-table td {
            padding: 8px 5px;
            vertical-align: top;
        }

        .detail-table .label {
            width: 180px;
            font-weight: bold;
        }

        .jumlah-rusak {
            color: #DC2626;
            font-weight: bold;
        }

        /* =========================
           PRINT
        ========================= */

        @media print {
            body {
                padding: 15px;
            }

            .container {
                max-width: none;
            }
        }
    </style>
</head>

<body>

<div class="container">

    {{-- =========================
         KOP SURAT
    ========================= --}}
    <div class="header">

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

        {{-- JUDUL DOKUMEN --}}
        <div class="judul-dokumen">
            <h1>BUKTI BARANG RUSAK</h1>
        </div>

    </div>


    {{-- =========================
         DETAIL BARANG RUSAK
    ========================= --}}
    <table class="detail-table">

        <tr>
            <td class="label">
                Tanggal Lapor
            </td>

            <td>
                : {{ $rusak->tanggal?->format('d-m-Y') ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Nama Barang
            </td>

            <td>
                : {{ $rusak->detailPenerimaan->barang->nama ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                No. Batch
            </td>

            <td>
                : {{ $rusak->detailPenerimaan->no_batch ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                No. Rak
            </td>

            <td>
                : {{ $rusak->detailPenerimaan->no_rak ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Expired Date
            </td>

            <td>
                : {{ $rusak->detailPenerimaan->expired_date?->format('d-m-Y') ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Jumlah Rusak
            </td>

            <td class="jumlah-rusak">
                : {{ $rusak->jumlah }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Keterangan
            </td>

            <td>
                : {{ $rusak->keterangan ?? '-' }}
            </td>
        </tr>

    </table>

</div>


<script>
    window.onload = function () {
        window.print();
    };
</script>

</body>
</html>
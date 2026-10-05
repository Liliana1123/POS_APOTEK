@extends('layouts.app')

@section('title', 'Laporan Stok')

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
            Laporan Ketersediaan Stok dan Kadaluarsa
        </h2>

        <p>
            Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
        </p>

    </div>

</div>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 print:hidden no-print page-header-web">
    <div>
        <h1>Laporan Monitoring Stok dan Kadaluarsa</h1>

        <p class="text-caption mt-1">
            Analisis ketersediaan stok obat serta deteksi dini batch kadaluarsa.
        </p>
    </div>

    <div class="flex gap-2 shrink-0">
        <a
            href="{{ route('laporan.stok', array_merge(request()->query(), ['export' => 'excel'])) }}"
            class="btn-secondary py-2 px-4 flex items-center justify-center gap-1.5"
        >
            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
            Ekspor Excel
        </a>

        <button
            onclick="printReport()"
            class="btn-primary py-2 px-4"
        >
            Cetak Laporan
        </button>
    </div>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6 print:hidden no-print filter-card-web">
    <form method="GET" action="{{ route('laporan.stok') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label for="nama" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Nama Barang
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="nama"
                        id="nama"
                        value="{{ request('nama') }}"
                        placeholder="Cari nama barang..."
                        class="form-input pr-8"
                    >
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </span>
                </div>
            </div>

            <div>
                <label for="kategori_id" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Kategori
                </label>
                <select
                    name="kategori_id"
                    id="kategori_id"
                    class="form-input"
                >
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoris as $kategori)
                        <option
                            value="{{ $kategori->id }}"
                            @selected(request('kategori_id') == $kategori->id)
                        >
                            {{ $kategori->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status_expired" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Status Kadaluarsa
                </label>
                <select
                    name="status_expired"
                    id="status_expired"
                    class="form-input"
                >
                    <option value="">Semua Status</option>
                    <option value="kadaluarsa" @selected(request('status_expired') === 'kadaluarsa')>
                        Kadaluarsa
                    </option>
                    <option value="1_bulan" @selected(request('status_expired') === '1_bulan')>
                        < 1 Bulan
                    </option>
                    <option value="3_bulan" @selected(request('status_expired') === '3_bulan')>
                        < 3 Bulan
                    </option>
                    <option value="normal" @selected(request('status_expired') === 'normal')>
                        Normal
                    </option>
                    <option value="tidak_ada" @selected(request('status_expired') === 'tidak_ada')>
                        Tidak Ada Tanggal
                    </option>
                </select>
            </div>

            <div>
                <label for="status_stok" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Status Stok
                </label>
                <select
                    name="status_stok"
                    id="status_stok"
                    class="form-input"
                >
                    <option value="">Semua Status</option>
                    <option value="habis" @selected(request('status_stok') === 'habis')>
                        Habis
                    </option>
                    <option value="menipis" @selected(request('status_stok') === 'menipis')>
                        Menipis
                    </option>
                    <option value="aman" @selected(request('status_stok') === 'aman')>
                        Aman
                    </option>
                </select>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            @if(request()->anyFilled(['nama', 'barang', 'kategori_id', 'supplier_id', 'batch', 'no_batch', 'status_expired', 'status_stok', 'tanggal', 'dari', 'sampai']))
                <a
                    href="{{ route('laporan.stok') }}"
                    class="btn-secondary py-1.5 px-4 flex items-center justify-center"
                >
                    Reset
                </a>
            @endif

            <button type="submit" class="btn-primary py-1.5 px-4">
                Filter
            </button>

        </div>
    </form>
</div>


<!-- ========================================================= -->
<!-- SECTION 1 : STOK PER BATCH -->
<!-- ========================================================= -->
<div class="mb-8 print-section">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2 print:mb-1">
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 font-sans print:text-gray-900">
                Stok Per Batch
            </h2>
            <p class="text-caption mt-0.5 print:text-[8px] print:mt-0">
                Rincian mutasi stok per batch: Stok Awal − Stok Terjual − Stok Rusak = Sisa Stok.
            </p>
        </div>
        <div class="text-xs font-medium text-gray-500 print:text-[8px]">
            Menampilkan <span class="font-semibold text-gray-700 print:text-gray-900">{{ $stokPerBatch->count() }}</span> batch
        </div>
    </div>

    <div class="table-custom-container print-table-wrapper">
        <div class="overflow-x-auto print-table-wrapper">

            <table class="table-custom print-table w-full min-w-[62rem]">
                <thead class="table-custom-header">
                    <tr>
                        <th scope="col" class="w-10 text-center !px-2">
                            No
                        </th>
                        <th scope="col" class="w-44 max-w-[12rem] !px-2.5">
                            Barang
                        </th>
                        <th scope="col" class="w-28 max-w-[7.5rem] !px-2 whitespace-nowrap">
                            Kategori
                        </th>
                        <th scope="col" class="w-24 text-center !px-2 whitespace-nowrap">
                            No. Batch
                        </th>
                        <th scope="col" class="w-20 text-center !px-2 whitespace-nowrap">
                            No. Rak
                        </th>
                        <th scope="col" class="text-center w-28 !px-2 whitespace-nowrap">
                            Expired
                        </th>
                        <th scope="col" class="text-center w-24 !px-2 whitespace-nowrap">
                            Status Expired
                        </th>
                        <th scope="col" class="text-right w-16 !px-2 whitespace-nowrap">
                            Stok Awal
                        </th>
                        <th scope="col" class="text-right w-16 !px-2 whitespace-nowrap">
                            Stok Terjual
                        </th>
                        <th scope="col" class="text-right w-16 !px-2 whitespace-nowrap">
                            Stok Rusak
                        </th>
                        <th scope="col" class="text-right w-16 !px-2 whitespace-nowrap">
                            Sisa Stok
                        </th>
                        <th scope="col" class="text-center w-20 !px-2 whitespace-nowrap">
                            Status Stok
                        </th>
                    </tr>
                </thead>

                <tbody class="table-custom-body">
                    @forelse ($stokPerBatch as $index => $item)
                        @php
                            $today = now()->startOfDay();
                            $oneMonth = $today->copy()->addMonth();
                            $threeMonths = $today->copy()->addMonths(3);

                            $expiredDate = $item->expired_date
                                ? \Carbon\Carbon::parse($item->expired_date)->startOfDay()
                                : null;

                            $stokAwal = (int) $item->jumlah;
                            $stokTerjual = (int) ($item->stok_terjual ?? 0);
                            $stokRusak = (int) ($item->stok_rusak ?? 0);
                            $sisaStok = $stokAwal - $stokTerjual - $stokRusak;
                            $stokMinimum = (int) ($item->barang->stok_minimum ?? 0);
                        @endphp

                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                            <!-- No -->
                            <td class="table-num text-center">
                                {{ $index + 1 }}
                            </td>

                            <!-- Barang -->
                            <td class="font-medium text-gray-800 !px-2.5 max-w-[12rem] break-words text-xs leading-snug">
                                {{ $item->barang->nama ?? '—' }}
                            </td>

                            <!-- Kategori -->
                            <td class="text-gray-600 !px-2 text-xs max-w-[7.5rem] truncate" title="{{ $item->barang->kategori->nama ?? '—' }}">
                                {{ $item->barang->kategori->nama ?? '—' }}
                            </td>

                            <!-- Batch -->
                            <td class="font-mono text-gray-600 text-center !px-2 whitespace-nowrap">
                                {{ $item->no_batch ?? '—' }}
                            </td>

                            <!-- Rak -->
                            <td class="font-mono text-gray-600 text-center !px-2 whitespace-nowrap">
                                {{ $item->no_rak ?? '—' }}
                            </td>

                            <!-- Expired -->
                            <td class="text-center font-mono !px-2 whitespace-nowrap">
                                {{ $expiredDate ? $expiredDate->translatedFormat('d F Y') : '—' }}
                            </td>

                            <!-- Status Expired -->
                            <td class="text-center !px-2 whitespace-nowrap">
                                @if (!$expiredDate)
                                    <span class="badge-secondary">
                                        Tidak Ada Tanggal
                                    </span>
                                @elseif ($expiredDate->isSameDay($today) || $expiredDate->isBefore($today))
                                    <span class="badge-danger">
                                        KADALUARSA
                                    </span>
                                @elseif ($expiredDate->lt($oneMonth))
                                    <span class="badge-orange">
                                        < 1 Bulan
                                    </span>
                                @elseif ($expiredDate->lt($threeMonths))
                                    <span class="badge-warning">
                                        < 3 Bulan
                                    </span>
                                @else
                                    <span class="badge-success">
                                        Normal
                                    </span>
                                @endif
                            </td>

                            <!-- Stok Awal -->
                            <td class="table-num font-medium text-gray-700 !px-2 text-right whitespace-nowrap">
                                {{ number_format($stokAwal, 0, ',', '.') }}
                            </td>

                            <!-- Stok Terjual -->
                            <td class="table-num font-semibold text-indigo-600 !px-2 text-right whitespace-nowrap">
                                {{ number_format($stokTerjual, 0, ',', '.') }}
                            </td>

                            <!-- Stok Rusak -->
                            <td class="table-num font-semibold text-rose-600 !px-2 text-right whitespace-nowrap">
                                {{ number_format($stokRusak, 0, ',', '.') }}
                            </td>

                            <!-- Sisa Stok Saat Ini -->
                            <td class="table-num font-bold {{ $sisaStok <= 0 ? 'text-red-600' : ($sisaStok <= $stokMinimum ? 'text-amber-600' : 'text-emerald-700') }} !px-2 text-right whitespace-nowrap">
                                {{ number_format($sisaStok, 0, ',', '.') }}
                            </td>

                            <!-- Status Stok -->
                            <td class="text-center !px-2 whitespace-nowrap">
                                @if ($sisaStok <= 0)
                                    <span class="badge-danger">Habis</span>
                                @elseif ($sisaStok <= $stokMinimum)
                                    <span class="badge-warning">Menipis</span>
                                @else
                                    <span class="badge-success">Aman</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="p-0">
                                <div class="empty-state-container">
                                    <div class="empty-state-title">
                                        @if(request()->anyFilled(['nama', 'kategori_id', 'status_expired', 'status_stok']))
                                            Stok Batch Tidak Ditemukan
                                        @else
                                            Stok Batch Kosong
                                        @endif
                                    </div>
                                    <div class="empty-state-desc">
                                        @if(request()->anyFilled(['nama', 'kategori_id', 'status_expired', 'status_stok']))
                                            Tidak ada data batch obat yang cocok dengan filter kriteria Anda.
                                        @else
                                            Belum ada data stok batch obat yang terdaftar di sistem.
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if ($stokPerBatch->isNotEmpty())
                    <tfoot class="bg-gray-50/50 border-t font-bold text-xs">
                        <tr>
                            <td colspan="7" class="!px-3 py-3 text-right uppercase tracking-wider text-gray-600">
                                Total (Ringkasan Data Tampil):
                            </td>
                            <td class="table-num !px-2 py-3 text-right text-gray-800 font-bold whitespace-nowrap">
                                {{ number_format($totalStokAwal, 0, ',', '.') }}
                            </td>
                            <td class="table-num !px-2 py-3 text-right text-indigo-700 font-bold whitespace-nowrap">
                                {{ number_format($totalStokTerjual, 0, ',', '.') }}
                            </td>
                            <td class="table-num !px-2 py-3 text-right text-rose-700 font-bold whitespace-nowrap">
                                {{ number_format($totalStokRusak, 0, ',', '.') }}
                            </td>
                            <td class="table-num !px-2 py-3 text-right {{ $totalSisaStok <= 0 ? 'text-red-600' : 'text-emerald-700' }} font-bold whitespace-nowrap">
                                {{ number_format($totalSisaStok, 0, ',', '.') }}
                            </td>
                            <td class="!px-2 py-3"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
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
                    No. SIPA: {{ $apotek->no_sipa }}
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


<!-- ========================================================= -->
<!-- SECTION 2 : BATCH MENDEKATI EXPIRED -->
<!-- ========================================================= -->
<div class="mb-8 no-print print:hidden no-print-expired">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 font-sans">
                Batch Mendekati Expired (&le; 90 Hari)
            </h2>
            <p class="text-caption mt-0.5">
                Daftar obat dengan sisa stok aktif yang akan kadaluarsa dalam rentang waktu dekat.
            </p>
        </div>
        <div class="text-xs font-medium text-gray-500">
            Menampilkan <span class="font-semibold text-gray-700">{{ $mendekatiExpired->count() }}</span> batch
        </div>
    </div>

   <div class="table-custom-container print-table-wrapper">
        <div class="overflow-x-auto print-table-wrapper">

            <table class="table-custom print-table w-full min-w-[58rem]">
                <thead class="table-custom-header">
                    <tr>
                        <th scope="col" class="w-10 text-center !px-2">
                            No
                        </th>
                        <th scope="col" class="w-44 max-w-[12rem] !px-2.5">
                            Barang
                        </th>
                        <th scope="col" class="w-28 max-w-[7.5rem] !px-2 whitespace-nowrap">
                            Kategori
                        </th>
                        <th scope="col" class="w-24 text-center !px-2 whitespace-nowrap">
                            No. Batch
                        </th>
                        <th scope="col" class="w-20 text-center !px-2 whitespace-nowrap">
                            No. Rak
                        </th>
                        <th scope="col" class="text-center w-28 !px-2 whitespace-nowrap">
                            Expired
                        </th>
                        <th scope="col" class="text-center w-24 !px-2 whitespace-nowrap">
                            Status Expired
                        </th>
                        <th scope="col" class="text-right w-20 !px-2 whitespace-nowrap">
                            Sisa Stok
                        </th>
                    </tr>
                </thead>

                <tbody class="table-custom-body">
                    @forelse ($mendekatiExpired as $index => $item)
                        @php
                            $today = now()->startOfDay();
                            $oneMonth = $today->copy()->addMonth();
                            $threeMonths = $today->copy()->addMonths(3);

                            $expiredDate = $item->expired_date
                                ? \Carbon\Carbon::parse($item->expired_date)->startOfDay()
                                : null;
                            $sisaStokExp = (int) $item->jumlah - (int) ($item->stok_terjual ?? 0) - (int) ($item->stok_rusak ?? 0);
                            $stokMinimum = (int) ($item->barang->stok_minimum ?? 0);
                        @endphp

                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                            <td class="table-num text-center">
                                {{ $index + 1 }}
                            </td>

                            <td class="font-medium text-gray-800 !px-2.5 max-w-[12rem] break-words text-xs leading-snug">
                                {{ $item->barang->nama ?? '—' }}
                            </td>

                            <td class="text-gray-600 !px-2 text-xs max-w-[7.5rem] truncate" title="{{ $item->barang->kategori->nama ?? '—' }}">
                                {{ $item->barang->kategori->nama ?? '—' }}
                            </td>

                            <td class="font-mono text-gray-600 text-center !px-2 whitespace-nowrap">
                                {{ $item->no_batch ?? '—' }}
                            </td>

                            <td class="font-mono text-gray-600 text-center !px-2 whitespace-nowrap">
                                {{ $item->no_rak ?? '—' }}
                            </td>

                            <td class="text-center font-mono !px-2 whitespace-nowrap">
                                {{ $expiredDate ? $expiredDate->translatedFormat('d F Y') : '—' }}
                            </td>

                            <td class="text-center !px-2 whitespace-nowrap">
                                @if (!$expiredDate)
                                    <span class="badge-secondary">
                                        Tidak Ada Tanggal
                                    </span>
                                @elseif ($expiredDate->isSameDay($today) || $expiredDate->isBefore($today))
                                    <span class="badge-danger">
                                        KADALUARSA
                                    </span>
                                @elseif ($expiredDate->lt($oneMonth))
                                    <span class="badge-orange">
                                        < 1 Bulan
                                    </span>
                                @elseif ($expiredDate->lt($threeMonths))
                                    <span class="badge-warning">
                                        < 3 Bulan
                                    </span>
                                @else
                                    <span class="badge-success">
                                        Normal
                                    </span>
                                @endif
                            </td>

                            <td class="table-num font-bold {{ $sisaStokExp <= 0 ? 'text-red-600' : ($sisaStokExp <= $stokMinimum ? 'text-amber-600' : 'text-emerald-700') }} !px-2 text-right whitespace-nowrap">
                                {{ number_format($sisaStokExp, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-0">
                                <div class="empty-state-container">
                                    <div class="empty-state-title">
                                        Tidak Ada Batch Kadaluarsa atau Mendekati Expired
                                    </div>
                                    <div class="empty-state-desc">
                                        Tidak terdapat batch dengan sisa stok aktif yang sudah kadaluarsa atau akan kadaluarsa dalam 90 hari.
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if ($mendekatiExpired->isNotEmpty())
                    <tfoot class="bg-gray-50/50 border-t font-bold text-xs">
                        <tr>
                            <td colspan="7" class="px-4 py-3 text-right uppercase tracking-wider text-gray-600">
                                Total Sisa Stok Kadaluarsa dan Mendekati Expired:
                            </td>
                            <td class="table-num !px-2 py-3 text-right text-red-600 font-bold whitespace-nowrap">
                                {{ number_format($mendekatiExpired->sum(fn ($i) => (int) $i->jumlah - (int) ($i->stok_terjual ?? 0) - (int) ($i->stok_rusak ?? 0)), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
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
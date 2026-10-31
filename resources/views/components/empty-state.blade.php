@props([
    'colspan' => '100%',
    'title' => null,
    'message' => null,
    'description' => null,
    'desc' => null,
    'search' => null,
    'hasFilter' => null,
])

@php
    $searchTerm = $search ?? request('cari') ?? request('search');
    $isFiltered = $hasFilter ?? (
        !empty($searchTerm) || request()->anyFilled([
            'dari', 'sampai', 'tanggal', 'tanggal_mulai', 'tanggal_akhir',
            'kategori_id', 'supplier_id', 'pabrik_id', 'status',
            'status_penerimaan', 'status_pembayaran', 'status_follow_up',
            'no_batch', 'role', 'jenis', 'jenis_transaksi', 'metode_pembayaran',
            'nama_barang', 'nama', 'barang', 'batch', 'no_faktur',
            'promo_id', 'status_pelanggan', 'status_expired', 'status_stok', 'keterangan'
        ])
    );

    $defaultTitle = $isFiltered ? 'Data Tidak Ditemukan' : 'Data Kosong';
    $defaultDesc = $isFiltered
        ? ($searchTerm
            ? 'Tidak ada data yang cocok dengan kata kunci "' . $searchTerm . '".'
            : 'Tidak ada data yang cocok dengan kriteria filter yang Anda terapkan.')
        : 'Belum ada data terdaftar di sistem.';

    $finalTitle = $title ?? $defaultTitle;
    $finalDesc = $description ?? $desc ?? $message ?? $defaultDesc;
@endphp

<tr>
    <td colspan="{{ $colspan }}" class="p-0">
        <div class="empty-state-container">
            <div class="empty-state-title">
                {{ $finalTitle }}
            </div>
            <div class="empty-state-desc">
                {{ $finalDesc }}
            </div>
            @if(isset($slot) && $slot->isNotEmpty())
                <div class="mt-3 flex items-center justify-center gap-2">
                    {{ $slot }}
                </div>
            @endif
        </div>
    </td>
</tr>

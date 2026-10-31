<!-- Info Cards Grid -->
<div class="card-base p-4 mb-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Tanggal Terima</span>
        <strong class="text-gray-800 text-sm">@tanggalId($penerimaan->tanggal, 'd F Y')</strong>
    </div>

    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Tanggal Faktur</span>
        <strong class="text-gray-800 text-sm">@tanggalId($penerimaan->tanggal_faktur, 'd F Y')</strong>
    </div>

    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">No. Faktur</span>
        <strong class="text-gray-800 text-sm">{{ $penerimaan->no_faktur }}</strong>
    </div>

    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Supplier</span>
        <strong class="text-gray-800 text-sm">{{ $penerimaan->supplier->nama ?? '' }}</strong>
    </div>

    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Dicatat Oleh</span>
        <strong class="text-gray-800 text-sm">{{ $penerimaan->user->name ?? '' }}</strong>
    </div>

    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Status Pembayaran</span>
        <div class="mt-0.5">
            <x-status-badge type="pembayaran" :status="$penerimaan->lunas" />
        </div>
    </div>

    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Status Penerimaan</span>
        <div class="mt-0.5">
            <x-status-badge type="penerimaan" :status="$penerimaan->statusPenerimaan()" />
        </div>
    </div>

    @if ($penerimaan->jatuh_tempo)
    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Jatuh Tempo</span>
        <strong class="text-gray-800 text-sm">@tanggalId($penerimaan->jatuh_tempo, 'd F Y')</strong>
    </div>
    @endif
</div>

@if ($penerimaan->keterangan)
<div class="card-base p-3 mb-4 text-xs bg-gray-50/50 border border-gray-200/70">
    <span class="font-bold text-gray-500 uppercase tracking-wider text-[10px] block mb-0.5">Catatan Faktur:</span>
    <p class="text-gray-700">{{ $penerimaan->keterangan }}</p>
</div>
@endif

<!-- Table List: Pemenuhan Pesanan & Rincian Fisik Batch -->
<div class="card-base p-0 mb-4 overflow-hidden border border-slate-200">
    <div class="overflow-x-auto">
        <table class="table-detail-penerimaan w-full">
            <thead>
                <tr>
                    <th scope="col" class="w-10 text-center">No</th>
                    <th scope="col" class="min-w-[14rem] text-left">Barang</th>
                    <th scope="col" class="text-center w-24">No. Batch</th>
                    <th scope="col" class="text-center w-24">Expired</th>
                    <th scope="col" class="text-center w-16">Rak</th>
                    <th scope="col" class="text-right w-20">Dipesan</th>
                    <th scope="col" class="text-right w-20">Diterima</th>
                    <th scope="col" class="text-right w-20">Dibatalkan</th>
                    <th scope="col" class="text-right w-20">Kurang</th>
                    <th scope="col" class="text-right w-24">Harga Beli</th>
                    <th scope="col" class="text-right w-24">Harga Jual</th>
                    <th scope="col" class="text-right w-28">Subtotal</th>
                    <th scope="col" class="min-w-[11rem]">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @if ($penerimaan->detailPesanan->isNotEmpty())
                @foreach ($penerimaan->detailPesanan as $index => $pesanan)
                @php
                $barang = $pesanan->barang;
                $bId = $pesanan->barang_id;
                $jumlahDipesan = (int) $pesanan->jumlah_dipesan;
                $totalDiterima = (int) $pesanan->totalDiterima();
                $totalDibatalkan = (int) $pesanan->totalDibatalkan();
                $kekurangan = (int) $pesanan->kekurangan();

                $batches = $penerimaan->detail->where('barang_id', $bId);
                $subtotalObat = $batches->sum(fn ($b) => (float) $b->harga_beli * (int) $b->jumlah);

                // Kumpulkan semua riwayat untuk pesanan ini
                $riwayatList = $penerimaan->riwayatPenerimaan
                ->where('detail_pesanan_penerimaan_id', $pesanan->id);

                // Tangani batch fisik yang belum terhubung ke riwayat (data lama)
                $batchIdsInRiwayat = $riwayatList->pluck('detail_penerimaan_id')->filter()->all();
                $untrackedBatches = $batches->whereNotIn('id', $batchIdsInRiwayat);

                $events = collect();

                foreach ($riwayatList as $rw) {
                $events->push([
                'type' => $rw->jenis,
                'tanggal' => $rw->tanggal ?? $penerimaan->tanggal,
                'batch' => $rw->detailPenerimaan,
                'jumlah' => (int) $rw->jumlah,
                'keterangan' => $rw->keterangan,
                'user' => $rw->user?->name,
                'id' => $rw->id,
                ]);
                }

                foreach ($untrackedBatches as $ub) {
                $events->push([
                'type' => 'penerimaan',
                'tanggal' => $penerimaan->tanggal,
                'batch' => $ub,
                'jumlah' => (int) $ub->jumlah,
                'keterangan' => 'Penerimaan',
                'user' => null,
                'id' => $ub->id,
                ]);
                }

                // Urutkan secara kronologis berdasarkan tanggal
                $events = $events->sortBy(function ($ev) {
                $tglStr = $ev['tanggal'] ? $ev['tanggal']->format('Y-m-d') : '9999-99-99';
                return $tglStr . '_' . str_pad($ev['id'], 10, '0', STR_PAD_LEFT);
                });
                @endphp

                {{-- Baris Induk: Ringkasan Pesanan Barang --}}
                <tr class="bg-slate-50/90 font-medium text-slate-800" style="border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;">
                    <td class="text-center text-slate-600 font-semibold">{{ $index + 1 }}</td>
                    <td>
                        <div class="text-slate-900 font-semibold">{{ $barang->nama ?? '' }}</div>
                        <div class="text-[11px] text-slate-500 font-normal">
                            Satuan: {{ $barang->satuan->nama ?? '' }} | Pabrik: {{ $barang->pabrik->nama ?? '' }}
                        </div>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-center text-slate-900 font-semibold">{{ number_format($jumlahDipesan) }}</td>
                    <td class="text-center text-blue-700 font-semibold">{{ number_format($totalDiterima) }}</td>
                    <td class="text-center {{ $totalDibatalkan > 0 ? 'text-red-600 font-semibold' : 'text-slate-400' }}">
                        {{ $totalDibatalkan > 0 ? number_format($totalDibatalkan) : '' }}
                    </td>
                    <td class="text-center {{ $kekurangan > 0 ? 'text-amber-600 font-semibold' : 'text-emerald-600 font-semibold' }}">
                        {{ $kekurangan > 0 ? number_format($kekurangan) : '' }}
                    </td>
                    <td></td>
                    <td></td>
                    <td class="text-right text-slate-900 font-semibold">
                        @rupiah($subtotalObat)
                    </td>
                    <td>
                        @if ($kekurangan === 0)
                        <span class="text-emerald-700 font-semibold">Lengkap</span>
                        @else
                        <span class="text-amber-700 font-semibold">Kurang {{ number_format($kekurangan) }}</span>
                        @endif
                    </td>
                </tr>

                {{-- Sub-baris: Rincian Kedatangan Fisik & Pembatalan (Urut Tanggal) --}}
                @forelse ($events as $ev)
                @if ($ev['type'] === 'penerimaan')
                @php
                $b = $ev['batch'];
                $hargaBeli = $b ? (float) $b->harga_beli : 0;
                $hargaJual = $b ? (float) $b->harga_jual : 0;
                $subtotalBatch = $hargaBeli * $ev['jumlah'];
                $isAwal = $ev['keterangan'] === 'Penerimaan awal' || str_contains(strtolower($ev['keterangan'] ?? ''), 'awal');
                $isSusulan = !$isAwal;
                $rowBg = $isSusulan ? 'hover:bg-emerald-50/30 bg-emerald-50/10' : 'hover:bg-slate-50/60 bg-white';
                @endphp
                <tr class="{{ $rowBg }} text-slate-700 transition-colors">
                    <td></td>
                    <td class="pl-6">
                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-400 font-mono text-[11px]">↳</span>
                            <span class="font-medium text-slate-700">{{ $ev['tanggal']?->format('d/m/Y') ?? '-' }}</span>
                            @if ($isSusulan)
                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                Susulan
                            </span>
                            @else
                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                Awal
                            </span>
                            @endif
                        </div>
                    </td>
                    <td class="text-center font-mono text-slate-800">{{ $b->no_batch ?? '-' }}</td>
                    <td class="text-center text-slate-600">{{ $b->expired_date?->format('d/m/Y') ?? '-' }}</td>
                    <td class="text-center text-slate-600">{{ $b->no_rak ?? '-' }}</td>
                    <td></td>
                    <td class="text-right font-semibold text-blue-700">{{ number_format($ev['jumlah']) }}</td>
                    <td></td>
                    <td></td>
                    <td class="text-right text-slate-700">@rupiah($hargaBeli)</td>
                    <td class="text-right text-slate-600">@if($hargaJual > 0) @rupiah($hargaJual) @else - @endif</td>
                    <td class="text-right font-medium text-slate-900">
                        @rupiah($subtotalBatch)
                    </td>
                    <td class="text-center text-slate-600 text-xs">
                        {{ $ev['keterangan'] ?: ($isSusulan ? 'Penerimaan susulan' : 'Penerimaan') }}
                        @if ($b && $b->stok < $b->jumlah)
                            <span class="text-slate-400 text-[11px] block sm:inline">(Sisa rak: {{ $b->stok }})</span>
                            @endif
                    </td>
                </tr>
                @elseif ($ev['type'] === 'pembatalan')
                <tr class="hover:bg-red-50/30 bg-red-50/15 text-slate-700 transition-colors">
                    <td></td>
                    <td class="pl-6">
                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-400 font-mono text-[11px]">↳</span>
                            <span class="font-medium text-slate-700">{{ $ev['tanggal']?->format('d/m/Y') ?? '-' }}</span>
                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-red-100 text-red-800">
                                Batal
                            </span>
                        </div>
                    </td>
                    <td class="text-center text-slate-400">-</td>
                    <td class="text-center text-slate-400">-</td>
                    <td class="text-center text-slate-400">-</td>
                    <td></td>
                    <td></td>
                    <td class="text-right font-semibold text-red-600">{{ number_format($ev['jumlah']) }}</td>
                    <td></td>
                    <td class="text-right text-slate-400">-</td>
                    <td class="text-right text-slate-400">-</td>
                    <td class="text-right text-slate-400">-</td>
                    <td class="text-red-700 font-medium text-xs">
                        {{ $ev['keterangan'] ?: 'Pembatalan kekurangan' }}
                    </td>
                </tr>
                @endif
                @empty
                <tr class="text-slate-400 bg-white">
                    <td></td>
                    <td class="pl-6 italic text-xs" colspan="12">
                        Belum ada fisik barang yang diterima untuk obat ini.
                    </td>
                </tr>
                @endforelse

                @endforeach
                @else
                {{-- Fallback jika faktur lama belum memiliki detailPesanan --}}
                @foreach ($penerimaan->detail as $index => $item)
                <tr class="hover:bg-slate-50/60 text-slate-700 bg-white">
                    <td class="text-center text-slate-600 font-semibold">{{ $index + 1 }}</td>
                    <td class="font-medium text-slate-900">{{ $item->barang->nama ?? '' }}</td>
                    <td class="text-center">{{ $item->no_batch }}</td>
                    <td class="text-center">{{ $item->expired_date?->format('d/m/Y') ?? '' }}</td>
                    <td class="text-center">{{ $item->no_rak ?? '' }}</td>
                    <td class="text-right">{{ number_format($item->jumlah) }}</td>
                    <td class="text-right text-blue-700 font-semibold">{{ number_format($item->jumlah) }}</td>
                    <td></td>
                    <td></td>
                    <td class="text-right">@rupiah($item->harga_beli)</td>
                    <td class="text-right">@if($item->harga_jual > 0) @rupiah($item->harga_jual) @endif</td>
                    <td class="text-right font-medium">@rupiah($item->harga_beli * $item->jumlah)</td>
                    <td class="text-emerald-700 font-semibold">Lengkap</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
    {{-- Riwayat Pembayaran --}}
    <div class="card-base p-3.5">
        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-2.5 flex items-center gap-1.5">
            <x-heroicon-o-banknotes class="w-4 h-4 text-emerald-600" />
            <span>Riwayat Pembayaran Faktur</span>
        </h4>

        @if ($penerimaan->pembayaran->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-gray-400 text-[10px] font-bold uppercase">
                        <th class="py-1 px-1.5 text-left">Tanggal</th>
                        <th class="py-1 px-1.5 text-right">Jumlah</th>
                        <th class="py-1 px-1.5 text-left pl-3">Keterangan</th>
                        <th class="py-1 px-1.5 text-left pl-2">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($penerimaan->pembayaran as $p)
                    <tr>
                        <td class="py-1.5 px-1.5 text-gray-600">@tanggalId($p->tanggal_bayar, 'd F Y')</td>
                        <td class="py-1.5 px-1.5 text-right font-medium text-emerald-600">
                            @rupiah($p->jumlah)
                        </td>
                        <td class="py-1.5 px-1.5 text-gray-600 pl-3">{{ $p->keterangan ?? 'Pembayaran' }}</td>
                        <td class="py-1.5 px-1.5 text-gray-500 pl-2">{{ $p->user->name ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-xs text-gray-400 italic py-2">Belum ada catatan pembayaran untuk faktur ini.</p>
        @endif
    </div>

    {{-- Ringkasan Total Tagihan & Hutang --}}
    <div class="card-base p-3.5 space-y-2">
        <div class="flex justify-between items-center text-xs pb-1.5 border-b border-slate-100">
            <span class="text-gray-500">Total Belanja (Fisik Diterima):</span>
            <strong class="text-gray-800 text-sm">@rupiah($penerimaan->totalFaktur())</strong>
        </div>

        <div class="flex justify-between items-center text-xs pb-1.5 border-b border-slate-100">
            <span class="text-gray-500">PPN (11%):</span>
            <strong class="text-gray-800">@rupiah($penerimaan->ppn)</strong>
        </div>

        <div class="flex justify-between items-center text-xs pb-1.5 border-b border-slate-100">
            <span class="text-gray-800 font-bold">Total Tagihan Faktur:</span>
            <strong class="text-blue-700 text-base font-bold">@rupiah($penerimaan->totalTagihan())</strong>
        </div>

        <div class="flex justify-between items-center text-xs pb-1.5 border-b border-slate-100">
            <span class="text-gray-500">Total Telah Dibayar:</span>
            <strong class="text-emerald-600 font-bold">@rupiah($penerimaan->totalDibayar())</strong>
        </div>

        @if ($penerimaan->sisaTagihan() > 0)
        <div class="flex justify-between items-center text-xs pt-1 text-red-600">
            <span class="font-bold">Sisa Hutang Faktur:</span>
            <strong class="text-base font-bold">@rupiah($penerimaan->sisaTagihan())</strong>
        </div>
        @else
        <div class="flex justify-between items-center text-xs pt-1 text-emerald-600">
            <span class="font-bold">Status Tagihan:</span>
            <x-status-badge type="pembayaran" status="lunas" />
        </div>
        @endif

        @if ($penerimaan->kelebihanPembayaran() > 0)
        <div class="flex justify-between items-center text-xs pt-1 text-amber-600">
            <span class="font-bold">Kelebihan Bayar:</span>
            <strong class="font-bold">@rupiah($penerimaan->kelebihanPembayaran())</strong>
        </div>
        @endif
    </div>
</div>
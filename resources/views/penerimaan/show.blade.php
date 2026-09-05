
<!-- Info Cards Grid -->
<div class="card-base p-6 mb-6 grid grid-cols-2 md:grid-cols-4 gap-6 text-xs">
    
    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1 font-sans">Tanggal Terima</span>
        <strong class="text-gray-800 text-sm font-sans">{{ $penerimaan->tanggal->format('d M Y') }}</strong>
    </div>

    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1 font-sans">
            No. Faktur
        </span>
        <strong class="text-gray-800 text-sm font-mono">
            {{ $penerimaan->no_faktur }}
        </strong>
    </div>


    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1 font-sans">Supplier</span>
        <strong class="text-gray-800 text-sm font-sans">{{ $penerimaan->supplier->nama ?? '—' }}</strong>
    </div>
    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1 font-sans">Dicatat Oleh</span>
        <strong class="text-gray-800 text-sm font-sans">{{ $penerimaan->user->name ?? '—' }}</strong>
    </div>
    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1 font-sans">Status Pembayaran</span>
        <div class="mt-1">
            @if ($penerimaan->lunas)
                <span class="badge-success">Lunas</span>
            @else
                <span class="badge-warning">Belum Lunas</span>
            @endif
        </div>
    </div>
</div>

<!-- Table list -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[50rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="w-16">No</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">No. Batch</th>
                    <th scope="col">No. Rak</th>
                    <th scope="col" class="text-right w-32">Harga Beli</th>
                    <th scope="col" class="text-right w-32">Harga Jual</th>
                    <th scope="col" class="text-center w-36">Expired Date</th>
                    <th scope="col" class="text-center">Satuan</th>
                    <th scope="col" class="text-right">Subtotal</th>
                    <th scope="col" class="text-right w-24">Diterima</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-y divide-gray-150">
                @foreach ($penerimaan->detail as $index => $item)
                    <tr>
                        <td class="table-num">{{ $index + 1 }}</td>
                        <td class="font-medium text-gray-800">{{ $item->barang->nama ?? '—' }}</td>
                        <td class="font-mono text-gray-600">{{ $item->no_batch }}</td>
                        <td class="text-gray-600">{{ $item->no_rak ?? '—' }}</td>
                        <td class="text-right text-gray-600 font-mono">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                        <td class="text-right text-gray-800 font-bold font-mono">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                        <td class="text-center text-gray-600 font-mono">{{ $item->expired_date->format('d M Y') }}</td>
                        <td class="text-center text-gray-600">{{ $item->barang->satuan->nama ?? '—' }}</td>
                        <td class="text-right font-mono font-semibold text-gray-800">Rp {{ number_format($item->harga_beli * $item->jumlah, 0, ',', '.') }}</td>
                        <td class="table-num font-bold text-gray-700">{{ $item->jumlah }}</td>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex justify-end border-t border-gray-200 px-4 py-4">
            <div class="text-right">
                <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">
                    Total Faktur
                </div>
                <div class="text-xl font-bold font-mono text-gray-800">
                    Rp {{ number_format($penerimaan->totalFaktur(), 0, ',', '.') }}
                </div>
            </div>
        </div>

    </div>
</div>
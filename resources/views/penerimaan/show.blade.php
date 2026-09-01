@extends('layouts.app')
@section('title', 'Detail Penerimaan')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1>Detail Penerimaan Faktur</h1>
        <p class="text-caption mt-1">Rincian masuk barang untuk Faktur: <span class="font-mono font-bold text-gray-800">{{ $penerimaan->no_faktur }}</span></p>
    </div>
    <a href="{{ route('penerimaan.index') }}" class="btn-secondary py-2 px-4">
        &larr; Kembali
    </a>
</div>

<!-- Info Cards Grid -->
<div class="card-base p-6 mb-6 grid grid-cols-2 md:grid-cols-4 gap-6 text-xs">
    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1 font-sans">Tanggal Terima</span>
        <strong class="text-gray-800 text-sm font-sans">{{ $penerimaan->tanggal->format('d M Y') }}</strong>
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
                    <th scope="col" class="text-right w-32">Harga Beli</th>
                    <th scope="col" class="text-right w-32">Harga Jual</th>
                    <th scope="col" class="text-center w-36">Expired Date</th>
                    <th scope="col" class="text-right w-24">Diterima</th>
                    <th scope="col" class="text-right w-24">Sisa Stok</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-y divide-gray-150">
                @foreach ($penerimaan->detail as $index => $item)
                    <tr>
                        <td class="table-num">{{ $index + 1 }}</td>
                        <td class="font-medium text-gray-800">{{ $item->barang->nama ?? '—' }}</td>
                        <td class="font-mono text-gray-600">{{ $item->no_batch }}</td>
                        <td class="text-right text-gray-600 font-mono">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                        <td class="text-right text-gray-800 font-bold font-mono">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                        <td class="text-center text-gray-600 font-mono">{{ $item->expired_date->format('d M Y') }}</td>
                        <td class="table-num font-bold text-gray-700">{{ $item->jumlah }}</td>
                        <td class="table-num font-bold text-gray-700">
                            @if ($item->stok <= 0)
                                <span class="text-red-600">0</span>
                            @else
                                <span>{{ $item->stok }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

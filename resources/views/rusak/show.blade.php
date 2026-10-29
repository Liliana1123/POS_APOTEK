@extends('layouts.app')

@section('title', 'Detail Barang Rusak')

@section('content')

<x-page-header
    title="Detail Barang Rusak"
    subtitle="Informasi pencatatan barang rusak."
    :back-url="route('rusak.index')"
/>

<!-- Info Cards -->
<div class="card-base p-6 mb-6 grid grid-cols-2 md:grid-cols-3 gap-6 text-xs">

    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1 font-sans">
            Tanggal Lapor
        </span>
        <strong class="text-gray-800 text-sm font-sans">
            @tanggalId($rusak->tanggal, 'd F Y')
        </strong>
    </div>

    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1 font-sans">
            Nama Barang
        </span>
        <strong class="text-gray-800 text-sm font-sans">
            {{ $rusak->detailPenerimaan->barang->nama ?? '—' }}
        </strong>
    </div>

    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1 font-sans">
            No. Batch
        </span>
        <strong class="text-gray-800 text-sm font-mono">
            {{ $rusak->detailPenerimaan->no_batch ?? '—' }}
        </strong>
    </div>

    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1 font-sans">
            Expired Date
        </span>
        <strong class="text-gray-800 text-sm font-mono">
            @tanggalId($rusak->detailPenerimaan->expired_date, 'd F Y')
        </strong>
    </div>

    <div>
        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1 font-sans">
            Jumlah Rusak
        </span>
        <strong class="text-red-600 text-sm font-bold font-mono">
            {{ $rusak->jumlah }}
        </strong>
    </div>

</div>

<!-- Keterangan -->
<div class="card-base p-6">
    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2 font-sans">
        Keterangan
    </span>

    <p class="text-sm text-gray-700">
        {{ $rusak->keterangan ?? '—' }}
    </p>
</div>

@endsection
@extends('layouts.app')
@section('title', 'Catat Barang Rusak')

@section('content')
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">Catat Barang Rusak Baru</h1>
    <p class="text-xs text-gray-500 mt-0.5">Laporkan obat yang rusak, pecah, atau kadaluarsa untuk dikurangi dari stok batch.</p>
</div>

<form action="{{ route('rusak.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-150 p-6 max-w-lg space-y-4">
    @csrf

    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Batch Barang / Obat</label>
        <select name="detail_penerimaan_id" required class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-xs focus:ring-blue-500">
            <option value="">Pilih Batch</option>
            @foreach ($batches as $batch)
                <option value="{{ $batch->id }}" @selected(old('detail_penerimaan_id') == $batch->id)>
                    {{ $batch->barang->nama }} — Batch {{ $batch->no_batch }} (Sisa Stok: {{ $batch->stok }}, ED: {{ $batch->expired_date->format('d M Y') }})
                </option>
            @endforeach
        </select>
        @error('detail_penerimaan_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Tanggal Lapor</label>
            <input type="date" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required
                class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-xs focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Jumlah Rusak</label>
            <input type="number" name="jumlah" min="1" value="{{ old('jumlah') }}" required
                class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-xs focus:ring-blue-500">
            @error('jumlah') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Keterangan (Opsional)</label>
        <textarea name="keterangan" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-xs focus:ring-blue-500">{{ old('keterangan') }}</textarea>
        @error('keterangan') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="flex gap-2 pt-2">
        <button type="submit" class="bg-blue-600 text-white text-xs px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 shadow-sm transition-colors">Simpan</button>
        <a href="{{ route('rusak.index') }}" class="bg-gray-100 text-gray-700 text-xs px-4 py-2 rounded-lg font-semibold hover:bg-gray-200 transition-colors">Batal</a>
    </div>
</form>
@endsection

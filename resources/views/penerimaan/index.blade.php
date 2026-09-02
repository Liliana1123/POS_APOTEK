@extends('layouts.app')
@section('title', 'Penerimaan Barang')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <h1>Daftar Penerimaan Barang</h1>
        <p class="text-caption mt-1">Kelola data faktur obat masuk, supplier, dan status pembayaran.</p>
    </div>
    <a href="{{ route('penerimaan.create') }}" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Faktur Penerimaan Baru</span>
    </a>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('penerimaan.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">No. Faktur</label>
                <div class="relative">
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari no. faktur..."
                        class="form-input pr-8 font-mono">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </span>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Supplier</label>
                <select name="supplier_id" class="form-input">
                    <option value="">Semua Supplier</option>
                    @foreach ($suppliers as $s)
                        <option value="{{ $s->id }}" @selected(request('supplier_id') == $s->id)>{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-input">
            </div>
        </div>
        
        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            @if(request()->anyFilled(['cari', 'supplier_id', 'tanggal']))
                <a href="{{ route('penerimaan.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                    Reset
                </a>
            @endif
            <button type="submit" class="btn-primary py-1.5 px-4">
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Table Custom Wrapper -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[50rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="w-16">No</th>
                    <th scope="col">No. Faktur</th>
                    <th scope="col">Tanggal Terima</th>
                    <th scope="col">Supplier</th>
                    <th scope="col">Dicatat Oleh</th>
                    <th scope="col" class="text-center w-28">Status</th>
                    <th scope="col" class="text-right w-44">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-custom-body">
                @forelse ($penerimaans as $index => $penerimaan)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="table-num">{{ $penerimaans->firstItem() + $index }}</td>
                        <td class="font-semibold text-gray-800 font-mono">{{ $penerimaan->no_faktur }}</td>
                        <td class="text-gray-600">{{ $penerimaan->tanggal->format('d M Y') }}</td>
                        <td class="font-medium text-gray-800">{{ $penerimaan->supplier->nama ?? '—' }}</td>
                        <td class="text-gray-600">{{ $penerimaan->user->name ?? '—' }}</td>
                        <td class="text-center">
                            @if ($penerimaan->lunas)
                                <span class="badge-success">Lunas</span>
                            @else
                                <span class="badge-warning">Belum Lunas</span>
                            @endif
                        </td>
                        <td class="text-right space-x-1.5">
                            <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('penerimaan.show', $penerimaan) }}" class="btn-secondary !p-1.5" title="Detail">
                                <x-heroicon-o-eye class="w-4 h-4" />
                            </a>
                            <form action="{{ route('penerimaan.destroy', $penerimaan) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-destructive !p-1.5" title="Hapus">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">
                                    @if(request()->anyFilled(['cari', 'supplier_id', 'tanggal']))
                                        Penerimaan Tidak Ditemukan
                                    @else
                                        Penerimaan Kosong
                                    @endif
                                </div>
                                <div class="empty-state-desc">
                                    @if(request()->anyFilled(['cari', 'supplier_id', 'tanggal']))
                                        Tidak ada faktur penerimaan yang cocok dengan filter kriteria Anda.
                                    @else
                                        Belum ada data faktur masuk terdaftar di sistem.
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $penerimaans->links() }}</div>
@endsection

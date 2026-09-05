@extends('layouts.app')
@section('title', 'Custom Discount')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1>Manajemen Custom Discount (Promo)</h1>
        <p class="text-caption mt-1">Kelola promo, periode aktif, dan cakupan obat/barang.</p>
    </div>
    <a href="{{ route('custom-discount.create') }}" class="btn-primary flex items-center gap-2 shrink-0">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Tambah Promo</span>
    </a>
</div>

@if (session('success'))
    <div class="alert-success p-3 mb-4">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert-danger p-3 mb-4">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('custom-discount.index') }}" class="flex flex-col md:flex-row gap-3">
        <div class="flex-1">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama promo..." class="form-input">
        </div>
        <div class="w-full md:w-48">
            <select name="status" class="form-input">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="belum_mulai" @selected(request('status') === 'belum_mulai')>Belum Mulai</option>
                <option value="berakhir" @selected(request('status') === 'berakhir')>Berakhir</option>
                <option value="nonaktif" @selected(request('status') === 'nonaktif')>Non-aktif</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-primary flex items-center gap-2">
                <x-heroicon-o-funnel class="w-4 h-4" />
                <span>Filter</span>
            </button>
            <a href="{{ route('custom-discount.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[55rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col">Nama Promo</th>
                    <th scope="col" class="w-24">Persentase</th>
                    <th scope="col" class="w-32">Mulai</th>
                    <th scope="col" class="w-32">Selesai</th>
                    <th scope="col" class="w-28">Cakupan</th>
                    <th scope="col" class="text-center w-28">Status</th>
                    <th scope="col" class="text-right w-64">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-y divide-gray-150">
                @forelse ($discounts as $index => $discount)
                    @php
                        $today = now()->format('Y-m-d');
                        $startDate = $discount->tanggal_mulai->format('Y-m-d');
                        $endDate = $discount->tanggal_selesai->format('Y-m-d');
                        
                        if (!$discount->aktif) {
                            $statusText = 'Non-aktif';
                            $statusClass = 'badge-neutral';
                        } elseif ($startDate > $today) {
                            $statusText = 'Belum Mulai';
                            $statusClass = 'badge-warning';
                        } elseif ($endDate < $today) {
                            $statusText = 'Berakhir';
                            $statusClass = 'badge-danger';
                        } else {
                            $statusText = 'Aktif';
                            $statusClass = 'badge-success';
                        }
                    @endphp
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="font-medium text-gray-800">{{ $discount->nama }}</td>
                        <td class="text-green-600 font-semibold font-mono">{{ $discount->persentase }}%</td>
                        <td class="text-gray-600">{{ $discount->tanggal_mulai->format('d M Y') }}</td>
                        <td class="text-gray-600">{{ $discount->tanggal_selesai->format('d M Y') }}</td>
                        <td>
                            <span class="badge-info uppercase tracking-wider text-[9px] font-bold">
                                {{ $discount->cakupan }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="{{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="text-right space-x-1">
                            <form action="{{ route('custom-discount.toggle', $discount) }}" method="POST" class="inline toggle-promo-form">
                                @csrf
                                <button type="submit" class="btn-secondary py-1 px-2.5 text-[10px] font-semibold">
                                    {{ $discount->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <a href="{{ route('custom-discount.edit', $discount) }}" class="btn-secondary !p-1.5" title="Edit">
                                <x-heroicon-o-pencil class="w-4 h-4" />
                            </a>
                            <form action="{{ route('custom-discount.destroy', $discount) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-destructive !p-1.5" title="Hapus">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">Promo Kosong</div>
                                <div class="empty-state-desc">Belum ada custom discount atau promo terdaftar di sistem.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $discounts->links() }}
</div>

<script>
document.querySelectorAll('.toggle-promo-form').forEach(form => {
    form.addEventListener('submit', function (e) {
        if (!confirm('Yakin ingin mengubah status keaktifan promo ini?')) {
            e.preventDefault();
        }
    });
});
</script>
@endsection

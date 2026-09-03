@extends('layouts.app')
@section('title', 'Pabrik')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1>Daftar Pabrik</h1>
        <p class="text-caption mt-1">Kelola data pabrikan produsen obat.</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <button type="button" id="btn-tambah-pabrik" class="btn-primary flex items-center gap-2">
            <x-heroicon-o-plus class="w-4 h-4" />
            <span>Tambah Pabrik</span>
        </button>
    </div>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('pabrik.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Cari Pabrik</label>
                <div class="relative">
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama pabrik..."
                        class="form-input pr-8">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </span>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            @if(request()->filled('cari'))
                <a href="{{ route('pabrik.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                    Reset
                </a>
            @endif
            <button type="submit" class="btn-primary !p-1.5" title="Filter">
                <x-heroicon-o-funnel class="w-4 h-4" />
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
                    <th scope="col" class="text-center w-36">Aksi</th>
                    <th scope="col" class="w-16">ID</th>
                    <th scope="col">Nama Pabrik</th>
                    <th scope="col" class="w-40">Telepon</th>
                    <th scope="col">Alamat</th>
                    <th scope="col" class="w-40">PIC</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($pabriks as $index => $pabrik)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left">
                            <div class="flex items-center justify-start gap-1">
                                <button type="button"
                                    class="btn-secondary !p-1.5 btn-edit-pabrik"
                                    style="color: #F59E0B;"
                                    title="Edit"
                                    data-id="{{ $pabrik->id }}"
                                    data-json="{{ json_encode(['nama' => $pabrik->nama, 'telepon' => $pabrik->telepon, 'alamat' => $pabrik->alamat, 'pic' => $pabrik->pic], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) }}">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </button>
                                <form action="{{ route('pabrik.destroy', $pabrik) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn-secondary !p-1.5"
                                        style="color: #DC2626;"
                                        title="Hapus"
                                        aria-label="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus pabrik ini?')">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="table-num">{{ $pabrik->id }}</td>
                        <td class="font-medium text-gray-800">{{ $pabrik->nama }}</td>
                        <td class="text-gray-600">{{ $pabrik->telepon ?? '—' }}</td>
                        <td class="text-gray-600 truncate max-w-xs" title="{{ $pabrik->alamat }}">{{ $pabrik->alamat ?? '—' }}</td>
                        <td class="text-gray-600">{{ $pabrik->pic ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">
                                    @if(request()->filled('cari'))
                                        Pabrik Tidak Ditemukan
                                    @else
                                        Pabrik Kosong
                                    @endif
                                </div>
                                <div class="empty-state-desc">
                                    @if(request()->filled('cari'))
                                        Tidak ada pabrik yang cocok dengan kata kunci "{{ request('cari') }}".
                                    @else
                                        Belum ada data pabrik terdaftar di sistem.
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

<div class="mt-4">{{ $pabriks->links() }}</div>

<x-modal-form
    id="modal-pabrik"
    create-title="Tambah Pabrik"
    edit-title="Edit Pabrik"
    create-url="{{ route('pabrik.store') }}"
    update-base="{{ url('pabrik') }}"
    create-btn="#btn-tambah-pabrik"
    edit-btn=".btn-edit-pabrik">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Nama Pabrik <span class="text-red-500">*</span></label>
        <input type="text" name="nama" required class="form-input" placeholder="Masukkan nama pabrik...">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="nama"></p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">No Telepon</label>
        <input type="text" name="telepon" class="form-input" placeholder="08xxxxxxxxxx">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="telepon"></p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Alamat</label>
        <textarea name="alamat" rows="3" class="form-input" placeholder="Alamat lengkap pabrik..."></textarea>
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="alamat"></p>
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">PIC (Person In Charge)</label>
        <input type="text" name="pic" class="form-input" placeholder="Nama penanggung jawab pabrik...">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="pic"></p>
    </div>
</x-modal-form>
@endsection

@extends('layouts.app')
@section('title', 'Pabrik')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <h1>Daftar Pabrik</h1>
        <p class="text-caption mt-1">Kelola data pabrikan produsen obat.</p>
    </div>
    <button type="button" id="btn-tambah-pabrik" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Tambah Pabrik</span>
    </button>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('pabrik.index') }}" class="flex flex-wrap gap-2 items-center">
        <div class="relative shrink-0 w-full sm:w-64">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari pabrik..."
                class="form-input pr-8">
            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                <x-heroicon-o-magnifying-glass class="w-4 h-4" />
            </span>
        </div>
        <button type="submit" class="btn-primary py-1.5 px-4">
            Cari
        </button>
        @if(request()->filled('cari'))
            <a href="{{ route('pabrik.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                Clear
            </a>
        @endif
    </form>
</div>

<!-- Table Custom Wrapper -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[50rem]">
        <thead class="table-custom-header">
            <tr>
                <th scope="col" class="w-16">No</th>
                <th scope="col">Nama Pabrik</th>
                <th scope="col" class="text-right w-36">Aksi</th>
            </tr>
        </thead>
        <tbody class="table-custom-body">
            @forelse ($pabriks as $index => $pabrik)
                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                    <td class="table-num">{{ $pabriks->firstItem() + $index }}</td>
                    <td class="font-medium text-gray-800">{{ $pabrik->nama }}</td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-1">
<button type="button" class="btn-secondary !p-1.5 btn-edit-pabrik" title="Edit"
                        data-id="{{ $pabrik->id }}"
                        data-json="{{ json_encode(['nama' => $pabrik->nama], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) }}">
                                    <x-heroicon-o-pencil class="w-4 h-4" />
                                </button>
                                <form action="{{ route('pabrik.destroy', $pabrik) }}" method="POST">
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
                    <td colspan="3" class="p-0">
                        <div class="empty-state-container">
                            <div class="empty-state-title">
                                @if(request()->filled('cari'))
                                    Pencarian Tidak Ditemukan
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
</x-modal-form>
@endsection

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
                <th scope="col" class="w-32 text-center align-middle">Aksi</th>
                <th scope="col" class="w-28 text-center align-middle">No</th>
                <th scope="col" class="text-left align-middle">Nama Pabrik</th>
            </tr>
        </thead>
        <tbody class="table-custom-body">
            @forelse ($pabriks as $index => $pabrik)
                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                    <td class="w-32 text-center align-middle">
                        <div class="flex items-center justify-center gap-1">
                                <button type="button"
                                    class="btn-secondary action-icon-button action-icon-edit !p-1.5 btn-edit-satuan"
                                    style="color: #F59E0B;"
                                    title="Edit"
                                    data-id="{{ $pabrik->id }}"
                                    data-json="{{ json_encode(['nama' => $pabrik->nama], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-[4px] w-[4px]"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        style="width: 16px; height: 16px; flex-shrink: 0; display: block;">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m16.862 4.487 2.651 2.651M18.5 2.5a2.121 2.121 0 1 1 3 3L7.5 18.5l-4 1 1-4L18.5 2.5Z" />
                                    </svg>
                                </button>
                                <form action="{{ route('pabrik.destroy', $pabrik) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="btn-secondary action-icon-button action-icon-delete !p-1.5"
                                        style="color: #DC2626;"
                                        title="Hapus"
                                        aria-label="Hapus">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            style="width: 16px; height: 16px; flex-shrink: 0; display: block;">

                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 6V4h8v2"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l1 14h10l-1-14"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 11v6M14 11v6"/>

                                        </svg>
                                    </button>
                                </form>
                        </div>
                    </td>
                    <td class="w-28 text-center align-middle">{{ $pabriks->firstItem() + $index }}</td>
                    <td class="font-medium text-gray-800 text-left align-middle">{{ $pabrik->nama }}</td>
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

@extends('layouts.app')
@section('title', 'Satuan')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <h1>Daftar Satuan</h1>
        <p class="text-caption mt-1">Kelola tipe satuan kemasan barang/obat.</p>
    </div>
    <button type="button" id="btn-tambah-satuan" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Tambah Satuan</span>
    </button>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('satuan.index') }}" class="flex flex-wrap gap-2 items-center">
        <div class="relative shrink-0 w-full sm:w-64">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari satuan..."
                class="form-input pr-8">
            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                <x-heroicon-o-magnifying-glass class="w-4 h-4" />
            </span>
        </div>
        <button type="submit" class="btn-primary py-1.5 px-4">
            Cari
        </button>
        @if(request()->filled('cari'))
            <a href="{{ route('satuan.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
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
                <th scope="col">Nama Satuan</th>
                <th scope="col" class="text-right w-36">Aksi</th>
            </tr>
        </thead>
        <tbody class="table-custom-body">
            @forelse ($satuans as $index => $satuan)
                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                    <td class="table-num">{{ $satuans->firstItem() + $index }}</td>
                    <td class="font-medium text-gray-800">{{ $satuan->nama }}</td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-1">
                                    <button type="button"
                                    class="btn-secondary !p-1.5 btn-edit-satuan"
                                    style="color: #F59E0B;"
                                    title="Edit"
                                    data-id="{{ $satuan->id }}"
                                    data-json="{{ json_encode(['nama' => $satuan->nama], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) }}">

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
                                <form action="{{ route('satuan.destroy', $satuan) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="btn-secondary !p-1.5"
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
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="p-0">
                        <div class="empty-state-container">
                            <div class="empty-state-title">
                                @if(request()->filled('cari'))
                                    Pencarian Tidak Ditemukan
                                @else
                                    Satuan Kosong
                                @endif
                            </div>
                            <div class="empty-state-desc">
                                @if(request()->filled('cari'))
                                    Tidak ada satuan yang cocok dengan kata kunci "{{ request('cari') }}".
                                @else
                                    Belum ada data satuan terdaftar di sistem.
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

<div class="mt-4">{{ $satuans->links() }}</div>

<x-modal-form
    id="modal-satuan"
    create-title="Tambah Satuan"
    edit-title="Edit Satuan"
    create-url="{{ route('satuan.store') }}"
    update-base="{{ url('satuan') }}"
    create-btn="#btn-tambah-satuan"
    edit-btn=".btn-edit-satuan">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Nama Satuan <span class="text-red-500">*</span></label>
        <input type="text" name="nama" required class="form-input" placeholder="Masukkan nama satuan...">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="nama"></p>
    </div>
</x-modal-form>
@endsection

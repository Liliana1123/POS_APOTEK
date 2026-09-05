@extends('layouts.app')
@section('title', 'Kategori')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <h1>Daftar Kategori</h1>
        <p class="text-caption mt-1">Kelola tipe penggolongan/kategori obat.</p>
    </div>
    <button type="button" id="btn-tambah-kategori" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Tambah Kategori</span>
    </button>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('kategori.index') }}" class="flex flex-wrap gap-2 items-center">
        <div class="relative shrink-0 w-full sm:w-64">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama kategori..."
                class="form-input pr-8">
            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                <x-heroicon-o-magnifying-glass class="w-4 h-4" />
            </span>
        </div>
        <button type="submit" class="btn-primary py-1.5 px-4">
            Cari
        </button>
        @if(request()->filled('cari'))
            <a href="{{ route('kategori.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
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
                    <th scope="col" class="text-center w-36">Aksi</th>
                    <th scope="col" class="w-16">ID</th>
                    <th scope="col">Nama Kategori</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($kategoris as $index => $kategori)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left">
                            <div class="flex items-center justify-start gap-1">
                                <button type="button"
                                    class="btn-secondary !p-1.5 btn-edit-kategori"
                                    style="color: #F59E0B;"
                                    title="Edit"
                                    data-id="{{ $kategori->id }}"
                                    data-json="{{ json_encode(['nama' => $kategori->nama], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) }}">

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
                                <form action="{{ route('kategori.destroy', $kategori) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn-secondary !p-1.5"
                                        style="color: #DC2626;"
                                        title="Hapus"
                                        aria-label="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="table-num">{{ $kategori->id }}</td>
                        <td class="font-medium text-gray-800">{{ $kategori->nama }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">
                                    @if(request()->filled('cari'))
                                        Kategori Tidak Ditemukan
                                    @else
                                        Kategori Kosong
                                    @endif
                                </div>
                                <div class="empty-state-desc">
                                    @if(request()->filled('cari'))
                                        Tidak ada kategori yang cocok dengan kata kunci "{{ request('cari') }}".
                                    @else
                                        Belum ada data kategori terdaftar di sistem.
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

<div class="mt-4">{{ $kategoris->links() }}</div>

<x-modal-form
    id="modal-kategori"
    create-title="Tambah Kategori"
    edit-title="Edit Kategori"
    create-url="{{ route('kategori.store') }}"
    update-base="{{ url('kategori') }}"
    create-btn="#btn-tambah-kategori"
    edit-btn=".btn-edit-kategori">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1 font-sans">Nama Kategori <span class="text-red-500">*</span></label>
        <input type="text" name="nama" required class="form-input" placeholder="Masukkan nama kategori...">
        <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="nama"></p>
    </div>
</x-modal-form>
@endsection

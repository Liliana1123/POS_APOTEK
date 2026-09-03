@extends('layouts.app')
@section('title', 'Satuan')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1>Daftar Satuan</h1>
        <p class="text-caption mt-1">Kelola tipe satuan kemasan barang/obat.</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <button type="button" id="btn-tambah-satuan" class="btn-primary flex items-center gap-2">
            <x-heroicon-o-plus class="w-4 h-4" />
            <span>Tambah Satuan</span>
        </button>
    </div>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('satuan.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">Cari Satuan</label>
                <div class="relative">
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari satuan..."
                        class="form-input pr-8">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </span>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            @if(request()->filled('cari'))
                <a href="{{ route('satuan.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
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
                    <th scope="col" class="w-16">No</th>
                    <th scope="col">Nama Satuan</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($satuans as $index => $satuan)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left">
                            <div class="flex items-center justify-start gap-1">
                                <button type="button"
                                    class="btn-secondary !p-1.5 btn-edit-satuan"
                                    style="color: #F59E0B;"
                                    title="Edit"
                                    data-id="{{ $satuan->id }}"
                                    data-json="{{ json_encode(['nama' => $satuan->nama], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) }}">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </button>
                                <form action="{{ route('satuan.destroy', $satuan) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn-secondary action-icon-button action-icon-delete !p-1.5"
                                        style="color: #DC2626;"
                                        title="Hapus"
                                        aria-label="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus satuan ini?')">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="table-num">{{ $satuans->firstItem() + $index }}</td>
                        <td class="font-medium text-gray-800">{{ $satuan->nama }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">
                                    @if(request()->filled('cari'))
                                        Satuan Tidak Ditemukan
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

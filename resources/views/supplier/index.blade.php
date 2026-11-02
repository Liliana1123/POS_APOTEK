@extends('layouts.app')
@section('title', 'Supplier')

@section('content')
<x-page-header title="Daftar Supplier" subtitle="Kelola data penyalur/distributor obat.">
    @if(auth()->user()->canManage('master.supplier'))
        <button type="button" id="btn-tambah-supplier" class="btn-primary flex items-center gap-2">
            <x-heroicon-o-plus class="w-4 h-4" />
            <span>Tambah Supplier</span>
        </button>
    @endif
</x-page-header>

<x-card-filter
    :action="route('supplier.index')"
    :reset-url="route('supplier.index')"
    :live-search="true"
    :has-reset="request()->filled('cari')">
    <x-search-input name="cari" placeholder="Cari nama supplier..." class="shrink-0 w-full sm:w-[360px]" />
</x-card-filter>

<div class="table-custom-container">
    {{-- Desktop Table View --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="table-custom min-w-[50rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="text-left w-36 pl-3">Aksi</th>
                    <th scope="col" class="w-16">ID</th>
                    <th scope="col">Nama Supplier</th>
                    <th scope="col" class="w-44">Telepon</th>
                    <th scope="col">Alamat</th>
                    <th scope="col" class="w-40">PIC</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($suppliers as $index => $supplier)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="text-left pl-3">
                            <x-table-action
                                edit-class="{{ auth()->user()->canManage('master.supplier') ? 'btn-edit-supplier' : '' }}"
                                :edit-id="auth()->user()->canManage('master.supplier') ? $supplier->id : null"
                                :edit-data="auth()->user()->canManage('master.supplier') ? ['nama' => $supplier->nama, 'telepon' => $supplier->telepon, 'alamat' => $supplier->alamat, 'pic' => $supplier->pic] : null"
                                :delete-url="auth()->user()->canManage('master.supplier') ? route('supplier.destroy', $supplier) : null"
                                :delete-item="$supplier->nama"
                                delete-confirm="Yakin ingin menghapus supplier ini?"
                            />
                        </td>
                        <td class="table-num">{{ sprintf('%02d', $supplier->id) }}</td>
                        <td class="font-medium text-gray-800">{{ $supplier->nama }}</td>
                        <td class="text-gray-600 font-mono">{{ $supplier->telepon ?? '—' }}</td>
                        <td class="text-gray-600 truncate max-w-xs" title="{{ $supplier->alamat }}">{{ $supplier->alamat ?? '—' }}</td>
                        <td class="text-gray-600">{{ $supplier->pic ?? '—' }}</td>
                    </tr>
                @empty
                    <x-empty-state colspan="6" />
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Card View --}}
    <div class="md:hidden divide-y divide-gray-150 mobile-card-list">
        @forelse ($suppliers as $index => $supplier)
            <div class="mobile-card-item p-4 space-y-2.5 bg-white">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-gray-900 text-sm leading-snug break-words">
                            {{ $supplier->nama }}
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            PIC: <span class="text-gray-700 font-medium">{{ $supplier->pic ?? '—' }}</span>
                        </div>
                    </div>
                    <div class="shrink-0">
                        <span class="font-mono text-gray-400 text-xs bg-gray-100 px-2 py-0.5 rounded">ID #{{ sprintf('%02d', $supplier->id) }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-1.5 text-xs py-2 px-3 bg-gray-50/80 rounded-lg border border-gray-100">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Telepon:</span>
                        @if($supplier->telepon)
                            <a href="tel:{{ $supplier->telepon }}" class="font-mono text-blue-600 hover:underline font-medium">{{ $supplier->telepon }}</a>
                        @else
                            <span class="text-gray-400 font-mono">—</span>
                        @endif
                    </div>
                    <div class="pt-1 border-t border-gray-100">
                        <span class="text-[10px] uppercase font-bold text-gray-400 block tracking-wider">Alamat:</span>
                        <span class="text-gray-600 mt-0.5 block leading-relaxed">{{ $supplier->alamat ?: 'Tidak ada alamat tercatat.' }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-xs">
                    <span class="text-[11px] text-gray-400">Aksi Supplier</span>
                    <x-table-action
                        edit-class="{{ auth()->user()->canManage('master.supplier') ? 'btn-edit-supplier' : '' }}"
                        :edit-id="auth()->user()->canManage('master.supplier') ? $supplier->id : null"
                        :edit-data="auth()->user()->canManage('master.supplier') ? ['nama' => $supplier->nama, 'telepon' => $supplier->telepon, 'alamat' => $supplier->alamat, 'pic' => $supplier->pic] : null"
                        :delete-url="auth()->user()->canManage('master.supplier') ? route('supplier.destroy', $supplier) : null"
                        :delete-item="$supplier->nama"
                        delete-confirm="Yakin ingin menghapus supplier ini?"
                    />
                </div>
            </div>
        @empty
            <x-empty-state :as-card="true" />
        @endforelse
    </div>
</div>

<x-pagination :paginator="$suppliers" />

<x-modal-form
    id="modal-supplier"
    create-title="Tambah Supplier"
    edit-title="Edit Supplier"
    create-url="{{ route('supplier.store') }}"
    update-base="{{ url('supplier') }}"
    create-btn="#btn-tambah-supplier"
    edit-btn=".btn-edit-supplier">
    <x-form.input name="nama" label="Nama Supplier" required placeholder="Masukkan nama supplier..." />
    <x-form.input name="telepon" label="Telepon" placeholder="08xxxxxxxxxx" />
    <x-form.textarea name="alamat" label="Alamat" rows="3" placeholder="Alamat lengkap supplier..." />
    <x-form.input name="pic" label="PIC (Person In Charge)" placeholder="Nama penanggung jawab supplier..." />
</x-modal-form>
@endsection
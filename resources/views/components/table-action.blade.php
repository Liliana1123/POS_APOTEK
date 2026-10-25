@props([
    'editClass' => '',
    'editId' => null,
    'editData' => null,
    'editUrl' => null,
    'deleteUrl' => null,
    'deleteConfirm' => 'Yakin ingin menghapus data ini?',
    'deleteItem' => '',
    'showUrl' => null,
    'align' => 'justify-start',
])

@php
    $hasEdit = $editUrl || ($editClass && ($editId !== null || $editData !== null));
    $hasAnyAction = $showUrl || $hasEdit || $deleteUrl || !empty(trim((string)$slot));
@endphp

<div class="flex items-center {{ $align }} gap-1" data-table-action="true">
    @if(!$hasAnyAction)
        <span class="text-xs text-gray-400 font-mono">—</span>
    @else
        {{-- 1. Tombol Detail (Eye) --}}
        @if($showUrl)
            <a href="{{ $showUrl }}"
                class="btn-secondary !p-1.5 hover:bg-blue-50 hover:border-blue-300 transform hover:scale-110 active:scale-95 transition-all duration-150 hover:shadow-xs"
                style="color: #2563EB;"
                title="Lihat Detail"
                aria-label="Lihat Detail">
                <x-heroicon-o-eye class="w-4 h-4" />
            </a>
        @endif

        {{-- 2. Tombol Edit (Pencil) --}}
        @if($editUrl)
            <a href="{{ $editUrl }}"
                class="btn-secondary !p-1.5 hover:bg-amber-50 hover:border-amber-300 transform hover:scale-110 active:scale-95 transition-all duration-150 hover:shadow-xs"
                style="color: #F59E0B;"
                title="Edit"
                aria-label="Edit">
                <x-heroicon-o-pencil-square class="w-4 h-4" />
            </a>
        @elseif($editClass && ($editId !== null || $editData !== null))
            <button type="button"
                class="btn-secondary !p-1.5 hover:bg-amber-50 hover:border-amber-300 transform hover:scale-110 active:scale-95 transition-all duration-150 hover:shadow-xs {{ $editClass }}"
                style="color: #F59E0B;"
                title="Edit"
                aria-label="Edit"
                @if($editId !== null) data-id="{{ $editId }}" @endif
                @if($editData !== null) data-json="{{ is_array($editData) ? json_encode($editData, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) : $editData }}" @endif>
                <x-heroicon-o-pencil-square class="w-4 h-4" />
            </button>
        @endif

        {{-- 3. Aksi Kustom Tambahan (Slot) --}}
        {{ $slot }}

        {{-- 4. Tombol Hapus (Trash) --}}
        @if($deleteUrl)
            <button type="button"
                class="btn-secondary !p-1.5 hover:bg-red-50 hover:border-red-300 transform hover:scale-110 active:scale-95 transition-all duration-150 hover:shadow-xs cursor-pointer"
                style="color: #DC2626;"
                title="Hapus"
                aria-label="Hapus"
                data-confirm-delete="{{ $deleteUrl }}"
                @if($deleteItem) data-item-name="{{ $deleteItem }}" @endif
                @if($deleteConfirm) data-confirm-message="{{ $deleteConfirm }}" @endif>
                <x-heroicon-o-trash class="w-4 h-4" />
            </button>
        @endif
    @endif
</div>

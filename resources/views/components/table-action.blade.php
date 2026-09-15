@props([
    'editClass' => '',
    'editId' => null,
    'editData' => null,
    'editUrl' => null,
    'deleteUrl' => null,
    'deleteConfirm' => 'Yakin ingin menghapus data ini?',
    'showUrl' => null,
    'align' => 'justify-start',
])

<div class="flex items-center {{ $align }} gap-1">
    {{-- 1. Tombol Detail (Eye) --}}
    @if($showUrl)
        <a href="{{ $showUrl }}"
            class="btn-secondary !p-1.5 hover:bg-blue-50 hover:border-blue-300 transition-colors"
            style="color: #2563EB;"
            title="Lihat Detail"
            aria-label="Lihat Detail">
            <x-heroicon-o-eye class="w-4 h-4" />
        </a>
    @endif

    {{-- 2. Tombol Edit (Pencil) --}}
    @if($editUrl)
        <a href="{{ $editUrl }}"
            class="btn-secondary !p-1.5 hover:bg-amber-50 hover:border-amber-300 transition-colors"
            style="color: #F59E0B;"
            title="Edit"
            aria-label="Edit">
            <x-heroicon-o-pencil-square class="w-4 h-4" />
        </a>
    @elseif($editClass || $editData !== null)
        <button type="button"
            class="btn-secondary !p-1.5 hover:bg-amber-50 hover:border-amber-300 transition-colors {{ $editClass }}"
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
        <form action="{{ $deleteUrl }}" method="POST" onsubmit="return confirm('{{ $deleteConfirm }}')">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="btn-secondary !p-1.5 hover:bg-red-50 hover:border-red-300 transition-colors"
                style="color: #DC2626;"
                title="Hapus"
                aria-label="Hapus">
                <x-heroicon-o-trash class="w-4 h-4" />
            </button>
        </form>
    @endif
</div>

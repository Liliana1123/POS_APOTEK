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
    {{ $slot }}

    @if($showUrl)
        <a href="{{ $showUrl }}" class="btn-secondary !p-1.5 text-blue-600 hover:text-blue-700" title="Detail" aria-label="Detail">
            <x-heroicon-o-eye class="w-4 h-4" />
        </a>
    @endif

    @if($editUrl)
        <a href="{{ $editUrl }}" class="btn-secondary !p-1.5 text-amber-500 hover:text-amber-600" title="Edit" aria-label="Edit">
            <x-heroicon-o-pencil-square class="w-4 h-4" />
        </a>
    @elseif($editClass || $editData !== null)
        <button type="button"
            class="btn-secondary !p-1.5 text-amber-500 hover:text-amber-600 {{ $editClass }}"
            title="Edit"
            aria-label="Edit"
            @if($editId !== null) data-id="{{ $editId }}" @endif
            @if($editData !== null) data-json="{{ is_array($editData) ? json_encode($editData, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG) : $editData }}" @endif>
            <x-heroicon-o-pencil-square class="w-4 h-4" />
        </button>
    @endif

    @if($deleteUrl)
        <form action="{{ $deleteUrl }}" method="POST" onsubmit="return confirm('{{ $deleteConfirm }}')">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="btn-secondary !p-1.5 text-red-600 hover:text-red-700"
                title="Hapus"
                aria-label="Hapus">
                <x-heroicon-o-trash class="w-4 h-4" />
            </button>
        </form>
    @endif
</div>

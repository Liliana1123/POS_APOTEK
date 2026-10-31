@props([
    'id',
    'title' => null,
    'subtitle' => null,
    'size' => 'max-w-15xl modal-dialog-large',
    'closeId' => null,
    'closeAction' => null,
    'contentId' => null,
    'bodyClass' => 'modal-body-custom overflow-y-auto modal-body-scroll',
    'headerClass' => '',
])

<div id="{{ $id }}"
    class="modal-backdrop-custom hidden"
    aria-hidden="true"
    {{ $attributes->except(['id', 'class']) }}>

    <div class="modal-container-custom {{ $size }}">

        {{-- Header Modal --}}
        @if($title || $subtitle || isset($header))
            <div class="modal-header-custom {{ $headerClass }}">
                @if(isset($header))
                    {{ $header }}
                @else
                    <div>
                        <h2>{{ $title }}</h2>
                        @if($subtitle)
                            <p class="text-caption mt-1">
                                {{ $subtitle }}
                            </p>
                        @endif
                    </div>
                @endif

                <button type="button"
                    @if($closeId) id="{{ $closeId }}" @endif
                    @if($closeAction) onclick="{{ $closeAction }}" @endif
                    class="btn-secondary !p-1.5 modal-close-btn"
                    title="Tutup"
                    aria-label="Tutup">
                    <x-heroicon-o-x-mark class="w-4 h-4 pointer-events-none" />
                </button>
            </div>
        @endif

        {{-- Body Modal --}}
        <div @if($contentId) id="{{ $contentId }}" @endif class="{{ $bodyClass }}">
            {{ $slot }}
        </div>

        {{-- Footer Modal (Opsional) --}}
        @if(isset($footer))
            <div class="modal-footer-custom">
                {{ $footer }}
            </div>
        @endif

    </div>
</div>

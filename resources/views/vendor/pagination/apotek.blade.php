@if ($paginator->hasPages() || $paginator->total() > 0)
@php
    $current = $paginator->currentPage();
    $last = $paginator->lastPage();

    $range = [];
    $pages = [];
    $prev = null;

    for ($i = 1; $i <= $last; $i++) {
        if (
            $i == 1 ||
            $i == $last ||
            ($i >= $current - 1 && $i <= $current + 1) ||
            ($current <= 2 && $i <= 3) ||
            ($current >= $last - 1 && $i >= $last - 2)
        ) {
            $range[] = $i;
        }
    }

    foreach ($range as $i) {
        if ($prev !== null) {
            if ($i - $prev === 2) {
                $pages[] = $prev + 1;
            } elseif ($i - $prev !== 1) {
                $pages[] = '...';
            }
        }
        $pages[] = $i;
        $prev = $i;
    }
@endphp

<nav role="navigation" aria-label="Navigasi halaman">
    {{-- Tampilan Mobile: Ringkas (Sebelumnya, Halaman X / Y, Berikutnya) --}}
    <div class="flex items-center justify-between sm:hidden text-xs">
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center px-3 py-1.5 font-medium text-gray-400 bg-white border border-gray-300 rounded-lg shadow-xs cursor-not-allowed">
                Sebelumnya
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-3 py-1.5 font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-xs hover:bg-gray-50 hover:text-blue-600 transition">
                Sebelumnya
            </a>
        @endif

        <span class="text-xs text-gray-600 font-medium">
            Halaman <span class="text-gray-900 font-semibold">{{ $current }}</span> / {{ $last }}
        </span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-3 py-1.5 font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-xs hover:bg-gray-50 hover:text-blue-600 transition">
                Berikutnya
            </a>
        @else
            <span class="inline-flex items-center px-3 py-1.5 font-medium text-gray-400 bg-white border border-gray-300 rounded-lg shadow-xs cursor-not-allowed">
                Berikutnya
            </span>
        @endif
    </div>

    {{-- Tampilan Layar Sedang & Besar (Desktop / Laptop / Tablet) --}}
    <div class="hidden sm:flex sm:items-center sm:justify-between sm:gap-4">
        <div>
            <p class="text-xs text-gray-600 leading-5">
                Menampilkan <span class="font-medium text-gray-900">{{ $paginator->firstItem() ?? 0 }}</span>–<span class="font-medium text-gray-900">{{ $paginator->lastItem() ?? 0 }}</span> dari <span class="font-medium text-gray-900">{{ $paginator->total() }}</span> data
            </p>
        </div>

        <div>
            <span class="inline-flex rtl:flex-row-reverse shadow-xs rounded-lg overflow-hidden border border-gray-300 divide-x divide-gray-300 bg-white">
                {{-- Tombol Sebelumnya --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="Sebelumnya">
                        <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-300 bg-white cursor-not-allowed leading-5" aria-hidden="true" title="Halaman Pertama">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-600 bg-white leading-5 hover:text-blue-600 hover:bg-blue-50/50 transition duration-150" aria-label="Sebelumnya" title="Halaman Sebelumnya">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif

                {{-- Nomor-Nomor Halaman Ringkas --}}
                @foreach ($pages as $page)
                    @if ($page === '...')
                        <span aria-disabled="true">
                            <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-400 bg-white cursor-default leading-5 select-none">...</span>
                        </span>
                    @elseif ($page == $current)
                        <span aria-current="page">
                            <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 cursor-default leading-5">{{ $page }}</span>
                        </span>
                    @else
                        <a href="{{ $paginator->url($page) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-white leading-5 hover:text-blue-600 hover:bg-blue-50/60 transition duration-150" aria-label="Ke halaman {{ $page }}">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                {{-- Tombol Berikutnya --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-600 bg-white leading-5 hover:text-blue-600 hover:bg-blue-50/50 transition duration-150" aria-label="Berikutnya" title="Halaman Berikutnya">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="Berikutnya">
                        <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-300 bg-white cursor-not-allowed leading-5" aria-hidden="true" title="Halaman Terakhir">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </span>
                @endif
            </span>
        </div>
    </div>
</nav>
@endif

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-4 w-full">
        
        <div class="hidden sm:block">
            <p class="text-sm text-gray-500 font-medium">
                Menampilkan
                <span class="font-bold text-gray-900">{{ $paginator->firstItem() ?? 0 }}</span>
                hingga
                <span class="font-bold text-gray-900">{{ $paginator->lastItem() ?? 0 }}</span>
                dari
                <span class="font-bold text-gray-900">{{ $paginator->total() }}</span>
                hasil
            </p>
        </div>

        <div class="flex items-center gap-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-gray-400 cursor-not-allowed">
                    <i class="ph ph-caret-left font-bold"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-primary-50 hover:text-primary-600 hover:border-primary-200 transition-colors">
                    <i class="ph ph-caret-left font-bold"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            <div class="hidden sm:flex items-center gap-1">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="w-9 h-9 flex items-center justify-center text-gray-500 font-medium">...</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="w-9 h-9 flex items-center justify-center rounded-lg border-2 border-primary-600 bg-primary-50 text-primary-700 font-bold">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-primary-50 hover:text-primary-600 hover:border-primary-200 transition-colors font-medium">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-primary-50 hover:text-primary-600 hover:border-primary-200 transition-colors">
                    <i class="ph ph-caret-right font-bold"></i>
                </a>
            @else
                <span class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-gray-400 cursor-not-allowed">
                    <i class="ph ph-caret-right font-bold"></i>
                </span>
            @endif
        </div>
        
    </nav>
@endif

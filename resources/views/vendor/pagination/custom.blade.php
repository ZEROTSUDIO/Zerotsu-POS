@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-12">
        <div class="flex gap-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="px-4 py-2 border border-[#E8E3D8] text-[#B5A693] cursor-not-allowed text-xs tracking-[2px]"
                    aria-disabled="true" aria-label="@lang('pagination.previous')">
                    &lsaquo;
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="px-4 py-2 border border-[#B5A693] text-[#6B5E52] hover:bg-[#3A352F] hover:text-white transition-colors duration-300 text-xs tracking-[2px]"
                    aria-label="@lang('pagination.previous')">
                    &lsaquo;
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-4 py-2 border border-transparent text-[#B5A693] text-xs tracking-[2px]"
                        aria-disabled="true">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span
                                class="px-4 py-2 border border-[#3A352F] bg-[#3A352F] text-white text-xs tracking-[2px]"
                                aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}#products"
                                class="px-4 py-2 border border-[#B5A693] text-[#6B5E52] hover:bg-[#3A352F] hover:text-white transition-colors duration-300 text-xs tracking-[2px]">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}#products" rel="next"
                    class="px-4 py-2 border border-[#B5A693] text-[#6B5E52] hover:bg-[#3A352F] hover:text-white transition-colors duration-300 text-xs tracking-[2px]"
                    aria-label="@lang('pagination.next')">
                    &rsaquo;
                </a>
            @else
                <span class="px-4 py-2 border border-[#E8E3D8] text-[#B5A693] cursor-not-allowed text-xs tracking-[2px]"
                    aria-disabled="true" aria-label="@lang('pagination.next')">
                    &rsaquo;
                </span>
            @endif
        </div>
    </nav>
@endif

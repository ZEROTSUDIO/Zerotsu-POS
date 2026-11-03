<!-- Pagination -->
@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        // Calculate the range of pages to show (max 5 pages)
        $start = max(1, $currentPage - 2);
        $end = min($lastPage, $currentPage + 2);

        // Adjust if we're near the beginning or end
        if ($currentPage <= 3) {
            $end = min($lastPage, 5);
        }
        if ($currentPage > $lastPage - 3) {
            $start = max(1, $lastPage - 4);
        }
    @endphp

    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mt-6">
        <div class="text-sm text-gray-600 w-full md:w-auto text-center md:text-left">
            Showing
            <span class="font-semibold text-gray-900">{{ $paginator->firstItem() }}-{{ $paginator->lastItem() }}</span>
            of
            <span class="font-semibold text-gray-900">{{ $paginator->total() }}</span>
            results
        </div>
        <div class="flex flex-wrap justify-center gap-2">
            {{-- Previous Button --}}
            @if ($paginator->onFirstPage())
                <button disabled
                    class="px-3 py-2 rounded-lg border-2 border-gray-200 text-gray-400 cursor-not-allowed transition-all duration-300">
                    <i class="fas fa-chevron-left"></i>
                </button>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled"
                    class="px-3 py-2 rounded-lg border-2 border-gray-200 text-slate-700 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-300">
                    <i class="fas fa-chevron-left"></i>
                </button>
            @endif

            {{-- First page + dots if needed --}}
            @if ($start > 1)
                <button wire:click="gotoPage(1)"
                    class="px-4 py-2 rounded-lg border-2 border-gray-200 text-slate-700 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-300">
                    1
                </button>
                @if ($start > 2)
                    <span class="px-2 py-2 text-gray-400">...</span>
                @endif
            @endif

            {{-- Page Numbers (limited to 5) --}}
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $currentPage)
                    <button wire:key="page-{{ $page }}"
                        class="px-4 py-2 rounded-lg gradient-gold text-white font-semibold">
                        {{ $page }}
                    </button>
                @else
                    <button wire:click="gotoPage({{ $page }})" wire:key="page-{{ $page }}"
                        class="px-4 py-2 rounded-lg border-2 border-gray-200 text-slate-700 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-300">
                        {{ $page }}
                    </button>
                @endif
            @endfor

            {{-- Last page + dots if needed --}}
            @if ($end < $lastPage)
                @if ($end < $lastPage - 1)
                    <span class="px-2 py-2 text-gray-400">...</span>
                @endif
                <button wire:click="gotoPage({{ $lastPage }})"
                    class="px-4 py-2 rounded-lg border-2 border-gray-200 text-slate-700 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-300">
                    {{ $lastPage }}
                </button>
            @endif

            {{-- Next Button --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled"
                    class="px-3 py-2 rounded-lg border-2 border-gray-200 text-slate-700 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-300">
                    <i class="fas fa-chevron-right"></i>
                </button>
            @else
                <button disabled
                    class="px-3 py-2 rounded-lg border-2 border-gray-200 text-gray-400 cursor-not-allowed transition-all duration-300">
                    <i class="fas fa-chevron-right"></i>
                </button>
            @endif
        </div>
        <select wire:model.live="perPage"
            class="px-4 py-2 rounded-lg border-2 border-gray-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all w-full md:w-auto">
            <option value="8">8 per page</option>
            <option value="15">15 per page</option>
            <option value="25">25 per page</option>
        </select>
    </div>
@endif

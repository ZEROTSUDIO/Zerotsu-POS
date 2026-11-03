<div class="flex-1 flex flex-col md:flex-row overflow-hidden relative">

    <!-- ══════════════════════════════════════════════════════════
         LEFT PANEL: PRODUCT CATALOG VIEWPORT (65% on Desktop)
         ══════════════════════════════════════════════════════════ -->
    <section class="flex-1 flex flex-col min-w-0 bg-[#F4F1FA] border-r-2 border-[#0A090D] overflow-hidden">
        
        <!-- Search & Filter Header Strip -->
        <div class="p-3 bg-white border-b-2 border-[#0A090D] flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 flex-shrink-0">
            <!-- Search Bar (Targeted by F2 Shortcut, Real-Time Debounce) -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#8C849B]">
                    <svg class="w-4 h-4 text-[#0A090D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="pos-product-search"
                    wire:model.live.debounce.250ms="search"
                    placeholder="Search menu or SKU... (Press F2)"
                    class="input-brutal w-full pl-9 pr-14 text-xs font-mono font-bold"
                    autocomplete="off"
                >
                <div class="absolute inset-y-0 right-0 pr-2 flex items-center gap-1">
                    @if (strlen($search) > 0)
                        <button 
                            type="button" 
                            wire:click="clearSearch"
                            class="w-5 h-5 flex items-center justify-center bg-[#0A090D] hover:bg-[#7C3AED] text-white text-[10px] font-mono font-bold"
                            title="Clear search"
                        >
                            ✕
                        </button>
                    @endif
                    <span class="hotkey-pill !text-[9px] !py-0.5 !px-1.5">[F2]</span>
                </div>
            </div>

            <!-- Telemetry Stats Badge -->
            <div class="flex items-center gap-2 flex-shrink-0">
                <div class="tag-block tag-dark !text-[10px] !py-1.5 !px-2.5">
                    <span class="text-[#C06BF0] font-bold">{{ count($products) }}</span> 
                    {{ strlen($search) > 0 ? 'MATCHED' : 'ITEMS READY' }}
                </div>
                <div class="tag-block tag-outline !text-[10px] !py-1.5 !px-2.5 bg-white">
                    <span class="font-bold">{{ $categories->count() }}</span> CATEGORIES
                </div>
            </div>
        </div>

        <!-- Dynamic Category Navigation Rail -->
        <div class="px-3 py-2 bg-[#FFFFFF] border-b-2 border-[#0A090D] flex items-center gap-2 overflow-x-auto flex-shrink-0 scrollbar-none">
            <!-- All Items Filter Button -->
            <button 
                type="button" 
                wire:click="selectCategory(null)"
                class="{{ $selectedCategory === null 
                    ? 'btn-solid !bg-[#7C3AED] text-white shadow-[3px_3px_0_#0A090D]' 
                    : 'btn-outline bg-white hover:bg-[#F4F1FA] text-[#0A090D] shadow-[2px_2px_0_#0A090D]' }} 
                    !py-1.5 !px-3 !text-[11px] font-mono font-bold flex items-center gap-1.5 transition-transform"
            >
                <span>ALL ITEMS</span>
                <span class="{{ $selectedCategory === null ? 'bg-[#5B1AA0] text-white' : 'bg-[#F4F1FA] text-[#0A090D]' }} px-1.5 py-0.2 text-[9px] font-mono font-bold">
                    {{ $totalActiveProducts }}
                </span>
            </button>

            <!-- Dynamic Category List -->
            @foreach ($categories as $cat)
                <button 
                    type="button" 
                    wire:click="selectCategory({{ $cat->id }})"
                    class="{{ $selectedCategory === $cat->id 
                        ? 'btn-solid !bg-[#7C3AED] text-white shadow-[3px_3px_0_#0A090D]' 
                        : 'btn-outline bg-white hover:bg-[#F4F1FA] text-[#0A090D] shadow-[2px_2px_0_#0A090D]' }} 
                        !py-1.5 !px-3 !text-[11px] font-mono font-bold flex items-center gap-1.5 transition-transform"
                >
                    <span class="uppercase">{{ $cat->name }}</span>
                    <span class="{{ $selectedCategory === $cat->id ? 'bg-[#5B1AA0] text-white' : 'bg-[#F4F1FA] text-[#0A090D]' }} px-1.5 py-0.2 text-[9px] font-mono font-bold">
                        {{ $cat->products_count }}
                    </span>
                </button>
            @endforeach
        </div>

        <!-- Stock / Validation Feedback Banner -->
        @if ($feedbackMessage)
            <div class="mx-4 mt-3 p-2.5 bg-[#FFF2F2] border-2 border-[#0A090D] shadow-[3px_3px_0_#0A090D] flex items-center justify-between text-xs font-mono text-[#0A090D]">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#EF4444]"></span>
                    <span><strong>ALERT:</strong> {{ $feedbackMessage }}</span>
                </div>
                <button 
                    type="button" 
                    wire:click="$set('feedbackMessage', null)" 
                    class="font-bold text-xs hover:text-red-700 ml-2"
                >
                    [DISMISS]
                </button>
            </div>
        @endif

        <!-- Catalog Product Grid Container (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-4 min-h-0">

            @if ($products->isEmpty())
                <!-- Zero State: No Products Matching Search or Category -->
                <div class="panel-offset p-8 bg-white text-center max-w-md mx-auto my-12">
                    <div class="w-14 h-14 border-2 border-dashed border-[#0A090D] mx-auto flex items-center justify-center mb-3 text-[#0A090D]">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-display text-lg tracking-wide uppercase text-[#0A090D]">
                        No Items Found
                    </h3>
                    <p class="font-body text-xs text-[#4B4556] mt-1 mb-4">
                        @if (strlen($search) > 0)
                            No menu item matches "<strong>{{ $search }}</strong>" in <span class="font-bold">{{ $activeCategoryName }}</span>.
                        @else
                            No products are currently available in this category.
                        @endif
                    </p>
                    <div class="flex items-center justify-center gap-2">
                        @if (strlen($search) > 0)
                            <button 
                                type="button" 
                                wire:click="clearSearch"
                                class="btn-solid !py-1.5 !px-3 !text-[11px] shadow-[2px_2px_0_#0A090D]"
                            >
                                Clear Search Filter
                            </button>
                        @endif
                        @if ($selectedCategory !== null)
                            <button 
                                type="button" 
                                wire:click="selectCategory(null)"
                                class="btn-outline !py-1.5 !px-3 !text-[11px] shadow-[2px_2px_0_#0A090D]"
                            >
                                View All Items
                            </button>
                        @endif
                    </div>
                </div>
            @else
                <!-- Active Product Grid (Neobrutalist Cards with Snap-into-Focus Tilt) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3.5 pb-6">
                    @foreach ($products as $product)
                        @php
                            $isOutOfStock = $product->stock <= 0;
                            $isLowStock = $product->isLowStock() && !$isOutOfStock;
                            $tiltAngle = ($loop->iteration % 2 === 0) ? '1.2deg' : '-1.2deg';
                        @endphp

                        <div 
                            style="--r: {{ $tiltAngle }};"
                            @if (!$isOutOfStock)
                                wire:click="addToCart({{ $product->id }})"
                            @endif
                            class="card-tilty p-3 flex flex-col justify-between select-none
                                {{ $isOutOfStock 
                                    ? 'opacity-65 grayscale cursor-not-allowed bg-[#E5E5E5]' 
                                    : 'cursor-pointer hover:bg-white bg-white active:translate-y-0 active:shadow-[2px_2px_0_#0A090D]' }}"
                        >
                            <!-- Out of Stock Slanted Washi Tape Badge -->
                            @if ($isOutOfStock)
                                <div class="badge-tape badge-tape-tr !bg-[#FF5C5C] !text-[#0A090D] !text-[9px] !py-0.5 !px-2">
                                    SOLD OUT
                                </div>
                            @endif

                            <div>
                                <!-- Top Metadata Strip: Category & SKU -->
                                <div class="flex items-center justify-between gap-1 mb-2">
                                    <span class="font-mono text-[9px] text-[#7C3AED] font-bold uppercase truncate">
                                        {{ $product->category?->name ?? 'General' }}
                                    </span>
                                    <span class="font-mono text-[9px] text-[#8C849B] font-bold">
                                        {{ $product->sku }}
                                    </span>
                                </div>

                                <!-- Architectural Character Art Box / Icon Box -->
                                <div class="h-24 bg-[#F4F1FA] border border-[#0A090D] mb-2.5 flex items-center justify-center relative overflow-hidden group">
                                    <!-- Halftone Background Texture -->
                                    <div class="absolute inset-0 halftone opacity-30"></div>
                                    
                                    <!-- Thematic Category SVG Glyph -->
                                    <div class="relative z-10 w-12 h-12 flex items-center justify-center text-[#0A090D] group-hover:scale-110 transition-transform">
                                        @if (str_contains(strtolower($product->category?->slug ?? ''), 'kopi'))
                                            <!-- Coffee Mug Glyph -->
                                            <svg class="w-8 h-8 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="1.8" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zM6 1v3M10 1v3M14 1v3"/>
                                            </svg>
                                        @elseif (str_contains(strtolower($product->category?->slug ?? ''), 'snack') || str_contains(strtolower($product->category?->slug ?? ''), 'pastry'))
                                            <!-- Pastry / Cookie Glyph -->
                                            <svg class="w-8 h-8 text-[#C06BF0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="1.8" d="M12 2a10 10 0 100 20 10 10 0 000-20zm-3 7a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm6 3a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm-4 5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                                            </svg>
                                        @else
                                            <!-- Beverage / Cold Drink Glyph -->
                                            <svg class="w-8 h-8 text-[#5B1AA0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                        @endif
                                    </div>

                                    <!-- Quick "+ Add" Overlay Hint on Hover -->
                                    @if (!$isOutOfStock)
                                        <div class="absolute inset-0 bg-[#0A090D] bg-opacity-80 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                            <span class="badge-lilac !text-[10px] !py-1 !px-2.5">
                                                + ADD TO ORDER
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Title -->
                                <h4 class="font-display text-sm tracking-wide text-[#0A090D] uppercase leading-snug line-clamp-2" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </h4>
                            </div>

                            <!-- Bottom Telemetry: Price & Live Stock Chip -->
                            <div class="mt-3 pt-2 border-t border-[#0A090D] flex items-center justify-between gap-1">
                                <span class="font-mono text-xs font-bold text-[#0A090D] tracking-tight">
                                    {{ $product->formatted_price }}
                                </span>

                                @if ($isOutOfStock)
                                    <span class="badge-red !text-[9px] !py-0.5 !px-1.5 font-mono">
                                        EMPTY
                                    </span>
                                @elseif ($isLowStock)
                                    <span class="badge-lilac !text-[9px] !py-0.5 !px-1.5 font-mono" title="Minimum Stock: {{ $product->minimum_stock }}">
                                        LOW: {{ $product->stock }}
                                    </span>
                                @else
                                    <span class="badge-dark !text-[9px] !py-0.5 !px-1.5 font-mono">
                                        STOCK: {{ $product->stock }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

        <!-- Terminal Status Footer Bar -->
        <div class="p-2 bg-[#0A0812] text-white border-t-2 border-[#0A090D] flex items-center justify-between px-4 text-[10px] font-mono flex-shrink-0">
            <div class="flex items-center gap-3">
                <span class="text-[#DDD6FE]">SYS: <strong class="text-[#C06BF0]">LARAVEL 12 + LIVEWIRE 3.7</strong></span>
                <span class="text-[#38225C]">|</span>
                <span class="text-[#DDD6FE]">CATEGORY: <strong class="text-white">{{ $activeCategoryName }}</strong></span>
            </div>
            <div class="text-[#DDD6FE] flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#10B981]"></span>
                <span>GATEWAY: <strong class="text-[#C06BF0]">MIDTRANS SANDBOX</strong></span>
            </div>
        </div>
    </section>


    <!-- ══════════════════════════════════════════════════════════
         RIGHT PANEL: CART ORDER SUMMARY DRAWER (35% on Desktop)
         ══════════════════════════════════════════════════════════ -->
    <aside class="w-full md:w-[380px] lg:w-[420px] bg-white flex flex-col flex-shrink-0 overflow-hidden z-10 shadow-[-4px_0_0_#0A090D]">
        
        <!-- Cart Dossier Title Bar -->
        <div class="title-bar !py-2.5 !px-3.5 flex-shrink-0">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-[#C06BF0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span class="tracking-widest">ORDER LEDGER</span>
            </div>
            <span class="badge-lilac !text-[9px] !py-0.5 !px-1.5">
                {{ count($cart) }} {{ count($cart) === 1 ? 'ITEM' : 'ITEMS' }}
            </span>
        </div>

        <!-- Customer Identity Bar -->
        <div class="p-2.5 bg-[#F4F1FA] border-b-2 border-[#0A090D] flex items-center justify-between text-xs font-mono">
            <div class="flex items-center gap-2">
                <span class="text-[#8C849B]">CUSTOMER:</span>
                <span class="font-bold text-[#0A090D]">Walk-in Guest</span>
            </div>
            <button type="button" class="text-[10px] text-[#7C3AED] hover:underline font-bold">
                [EDIT]
            </button>
        </div>

        <!-- Cart Line Items Area (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-4 flex flex-col {{ empty($cart) ? 'items-center justify-center text-center' : '' }}">
            @if (empty($cart))
                <!-- Empty Cart Illustration State -->
                <div class="w-16 h-16 border-2 border-dashed border-[#8C849B] flex items-center justify-center mb-3 text-[#8C849B]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="font-display text-base tracking-wide text-[#0A090D] uppercase">
                    Cart is Empty
                </div>
                <p class="font-mono text-[11px] text-[#8C849B] max-w-[220px] mt-1">
                    Select items from the catalog or press <strong class="text-[#0A090D]">[F2]</strong> to search.
                </p>
            @else
                <!-- Active Line Items Preview (Ready for Phase 3.3 Engine) -->
                <div class="w-full space-y-2">
                    @foreach ($cart as $item)
                        <div class="p-2.5 bg-[#F4F1FA] border-1.5 border-[#0A090D] shadow-[2px_2px_0_#0A090D] flex items-center justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <div class="font-display text-xs text-[#0A090D] uppercase truncate">
                                    {{ $item['name'] }}
                                </div>
                                <div class="font-mono text-[10px] text-[#7C3AED]">
                                    {{ $item['quantity'] }}x @ Rp {{ number_format($item['price'], 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="font-mono text-xs font-bold text-[#0A090D]">
                                Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Financial Calculation Summary Dossier -->
        @php
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += ($item['price'] * $item['quantity']);
            }
            $taxAmount = (int) round($subtotal * 0.10); // 10% PB1
            $grandTotal = $subtotal + $taxAmount;
        @endphp

        <div class="bg-[#F4F1FA] border-t-2 border-[#0A090D] p-3.5 space-y-2 flex-shrink-0 font-mono text-xs">
            <div class="flex items-center justify-between text-[#4B4556]">
                <span>SUBTOTAL</span>
                <span class="font-bold text-[#0A090D]">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between text-[#4B4556]">
                <div class="flex items-center gap-1.5">
                    <span>TAX (PB1 10%)</span>
                    <span class="badge-white !text-[8px] !py-0 !px-1">ENABLED</span>
                </div>
                <span class="font-bold text-[#0A090D]">Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between text-[#4B4556]">
                <span>DISCOUNT</span>
                <span class="font-bold text-[#0A090D]">Rp 0</span>
            </div>

            <!-- Grand Total Strip -->
            <div class="pt-2 border-t border-[#0A090D] flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-[#8C849B] block leading-none">TOTAL DUE</span>
                    <span class="font-mono text-[11px] text-[#7C3AED] font-bold">IDR</span>
                </div>
                <div class="font-display text-3xl text-[#0A090D] leading-none tracking-tight">
                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- Checkout Action Button Bar -->
        <div class="p-3 bg-white border-t-2 border-[#0A090D] flex items-center gap-2 flex-shrink-0">
            <button 
                type="button"
                {{ empty($cart) ? 'disabled' : '' }}
                class="btn-solid flex-1 !py-3 !text-xs {{ empty($cart) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#7C3AED]' }} shadow-[3px_3px_0_#0A090D]"
            >
                <span>[F4] CHECKOUT</span>
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>

            <button 
                type="button" 
                {{ empty($cart) ? 'disabled' : '' }}
                wire:click="$set('cart', [])"
                class="btn-outline !py-3 !px-3 text-xs {{ empty($cart) ? 'opacity-40 cursor-not-allowed' : 'hover:bg-red-50' }} border-2 border-[#0A090D]"
                title="Clear Cart (ESC)"
            >
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>

    </aside>

</div>

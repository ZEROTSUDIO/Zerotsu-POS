<div class="flex-1 flex flex-col md:flex-row overflow-hidden relative">

    <!-- ══════════════════════════════════════════════════════════
         LEFT PANEL: PRODUCT CATALOG VIEWPORT (65% on Desktop)
         ══════════════════════════════════════════════════════════ -->
    <section class="flex-1 flex flex-col min-w-0 bg-[#F4F1FA] border-r-2 border-[#0A090D] overflow-hidden">
        
        <!-- Search & Filter Header Strip -->
        <div class="p-3 bg-white border-b-2 border-[#0A090D] flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 flex-shrink-0">
            <!-- Search Bar (Targeted by F2 Shortcut) -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#8C849B]">
                    <svg class="w-4 h-4 text-[#0A090D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="pos-product-search"
                    placeholder="Search menu or SKU... (Press F2)"
                    class="input-brutal w-full pl-9 pr-14 text-xs font-mono font-bold"
                >
                <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                    <span class="hotkey-pill !text-[9px] !py-0.5 !px-1.5">[F2]</span>
                </div>
            </div>

            <!-- Telemetry Stats Badge -->
            <div class="flex items-center gap-2 flex-shrink-0">
                <div class="tag-block tag-dark !text-[10px] !py-1.5 !px-2.5">
                    <span class="text-[#C06BF0] font-bold">{{ $productsCount }}</span> ITEMS READY
                </div>
                <div class="tag-block tag-outline !text-[10px] !py-1.5 !px-2.5 bg-white">
                    <span class="font-bold">{{ $categoriesCount }}</span> CATEGORIES
                </div>
            </div>
        </div>

        <!-- Category Nav Rail (Phase 3.1 Skeleton / Placeholder) -->
        <div class="px-3 py-2 bg-[#FFFFFF] border-b-2 border-[#0A090D] flex items-center gap-2 overflow-x-auto flex-shrink-0 scrollbar-none">
            <button type="button" class="btn-solid !py-1.5 !px-3 !text-[11px] shadow-[2px_2px_0_#0A090D] bg-[#7C3AED] text-white">
                ALL ITEMS
            </button>
            <button type="button" class="btn-outline !py-1.5 !px-3 !text-[11px] shadow-[2px_2px_0_#0A090D] bg-white hover:bg-[#F4F1FA]">
                KOPI
            </button>
            <button type="button" class="btn-outline !py-1.5 !px-3 !text-[11px] shadow-[2px_2px_0_#0A090D] bg-white hover:bg-[#F4F1FA]">
                NON-KOPI
            </button>
            <button type="button" class="btn-outline !py-1.5 !px-3 !text-[11px] shadow-[2px_2px_0_#0A090D] bg-white hover:bg-[#F4F1FA]">
                SNACK & PASTRY
            </button>
        </div>

        <!-- Catalog Grid Container (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-4">
            <!-- Phase 3.1 Architecture Readiness Banner -->
            <div class="panel-offset p-4 bg-white mb-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="badge-purple !text-[9px] !py-0.5 !px-2">PHASE 3.1 READY</span>
                            <span class="font-mono text-[11px] font-bold text-[#7C3AED]">ARCHITECTURAL HUD & SHELL DEPLOYED</span>
                        </div>
                        <h2 class="font-display text-lg tracking-wide uppercase text-[#0A090D]">
                            Cashier POS Terminal Viewport
                        </h2>
                        <p class="font-body text-xs text-[#4B4556] mt-0.5 max-w-xl">
                            Heads-Up Display, Space Mono telemetry clock, hotkey listeners (F2, F4, ESC), and Midtrans Snap SDK are online. 
                            Ready to load dynamic product cards and instant search in <strong>Phase 3.2</strong>.
                        </p>
                    </div>

                    <div class="hidden sm:flex flex-col items-end gap-1">
                        <span class="font-mono text-[10px] text-[#8C849B]">TERMINAL:</span>
                        <span class="badge-dark !text-[10px]">{{ $terminalId }}</span>
                    </div>
                </div>
            </div>

            <!-- Wireframe Grid Preview -->
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3.5 opacity-80">
                @for ($i = 1; $i <= 8; $i++)
                    <div class="panel bg-white p-3 border-2 border-[#0A090D] shadow-[3px_3px_0_#0A090D]">
                        <div class="h-24 bg-[#F4F1FA] border border-[#0A090D] mb-2 flex items-center justify-center text-[#8C849B] font-mono text-[10px]">
                            [ITEM #{{ $i }}]
                        </div>
                        <div class="font-mono text-[9px] text-[#7C3AED] font-bold">CATEGORY #{{ ($i % 3) + 1 }}</div>
                        <div class="font-display text-sm tracking-wide text-[#0A090D] truncate">Menu Item Preview {{ $i }}</div>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="font-mono text-xs font-bold text-[#0A090D]">Rp 25.000</span>
                            <span class="badge-dark !text-[9px] !py-0.5 !px-1.5">STOCK: 50</span>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Terminal Status Footer Bar -->
        <div class="p-2 bg-[#0A0812] text-white border-t-2 border-[#0A090D] flex items-center justify-between px-4 text-[10px] font-mono">
            <div class="flex items-center gap-3">
                <span class="text-[#DDD6FE]">SYS: <strong class="text-[#C06BF0]">LARAVEL 12 + LIVEWIRE 3.7</strong></span>
                <span class="text-[#38225C]">|</span>
                <span class="text-[#DDD6FE]">DB: <strong class="text-white">MYSQL 8.4</strong></span>
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
            <span class="badge-lilac !text-[9px] !py-0.5 !px-1.5">0 ITEMS</span>
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
        <div class="flex-1 overflow-y-auto p-4 flex flex-col items-center justify-center text-center">
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
        </div>

        <!-- Financial Calculation Summary Dossier -->
        <div class="bg-[#F4F1FA] border-t-2 border-[#0A090D] p-3.5 space-y-2 flex-shrink-0 font-mono text-xs">
            <div class="flex items-center justify-between text-[#4B4556]">
                <span>SUBTOTAL</span>
                <span class="font-bold text-[#0A090D]">Rp 0</span>
            </div>
            <div class="flex items-center justify-between text-[#4B4556]">
                <div class="flex items-center gap-1.5">
                    <span>TAX (PB1 10%)</span>
                    <span class="badge-white !text-[8px] !py-0 !px-1">ENABLED</span>
                </div>
                <span class="font-bold text-[#0A090D]">Rp 0</span>
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
                    Rp 0
                </div>
            </div>
        </div>

        <!-- Checkout Action Button Bar -->
        <div class="p-3 bg-white border-t-2 border-[#0A090D] flex items-center gap-2 flex-shrink-0">
            <button 
                type="button"
                disabled
                class="btn-solid flex-1 !py-3 !text-xs opacity-50 cursor-not-allowed shadow-[3px_3px_0_#0A090D]"
            >
                <span>[F4] CHECKOUT</span>
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>

            <button 
                type="button" 
                disabled
                class="btn-outline !py-3 !px-3 text-xs opacity-40 cursor-not-allowed border-2 border-[#0A090D]"
                title="Clear Cart (ESC)"
            >
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>

    </aside>

</div>

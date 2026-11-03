<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'ZerotsuPOS // Terminal 01' }}</title>

    <!-- Google Fonts for Zerotsudios UI -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo:wght@400;500;600;700;800;900&family=Space+Mono:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Official Zerotsudios UI Preset) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        paper: '#FFFFFF',
                        dim: '#F4F1FA',
                        ink: '#0A090D',
                        obsidian: '#0A0812',
                        purple: {
                            DEFAULT: '#7C3AED',
                            deep: '#5B1AA0',
                            mid: '#9333EA',
                            light: '#C06BF0',
                            soft: '#DDD6FE',
                            ink: '#1B0E33'
                        }
                    },
                    fontFamily: {
                        display: ['Anton', 'sans-serif'],
                        mono: ['Space Mono', 'monospace'],
                        body: ['Archivo', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <!-- Zerotsudios UI Core Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/zerotsudios.css') }}">

    <!-- Midtrans Snap JS SDK (Loaded conditionally when client key is set) -->
    @if (!empty(config('midtrans.client_key')))
        <script 
            type="text/javascript"
            src="{{ config('midtrans.snap_js_url') }}"
            data-client-key="{{ config('midtrans.client_key') }}">
        </script>
    @endif

    @livewireStyles

    <style>
        /* POS Viewport Strict Constraints */
        html, body {
            height: 100vh !important;
            margin: 0;
            padding: 0;
            overflow: hidden !important;
            background-color: #F4F1FA;
            color: #0A090D;
            font-family: 'Archivo', sans-serif;
        }

        .hud-border {
            border-bottom: 2px solid #0A090D;
        }

        /* Keyboard Hotkey Indicator Pills */
        .hotkey-pill {
            font-family: 'Space Mono', monospace;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 6px;
            background: #140C24;
            color: #DDD6FE;
            border: 1.5px solid #38225C;
            letter-spacing: 0.05em;
        }

        .hotkey-key {
            color: #C06BF0;
            font-weight: 800;
        }
    </style>
</head>
<body class="h-full flex flex-col font-body antialiased select-none overflow-hidden">

    <!-- Print Registration Technical Marks (Architectural Dossier Aesthetic) -->
    <div class="print-mark hidden lg:block" style="top: 8px; left: 8px;">
        <div class="crosshair"></div>
    </div>
    <div class="print-mark hidden lg:block" style="top: 8px; right: 8px;">
        <div class="crosshair"></div>
    </div>

    <!-- ══════════════════════════════════════════════════════════
         TOPBAR CASHIER HUD (HEADS-UP DISPLAY)
         ══════════════════════════════════════════════════════════ -->
    <header class="hud-border bg-[#0A0812] text-white flex-shrink-0 z-30 px-4 py-2.5 shadow-[0_2px_0_#0A090D]">
        <div class="flex items-center justify-between gap-4">

            <!-- Left: Terminal Branding & Shift Telemetry -->
            <div class="flex items-center gap-3.5">
                <!-- Studio Icon -->
                <a href="{{ route('pos') }}" class="flex items-center gap-2 group focus:outline-none">
                    <img 
                        src="{{ asset('assets/zero-studios-icon.svg') }}" 
                        alt="Zerotsudios Logo" 
                        width="28"
                        height="28"
                        style="width: 28px; height: 28px; min-width: 28px; max-width: 28px;"
                        class="w-7 h-7 flex-shrink-0 drop-shadow-[0_0_8px_rgba(124,58,237,0.6)] group-hover:rotate-12 transition-transform duration-200"
                        onerror="this.style.display='none'"
                    >
                    
                    <!-- Sasha Volkov Stacked Header -->
                    <div class="title-bar-stacked">
                        <span class="title-bar-tag !text-[9px] !py-0.5 !px-2 leading-none">
                            TERMINAL 01 // CASHIER
                        </span>
                        <span class="title-bar-main !text-sm !py-1 !px-2.5 leading-none tracking-wide text-white flex items-center gap-1.5 font-display">
                            ZEROTSU<span class="text-[#7C3AED]">POS</span>
                        </span>
                    </div>
                </a>

                <!-- Shift Status Indicator -->
                <div class="hidden sm:inline-flex items-center gap-2 px-2.5 py-1 bg-[#140C24] border border-[#38225C] text-[10px] font-mono tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-[#10B981] animate-pulse"></span>
                    <span class="text-[#DDD6FE] font-bold">SHIFT #01</span>
                    <span class="text-[#8C849B]">·</span>
                    <span class="text-[#10B981] font-bold">ACTIVE</span>
                </div>

                <!-- Environment Badge -->
                <div class="hidden md:inline-flex badge-lilac !text-[9px] !py-0.5 !px-2">
                    {{ config('midtrans.is_production') ? 'LIVE PRODUCTION' : 'MIDTRANS SANDBOX' }}
                </div>
            </div>

            <!-- Center: Live Digital Telemetry Clock (Space Mono) -->
            <div 
                x-data="{
                    time: '',
                    date: '',
                    init() {
                        this.tick();
                        setInterval(() => this.tick(), 1000);
                    },
                    tick() {
                        const now = new Date();
                        this.time = now.toLocaleTimeString('id-ID', { hour12: false }) + ' WIB';
                        this.date = now.toLocaleDateString('id-ID', { 
                            weekday: 'short', 
                            day: '2-digit', 
                            month: 'short', 
                            year: 'numeric' 
                        }).toUpperCase();
                    }
                }"
                class="hidden lg:flex items-center gap-3 bg-[#140C24] px-3.5 py-1.5 border border-[#38225C] shadow-[2px_2px_0_#0A090D]"
            >
                <div class="flex items-center gap-2 text-xs font-mono font-bold">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#C06BF0]"></span>
                    <span x-text="time" class="text-white tracking-wider">--:--:-- WIB</span>
                </div>
                <span class="text-[#38225C]">|</span>
                <span x-text="date" class="text-[#DDD6FE] font-mono text-[11px] tracking-wider">-- --- ----</span>
            </div>

            <!-- Right: Keyboard Hotkey Hints & Cashier Identity -->
            <div class="flex items-center gap-2.5 sm:gap-3.5">

                <!-- Hotkey Reference Guides (Hidden on small mobile) -->
                <div class="hidden xl:flex items-center gap-2">
                    <div class="hotkey-pill" title="Shortcut F2: Focus Live Product Search">
                        <span class="hotkey-key">[F2]</span> SEARCH
                    </div>
                    <div class="hotkey-pill" title="Shortcut F4: Open Checkout Modal">
                        <span class="hotkey-key">[F4]</span> CHECKOUT
                    </div>
                    <div class="hotkey-pill" title="Shortcut ESC: Reset / Clear">
                        <span class="hotkey-key">[ESC]</span> RESET
                    </div>
                </div>

                <!-- Cashier Profile Dossier Chip -->
                <div class="flex items-center gap-2.5 bg-[#140C24] px-3 py-1 border border-[#38225C] shadow-[2px_2px_0_#0A090D]">
                    <div class="w-6 h-6 rounded-none bg-[#7C3AED] border border-[#0A090D] flex items-center justify-center font-mono text-[11px] font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->name ?? 'ZC', 0, 2)) }}
                    </div>
                    <div class="text-left hidden sm:block">
                        <div class="text-xs font-mono font-bold text-white leading-none">
                            {{ auth()->user()->name ?? 'Zerotsu Cashier' }}
                        </div>
                        <div class="text-[9px] font-mono text-[#C06BF0] font-bold tracking-widest mt-0.5 leading-none">
                            OPERATOR
                        </div>
                    </div>
                </div>

                <!-- Fullscreen Toggle Button -->
                <button 
                    type="button" 
                    onclick="if(!document.fullscreenElement){document.documentElement.requestFullscreen();}else{document.exitFullscreen();}"
                    class="p-1.5 bg-[#140C24] hover:bg-[#7C3AED] text-[#DDD6FE] hover:text-white border border-[#38225C] transition-colors focus:outline-none"
                    title="Toggle Fullscreen POS Mode"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                </button>
            </div>

        </div>
    </header>

    <!-- ══════════════════════════════════════════════════════════
         MAIN POS TERMINAL VIEWPORT (Fills remaining height)
         ══════════════════════════════════════════════════════════ -->
    <main class="flex-1 overflow-hidden relative flex flex-col min-h-0">
        {{ $slot }}
    </main>

    @livewireScripts

    <!-- Zerotsudios UI Interactivity Scripts -->
    <script src="{{ asset('js/zerotsudios.js') }}"></script>

    <!-- Global POS Keyboard Hotkey Listeners -->
    <script>
        document.addEventListener('keydown', function(e) {
            // [F2] -> Focus Product Search Input
            if (e.key === 'F2') {
                e.preventDefault();
                const searchInput = document.getElementById('pos-product-search');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }

            // [F4] -> Trigger Checkout Modal
            if (e.key === 'F4') {
                e.preventDefault();
                Livewire.dispatch('trigger-checkout');
            }

            // [Escape] -> Close modals or clear focus
            if (e.key === 'Escape') {
                Livewire.dispatch('close-modals');
            }
        });
    </script>
</body>
</html>

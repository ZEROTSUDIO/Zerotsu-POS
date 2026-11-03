<nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200 shadow-xs"
    x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
            <!-- Brand -->
            <a href="{{ url('/') }}" class="flex items-center hover:opacity-85 transition-opacity">
                <img src="{{ asset('img/logo.svg') }}" alt="{{ config('app.name', 'Laravel') }}" class="h-9 w-auto">
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <x-front.navlink href="{{ url('/') }}" :active="request()->is('/')">Home</x-front.navlink>
                <x-front.navlink href="{{ url('/about') }}" :active="request()->is('about')">About</x-front.navlink>

                <!-- Dropdown for Catalogue -->
                <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button
                        class="nav-link-hover text-slate-700 text-xs font-semibold tracking-wider uppercase hover:text-blue-600 transition-colors flex items-center gap-1 py-2">
                        Catalog
                        <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': open }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div
                        class="absolute left-0 mt-1 w-52 bg-white shadow-xl rounded-xl border border-slate-100 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top z-50">
                        <a href="{{ url('/catalogs') }}"
                            class="block px-4 py-2 text-xs font-medium uppercase tracking-wider text-slate-600 hover:bg-slate-50 hover:text-blue-600">
                            All Catalog
                        </a>
                        @foreach ($categories as $category)
                            <a href="{{ url('/catalogs?category=' . $category->slug) }}"
                                class="block px-4 py-2 text-xs font-medium uppercase tracking-wider text-slate-600 hover:bg-slate-50 hover:text-blue-600">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <x-front.navlink href="{{ url('/articles') }}" :active="request()->is('articles*')">Articles</x-front.navlink>
                <x-front.navlink href="{{ url('/contact') }}" :active="request()->is('contact')">Contact</x-front.navlink>
            </div>

            <!-- Right Actions (Search + Mobile Toggle) -->
            <div class="flex items-center space-x-4">
                <!-- Search -->
                <form action="{{ url('/catalogs') }}" class="hidden lg:flex items-center relative">
                    <input type="text" name="search" placeholder="Search..."
                        class="bg-slate-100 border border-slate-200 rounded-lg pl-3 pr-8 py-1.5 text-slate-800 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all w-36 focus:w-48 placeholder-slate-400">
                    <button class="absolute right-2.5 text-slate-400 hover:text-blue-600 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>

                <!-- Admin Link -->
                <a href="{{ route('admin') }}" class="hidden sm:inline-flex items-center text-xs font-semibold px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                    Admin
                </a>

                <!-- Mobile Menu Button -->
                <button @click="mobileOpen = !mobileOpen" class="md:hidden text-slate-600 hover:text-slate-900 focus:outline-none p-1.5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileOpen" x-transition class="md:hidden mt-4 pb-3 space-y-3 border-t border-slate-100 pt-4"
            style="display: none;">
            <a href="{{ url('/') }}" class="block text-slate-700 text-sm font-medium hover:text-blue-600">Home</a>
            <a href="{{ url('/about') }}" class="block text-slate-700 text-sm font-medium hover:text-blue-600">About</a>
            <div x-data="{ catOpen: false }">
                <button @click="catOpen = !catOpen"
                    class="flex justify-between items-center w-full text-slate-700 text-sm font-medium hover:text-blue-600">
                    <span>Catalog</span>
                    <svg class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': catOpen }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="catOpen" class="pl-4 mt-2 space-y-2 border-l border-slate-200 ml-1">
                    <a href="{{ url('/catalogs') }}" class="block text-slate-600 text-xs hover:text-blue-600">All Catalog</a>
                    @foreach ($categories as $category)
                        <a href="{{ url('/catalogs?category=' . $category->slug) }}"
                            class="block text-slate-600 text-xs hover:text-blue-600">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>
            <a href="{{ url('/articles') }}" class="block text-slate-700 text-sm font-medium hover:text-blue-600">Articles</a>
            <a href="{{ url('/contact') }}" class="block text-slate-700 text-sm font-medium hover:text-blue-600">Contact</a>
            <a href="{{ route('admin') }}" class="block text-blue-600 text-sm font-semibold pt-2 border-t border-slate-100">Admin Dashboard</a>
        </div>
    </div>
</nav>

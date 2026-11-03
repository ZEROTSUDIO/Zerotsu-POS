<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'Laravel') }} - Admin Dashboard</title>
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.6.0/tinymce.min.js" referrerpolicy="origin"></script>
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">
</head>

<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col justify-between" x-data="{ sidebarOpen: false }">
    <!-- Sidebar -->
    <x-admin.sidebar></x-admin.sidebar>

    <!-- Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 z-40 md:hidden" style="display: none;">
    </div>

    <!-- Top Navigation Bar -->
    <div id="topbar"
        class="fixed top-0 left-0 md:left-64 right-0 h-16 bg-white border-b border-slate-200 z-40 transition-all duration-300">
        <div class="flex items-center justify-between h-full px-4 md:px-8">
            <button id="toggleSidebar" @click="sidebarOpen = !sidebarOpen"
                class="text-xl text-slate-600 hover:text-slate-900 transition-colors md:hidden p-1">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Desktop breadcrumb placeholder / title -->
            <div class="hidden md:flex items-center gap-2 text-xs font-medium text-slate-500 uppercase tracking-wider">
                <span>Admin Console</span>
            </div>

            <div class="flex items-center gap-4">
                <!-- User Menu -->
                <div class="relative" x-data="{ dropdownOpen: false }">
                    <button @click="dropdownOpen = !dropdownOpen" @click.outside="dropdownOpen = false"
                        class="flex items-center gap-3 cursor-pointer px-3 py-1.5 rounded-lg hover:bg-slate-100 transition-colors focus:outline-none">
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-xs">
                            {{ Auth::check() ? Auth::user()->name[0] : 'A' }}
                        </div>
                        <div class="user-details hidden md:block text-left">
                            <div class="text-xs font-bold text-slate-800">
                                {{ Auth::check() ? Auth::user()->name : 'Admin' }}
                            </div>
                            <div class="text-[10px] text-slate-500">
                                Administrator
                            </div>
                        </div>
                        <i class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200"
                            :class="{ 'rotate-180': dropdownOpen }"></i>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="dropdownOpen" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50"
                        style="display: none;">
                        <div class="px-4 py-2 border-b border-slate-100">
                            <p class="text-xs font-semibold text-slate-800">{{ Auth::check() ? Auth::user()->name : 'Admin' }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ Auth::check() ? Auth::user()->email : 'admin@example.com' }}</p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div id="mainContent" class="ml-0 md:ml-64 mt-16 p-6 md:p-8 flex-grow">
        {{ $slot }}
    </div>

    <!-- Admin Footer -->
    <footer class="ml-0 md:ml-64 py-4 px-6 border-t border-slate-200 bg-white text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} {{ config('app.name', 'Laravel Starter') }}. All rights reserved.
    </footer>

    @stack('scripts')
</body>

</html>

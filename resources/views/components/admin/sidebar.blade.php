<div id="sidebar" :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
    class="fixed top-0 left-0 h-screen w-64 bg-slate-900 border-r border-slate-800 shadow-xl z-50 transition-transform duration-300 overflow-y-auto transform -translate-x-full md:translate-x-0">
    <div class="p-6">
        <!-- Logo -->
        <div class="flex items-center gap-3 mb-8 pb-5 border-b border-slate-800">
            <a href="{{ route('admin') }}" class="flex items-center gap-2">
                <img src="{{ asset('img/logo-white.svg') }}" alt="{{ config('app.name', 'Admin') }}" class="h-8 w-auto">
            </a>
        </div>

        <!-- Navigation -->
        <nav class="space-y-1.5">
            <x-admin.navlink href="{{ url('admin') }}" active="{{ request()->routeIs('admin') }}">
                <i class="fas fa-tachometer-alt w-5 text-center"></i>
                <span class="sidebar-text">Dashboard</span>
            </x-admin.navlink>
            <x-admin.navlink href="{{ url('admin/catalogues') }}" active="{{ request()->routeIs('admin.catalogues') }}">
                <i class="fas fa-box w-5 text-center"></i>
                <span class="sidebar-text">Catalog</span>
            </x-admin.navlink>
            <x-admin.navlink href="{{ url('admin/categories') }}" active="{{ request()->routeIs('admin.categories') }}">
                <i class="fas fa-folder w-5 text-center"></i>
                <span class="sidebar-text">Categories</span>
            </x-admin.navlink>
            <x-admin.navlink href="{{ url('admin/messages') }}" active="{{ request()->routeIs('admin.messages') }}">
                <i class="fas fa-envelope w-5 text-center"></i>
                <span class="sidebar-text">Messages</span>
            </x-admin.navlink>
            <x-admin.navlink href="{{ url('admin/articles') }}" active="{{ request()->routeIs('admin.articles') }}">
                <i class="fas fa-file-alt w-5 text-center"></i>
                <span class="sidebar-text">Articles</span>
            </x-admin.navlink>
            <x-admin.navlink href="{{ url('admin/topics') }}" active="{{ request()->routeIs('admin.topics') }}">
                <i class="fas fa-tags w-5 text-center"></i>
                <span class="sidebar-text">Topics</span>
            </x-admin.navlink>

            <div class="pt-6 mt-6 border-t border-slate-800">
                <a href="{{ url('/') }}" target="_blank"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors text-xs font-semibold uppercase tracking-wider">
                    <i class="fas fa-external-link-alt w-5 text-center"></i>
                    <span>View Site</span>
                </a>
            </div>
        </nav>
    </div>
</div>

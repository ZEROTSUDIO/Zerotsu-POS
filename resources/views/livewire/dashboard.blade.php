<div id="dashboard" class="page active animate-slide-in">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-slate-900 mb-2">
            Dashboard Overview
        </h1>
        <p class="text-gray-500">
            Welcome to your elegant admin dashboard
        </p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-lg hover-lift border-l-4 border-blue-500">
            <div class="w-14 h-14 rounded-xl gradient-blue flex items-center justify-center text-white text-2xl mb-4">
                <i class="fas fa-box"></i>
            </div>
            <div class="text-3xl font-bold text-slate-900 mb-2">
                {{ $totalProducts }}
            </div>
            <div class="text-sm text-gray-500 font-medium">
                Total Products
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg hover-lift border-l-4 border-purple-500">
            <div class="w-14 h-14 rounded-xl gradient-purple flex items-center justify-center text-white text-2xl mb-4">
                <i class="fas fa-newspaper"></i>
            </div>
            <div class="text-3xl font-bold text-slate-900 mb-2">
                {{ $totalArticles }}
            </div>
            <div class="text-sm text-gray-500 font-medium">
                Total Articles
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg hover-lift border-l-4 border-green-500">
            <div class="w-14 h-14 rounded-xl gradient-green flex items-center justify-center text-white text-2xl mb-4">
                <i class="fas fa-tags"></i>
            </div>
            <div class="text-3xl font-bold text-slate-900 mb-2">
                {{ $totalCategories }}
            </div>
            <div class="text-sm text-gray-500 font-medium">
                Total Categories
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg hover-lift border-l-4 border-pink-500">
            <div class="w-14 h-14 rounded-xl gradient-pink flex items-center justify-center text-white text-2xl mb-4">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="text-3xl font-bold text-slate-900 mb-2">
                {{ $totalNewContacts }}
            </div>
            <div class="text-sm text-gray-500 font-medium">
                New Enquiries
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Activity -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-slate-900">
                    Recent Enquiries
                </h2>
            </div>
            <div class="p-6 space-y-4">
                @forelse($recentContacts as $contact)
                    <div class="flex items-center gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                        <div
                            class="w-12 h-12 rounded-xl gradient-pink flex items-center justify-center text-white shrink-0">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h6 class="font-semibold text-gray-800 mb-1 truncate">
                                {{ $contact->subject ?? 'No Subject' }}
                            </h6>
                            <p class="text-sm text-gray-600 truncate">From: {{ $contact->name }}</p>
                            <small class="text-gray-400">{{ $contact->created_at->diffForHumans() }}</small>
                        </div>
                        <div>
                            <a wire:navigate href="{{ url('admin/messages') }}"
                                class="text-xs font-medium text-blue-600 hover:text-blue-800">
                                View
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-gray-500">
                        No recent enquiries found.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-slate-900">
                    Quick Actions
                </h2>
            </div>
            <div class="p-6 space-y-3">
                <a wire:navigate href="{{ url('admin/catalogues') }}"
                    class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-xs transition-colors text-sm">
                    <i class="fas fa-box mr-2"></i>Manage Catalog
                </a>
                <a wire:navigate href="{{ url('admin/articles') }}"
                    class="block w-full text-center bg-white border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-semibold hover:bg-slate-50 transition-colors text-sm">
                    <i class="fas fa-file-alt mr-2"></i>Manage Articles
                </a>
                <a wire:navigate href="{{ url('admin/messages') }}"
                    class="block w-full text-center bg-white border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-semibold hover:bg-slate-50 transition-colors text-sm">
                    <i class="fas fa-envelope mr-2"></i>View Messages
                </a>
            </div>
        </div>
    </div>
</div>

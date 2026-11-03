<x-front.layout :title="config('app.name', 'Laravel Starter') . ' | Modern Modular Web Application'"
    :meta-description="'A scalable, production-ready full stack web starter built with Laravel, Livewire, and Tailwind CSS.'">

    <!-- Hero Section -->
    <x-front.carousel :totalSlides="3">
        <x-front.hero-slide :index="0" image="https://images.unsplash.com/photo-1518770660439-4636190af475?w=1600"
            title="MODERN ARCHITECTURE" subtitle="ENGINEERED FOR SCALE, RELIABILITY AND SPEED"
            :tags="['MODULAR', 'HIGH PERFORMANCE', 'PRODUCTION READY']" />

        <x-front.hero-slide :index="1" image="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1600"
            title="PRECISION ENGINEERING" subtitle="STREAMLINED SYSTEMS DESIGNED FOR MODERN TEAMS"
            :tags="['FLEXIBLE', 'DEVELOPER FIRST', 'ROBUST']" />

        <x-front.hero-slide :index="2" image="https://images.unsplash.com/photo-1497366216548-37526070297c?w=1600"
            title="INNOVATIVE PLATFORM" subtitle="CONNECTING FUNCTIONALITY WITH MODERN DESIGN"
            :tags="['CUSTOMIZABLE', 'FAST DEPLOY', 'ENTERPRISE']" />
    </x-front.carousel>

    <!-- Value Proposition -->
    <section class="py-24 px-6 bg-white border-b border-slate-100">
        <div class="max-w-7xl mx-auto">
            <span class="block text-center text-xs font-semibold text-blue-600 tracking-wider uppercase mb-3">Core Pillars</span>
            <h2 class="text-3xl md:text-4xl font-bold text-center text-slate-900 mb-16 tracking-tight">Why Choose Our Platform</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="w-12 h-12 mx-auto mb-4 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Modular Structure</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Built with component-driven architecture for effortless maintenance, scalability, and reusability.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="w-12 h-12 mx-auto mb-4 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fa-solid fa-gauge-high"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">High Performance</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Optimized database queries, reactive Livewire components, and lightning-fast asset compilation.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="w-12 h-12 mx-auto mb-4 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Fully Customizable</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Easily swap stylesheets, layouts, and business logic to fit any client specification or branding.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-24 px-6 bg-slate-50">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
                <div>
                    <span class="text-xs font-semibold text-blue-600 tracking-wider uppercase mb-2 block">Catalog Highlights</span>
                    <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Featured Products</h2>
                </div>
                <a href="{{ url('/catalogs') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors mt-4 md:mt-0">
                    View All Products &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse ($featuredProducts as $product)
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow flex flex-col">
                        <a href="{{ url('/catalogs/' . $product->category->slug . '/' . $product->slug) }}" class="block aspect-video bg-slate-100 overflow-hidden relative group">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('img/placeholder.svg') }}"
                                alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                onerror="this.src='{{ asset('img/placeholder.svg') }}'">
                        </a>
                        <div class="p-6 flex-1 flex flex-col">
                            <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">{{ $product->category->name }}</span>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">
                                <a href="{{ url('/catalogs/' . $product->category->slug . '/' . $product->slug) }}" class="hover:text-blue-600 transition-colors">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <p class="text-sm text-slate-600 line-clamp-2 mb-4 flex-1">
                                {{ Str::limit(strip_tags($product->description), 90) }}
                            </p>
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-xs text-slate-400 font-mono">{{ $product->code }}</span>
                                <a href="{{ url('/catalogs/' . $product->category->slug . '/' . $product->slug) }}" class="text-xs font-semibold text-blue-600 hover:underline">
                                    Details &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 text-slate-400">
                        No featured products currently available. Run seeders to populate.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Categories / Collections -->
    <section class="py-24 px-6 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <span class="text-xs font-semibold text-blue-600 tracking-wider uppercase mb-2 block">Categories</span>
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Explore by Category</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($categories as $category)
                    <a href="{{ url('/catalogs?category=' . $category->slug) }}"
                        class="p-6 rounded-2xl border border-slate-200 bg-slate-50 hover:border-blue-500 hover:bg-blue-50/30 transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-folder-open"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 mb-1 group-hover:text-blue-600 transition-colors">{{ $category->name }}</h3>
                        <p class="text-xs text-slate-500 line-clamp-2">{{ $category->description ?? 'Explore all catalog items under this category.' }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Latest Articles -->
    <section class="py-24 px-6 bg-slate-50 border-t border-slate-100">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
                <div>
                    <span class="text-xs font-semibold text-blue-600 tracking-wider uppercase mb-2 block">Latest News</span>
                    <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Articles & Resources</h2>
                </div>
                <a href="{{ url('/articles') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors mt-4 md:mt-0">
                    Read All Articles &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse ($latestArticles as $article)
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm flex flex-col">
                        <div class="aspect-video bg-slate-100 overflow-hidden">
                            <img src="{{ $article->image ? asset('storage/' . $article->image) : asset('img/placeholder.svg') }}"
                                alt="{{ $article->title }}" class="w-full h-full object-cover"
                                onerror="this.src='{{ asset('img/placeholder.svg') }}'">
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex items-center gap-2 text-xs text-slate-400 mb-2">
                                <span class="font-semibold text-blue-600">{{ $article->topic->name ?? 'General' }}</span>
                                <span>&bull;</span>
                                <span>{{ $article->created_at->diffForHumans() }}</span>
                            </div>
                            <h3 class="font-bold text-slate-900 mb-2 hover:text-blue-600 transition-colors">
                                <a href="{{ url('/articles/' . $article->slug) }}">{{ $article->title }}</a>
                            </h3>
                            <p class="text-sm text-slate-600 line-clamp-3 mb-4 flex-1">
                                {{ Str::limit(strip_tags($article->body), 120) }}
                            </p>
                            <a href="{{ url('/articles/' . $article->slug) }}" class="text-xs font-semibold text-blue-600 hover:underline">
                                Read Article &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 text-slate-400">
                        No articles published yet.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-20 px-6 bg-slate-900 text-white">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 tracking-tight">Ready to Get Started?</h2>
            <p class="text-slate-400 mb-8 max-w-xl mx-auto text-sm leading-relaxed">
                Connect with our team to explore tailored implementations, documentation, and technical support.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ url('/catalogs') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors">
                    Explore Catalog
                </a>
                <a href="{{ url('/contact') }}" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-sm font-semibold transition-colors border border-slate-700">
                    Contact Us
                </a>
            </div>
        </div>
    </section>

    @push('schema')
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "Organization",
            "name": "{{ config('app.name', 'Starter') }}",
            "url": "{{ url('/') }}",
            "logo": "{{ asset('img/logo.svg') }}"
        }
        </script>
    @endpush
</x-front.layout>

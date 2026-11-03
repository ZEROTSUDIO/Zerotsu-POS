<x-front.layout>
    <x-slot:title>Product Catalog - {{ config('app.name', 'Laravel Starter') }}</x-slot:title>

    <section class="pt-32 pb-24 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-8">
                <span class="text-xs font-semibold text-blue-600 tracking-wider uppercase mb-2 block">Our Collection</span>
                <h1 class="text-4xl md:text-5xl font-bold text-slate-900 tracking-tight">Product Catalog</h1>
            </div>

            <x-front.category-filter :categories="$categories" />

            <div id="products" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
                @forelse ($catalogs as $catalog)
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md transition-shadow flex flex-col group" data-category="{{ $catalog->category->name }}">
                        <a href="{{ url('/catalogs/' . $catalog->category->slug . '/' . $catalog->slug) }}" class="block aspect-video bg-slate-100 overflow-hidden relative">
                            <img src="{{ $catalog->image ? asset('storage/' . $catalog->image) : asset('img/placeholder.svg') }}"
                                alt="{{ $catalog->name }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                onerror="this.src='{{ asset('img/placeholder.svg') }}'">
                        </a>
                        <div class="p-5 flex-1 flex flex-col">
                            <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">{{ $catalog->category->name }}</span>
                            <h3 class="text-base font-bold text-slate-900 mb-2">
                                <a href="{{ url('/catalogs/' . $catalog->category->slug . '/' . $catalog->slug) }}" class="hover:text-blue-600 transition-colors">
                                    {{ $catalog->name }}
                                </a>
                            </h3>
                            <p class="text-xs text-slate-500 line-clamp-2 mb-4 flex-1">
                                {{ Str::limit(strip_tags($catalog->description), 80) }}
                            </p>
                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-xs font-mono text-slate-400">{{ $catalog->code }}</span>
                                <a href="{{ url('/catalogs/' . $catalog->category->slug . '/' . $catalog->slug) }}" class="text-xs font-semibold text-blue-600 hover:underline">
                                    View Details &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-16 text-slate-400">
                        No products match the selected criteria.
                    </div>
                @endforelse
            </div>

            <div class="mt-12">
                {{ $catalogs->links() }}
            </div>
        </div>
    </section>
</x-front.layout>

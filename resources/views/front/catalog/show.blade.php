<x-front.layout :title="$catalog->name . ' | ' . config('app.name', 'Catalog')"
    :meta-description="Str::limit(strip_tags($catalog->description), 160)"
    :canonical="route('catalog.show', [$catalog->category->slug, $catalog->slug])"
    :og-image="$catalog->image ? asset('storage/' . $catalog->image) : asset('img/placeholder.svg')">

    <section class="pt-40 pb-32 px-6" x-data="{ activeImage: '{{ $catalog->image ? asset('storage/' . $catalog->image) : asset('img/placeholder.svg') }}', lightboxOpen: false }">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-12 gap-12 lg:gap-24">
                <!-- Gallery Section -->
                <div class="md:col-span-7">
                    <div class="mb-6 overflow-hidden shadow-lg rounded-xl bg-slate-100 border border-slate-200">
                        <img :src="activeImage" alt="{{ $catalog->name }}" @click="lightboxOpen = true"
                            class="w-full h-[500px] object-cover cursor-pointer transition-opacity duration-300">
                    </div>
                    <div class="grid grid-cols-4 gap-4">
                        @if ($catalog->image)
                            <div class="cursor-pointer overflow-hidden rounded-lg border border-transparent hover:border-blue-500 transition-all"
                                @click="activeImage = '{{ asset('storage/' . $catalog->image) }}'">
                                <img src="{{ asset('storage/' . $catalog->image) }}" class="w-full h-24 object-cover">
                            </div>
                        @endif
                        @foreach ($catalog->images as $image)
                            <div class="cursor-pointer overflow-hidden rounded-lg border border-transparent hover:border-blue-500 transition-all"
                                @click="activeImage = '{{ asset('storage/' . $image->image_path) }}'">
                                <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-24 object-cover"
                                    onerror="this.src='{{ asset('img/placeholder.svg') }}'">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Product Info -->
                <div class="md:col-span-5">
                    <div class="sticky top-32">
                        <p class="text-xs text-blue-600 font-semibold tracking-wider uppercase mb-2">
                            {{ $catalog->category->name }} &middot; {{ $catalog->code }}
                        </p>
                        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-6">{{ $catalog->name }}</h1>

                        <div class="text-slate-600 leading-relaxed mb-8 text-base">
                            {{ $catalog->description }}
                        </div>

                        <div class="mb-8 space-y-3 border-t border-slate-200 pt-6">
                            <h4 class="text-sm font-semibold uppercase tracking-wider text-slate-900 mb-4">Specifications</h4>
                            @if ($catalog->specification)
                                @foreach ($catalog->specification as $key => $value)
                                    <div class="flex justify-between items-center text-sm border-b border-slate-100 pb-2 last:border-0">
                                        <span class="text-slate-500 uppercase tracking-wide text-xs">{{ Str::title(str_replace('_', ' ', $key)) }}</span>
                                        <span class="text-slate-900 font-medium">
                                            @if (Str::lower($key) === 'price' && is_numeric($value))
                                                ${{ number_format($value, 2) }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <a href="{{ url('contact') }}"
                            class="block w-full text-center bg-blue-600 text-white py-3.5 px-6 rounded-lg font-medium text-sm hover:bg-blue-700 transition-colors shadow-sm">
                            Inquire About This Product
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="lightboxOpen" x-transition.opacity @keydown.escape.window="lightboxOpen = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80" style="display: none;">
            <div class="absolute inset-0" @click="lightboxOpen = false"></div>
            <img :src="activeImage" class="relative max-w-[90vw] max-h-[90vh] object-contain shadow-2xl rounded-lg"
                alt="{{ $catalog->name }}">
            <button @click="lightboxOpen = false" class="absolute top-6 right-6 text-white text-3xl leading-none">
                &times;
            </button>
        </div>
    </section>
</x-front.layout>

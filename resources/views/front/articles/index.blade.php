<x-front.layout>
    <x-slot:title>Articles & Updates - {{ config('app.name', 'Laravel Starter') }}</x-slot:title>

    <section class="pt-32 pb-24 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <span class="text-xs font-semibold text-blue-600 tracking-wider uppercase mb-2 block">Insights & News</span>
                <h1 class="text-4xl md:text-5xl font-bold text-slate-900 tracking-tight">Articles & Knowledge Base</h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse ($articles as $article)
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md transition-shadow flex flex-col">
                        <a href="{{ url('/articles/' . $article->slug) }}" class="block aspect-video bg-slate-100 overflow-hidden relative">
                            <img src="{{ $article->image ? asset('storage/' . $article->image) : asset('img/placeholder.svg') }}"
                                alt="{{ $article->title }}"
                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                onerror="this.src='{{ asset('img/placeholder.svg') }}'">
                        </a>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex items-center gap-2 text-xs text-slate-400 mb-2">
                                <a href="{{ url('/articles?topic=' . $article->topic->slug) }}" class="font-semibold text-blue-600 hover:underline">
                                    {{ $article->topic->name }}
                                </a>
                                <span>&bull;</span>
                                <span>{{ $article->created_at->format('M d, Y') }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">
                                <a href="{{ url('/articles/' . $article->slug) }}" class="hover:text-blue-600 transition-colors">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            <p class="text-sm text-slate-600 line-clamp-3 mb-6 flex-1">
                                {{ Str::limit(strip_tags($article->body), 120) }}
                            </p>
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-xs text-slate-400">By {{ $article->author->name ?? 'Admin' }}</span>
                                <a href="{{ url('/articles/' . $article->slug) }}" class="text-xs font-semibold text-blue-600 hover:underline">
                                    Read Article &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-16 text-slate-400">
                        No articles found.
                    </div>
                @endforelse
            </div>

            <div class="mt-12">
                {{ $articles->links() }}
            </div>
        </div>
    </section>
</x-front.layout>

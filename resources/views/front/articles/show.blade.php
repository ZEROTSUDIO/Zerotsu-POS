<x-front.layout>
    <x-slot:title>{{ $article->title }} - {{ config('app.name', 'Laravel Starter') }}</x-slot:title>

    <article class="pt-32 pb-24 px-6">
        <div class="max-w-3xl mx-auto">
            <div class="mb-8">
                <a href="{{ url('/articles') }}" class="text-xs font-semibold text-blue-600 hover:underline inline-flex items-center gap-1 mb-6">
                    &larr; Back to all articles
                </a>
                <div class="flex items-center gap-2 text-xs text-slate-400 mb-3">
                    <span class="font-semibold text-blue-600">{{ $article->topic->name }}</span>
                    <span>&bull;</span>
                    <span>{{ $article->created_at->format('F d, Y') }}</span>
                    @if ($article->author)
                        <span>&bull;</span>
                        <span>By {{ $article->author->name }}</span>
                    @endif
                </div>
                <h1 class="text-3xl md:text-5xl font-bold text-slate-900 tracking-tight leading-tight mb-8">
                    {{ $article->title }}
                </h1>
            </div>

            @if ($article->image)
                <div class="aspect-video w-full rounded-2xl overflow-hidden mb-12 shadow-md bg-slate-100">
                    <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                </div>
            @endif

            <div class="prose prose-slate prose-lg max-w-none text-slate-700 leading-relaxed">
                {!! $article->body !!}
            </div>

            <div class="mt-16 pt-8 border-t border-slate-200 flex justify-between items-center">
                <a href="{{ url('/articles') }}" class="text-xs font-semibold text-blue-600 hover:underline">
                    &larr; Back to Articles
                </a>
                <span class="text-xs text-slate-400">Share or bookmark this post</span>
            </div>
        </div>
    </article>
</x-front.layout>

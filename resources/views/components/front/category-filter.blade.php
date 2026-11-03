<div class="bg-white py-6 mb-12 border-y border-slate-200">
    <div class="flex flex-wrap justify-center gap-2">
        <a href="{{ url('/catalogs#products') }}"
            class="px-5 py-2 rounded-full border text-xs font-semibold uppercase tracking-wider transition-colors
            {{ request()->missing('category') ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-700 border-slate-300 hover:border-blue-500 hover:text-blue-600' }}">
            All
        </a>

        @foreach ($categories as $category)
            <a href="{{ url('/catalogs?category=' . $category->slug . '#products') }}"
                class="px-5 py-2 rounded-full border text-xs font-semibold uppercase tracking-wider transition-colors
                {{ request()->get('category') === $category->slug ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-700 border-slate-300 hover:border-blue-500 hover:text-blue-600' }}">
                {{ $category->name }}
            </a>
        @endforeach
    </div>
</div>

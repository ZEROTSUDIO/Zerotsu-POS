@props([
    'index' => 0,
    'image',
    'title',
    'subtitle',
    'tags' => [],
])

<div class="absolute inset-0 transition-opacity duration-1000 ease-in-out" x-show="active === {{ $index }}"
    x-transition:enter="transition-opacity duration-1000" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-1000"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="h-full w-full bg-cover bg-center"
        style="background-image: linear-gradient(rgba(58, 53, 47, 0.15), rgba(58, 53, 47, 0.25)), url('{{ $image }}')">

        <div class="max-w-7xl mx-auto px-6 h-full flex items-center">
            <div class="max-w-2xl" x-show="active === {{ $index }}"
                x-transition:enter="transition-all duration-1000 delay-300"
                x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">
                <h1 class="font-heading text-5xl md:text-7xl text-white font-light tracking-[4px] mb-8 drop-shadow-xl">
                    {{ $title }}</h1>
                <p class="text-white/90 text-sm tracking-[3px] uppercase mb-10 font-light">{{ $subtitle }}</p>

                @if (!empty($tags))
                    <div class="flex flex-wrap gap-4">
                        @foreach ($tags as $tag)
                            <span
                                class="text-white/70 text-[10px] tracking-[2px] uppercase border border-white/20 px-4 py-1.5 backdrop-blur-sm">{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

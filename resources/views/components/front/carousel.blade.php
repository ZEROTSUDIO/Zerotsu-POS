@props([
    'id' => 'heroCarousel',
    'interval' => 5000,
    'totalSlides' => 1,
])

<section id="{{ $id }}" class="relative h-screen overflow-hidden group" x-data="{
    active: 0,
    total: {{ $totalSlides }},
    autoplay: true,
    interval: {{ $interval }},
    next() { this.active = (this.active + 1) % this.total },
    prev() { this.active = (this.active - 1 + this.total) % this.total },
    init() {
        setInterval(() => {
            if (this.autoplay) this.next();
        }, this.interval);
    }
}"
    @mouseenter="autoplay = false" @mouseleave="autoplay = true">

    {{-- Indicators --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-30 flex gap-3">
        <template x-for="i in total" :key="i - 1">
            <button @click="active = i-1" class="w-12 h-1 transition-all duration-500 rounded-full"
                :class="active === i - 1 ? 'bg-white' : 'bg-white/30 hover:bg-white/50'"></button>
        </template>
    </div>

    {{-- Carousel Inner --}}
    <div class="relative w-full h-full min-h-[600px]">
        {{ $slot }}
    </div>

    {{-- Controls --}}
    <button @click="prev()"
        class="absolute left-6 top-1/2 -translate-y-1/2 z-30 p-3 rounded-full bg-black/10 hover:bg-black/20 text-white/50 hover:text-white transition-all opacity-0 group-hover:opacity-100 backdrop-blur-sm">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
        </svg>
    </button>
    <button @click="next()"
        class="absolute right-6 top-1/2 -translate-y-1/2 z-30 p-3 rounded-full bg-black/10 hover:bg-black/20 text-white/50 hover:text-white transition-all opacity-0 group-hover:opacity-100 backdrop-blur-sm">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
        </svg>
    </button>
</section>

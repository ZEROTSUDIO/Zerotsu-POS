@props([
    'active' => false,
    'image' => '',
    'title' => '',
    'subtitle' => '',
    'tags' => [],
])

<div class="carousel-item {{ $active ? 'active' : '' }}">
    <div class="hero"
        style="background-image: linear-gradient(rgba(58, 53, 47, 0.15), rgba(58, 53, 47, 0.25)), url('{{ $image }}')">
        <div class="hero-content">
            <h1>{{ $title }}</h1>
            <p class="hero-subtitle">{{ $subtitle }}</p>
            <div class="tagline">
                @foreach ($tags as $tag)
                    <span>{{ $tag }}</span>
                @endforeach
            </div>
        </div>
    </div>
</div>

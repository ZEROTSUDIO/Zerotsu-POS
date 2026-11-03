<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $defaultTitle = config('app.name', 'Laravel Starter') . ' - Application Starter';
        $defaultDescription = 'A scalable full-stack starter template built with Laravel, Livewire, and Tailwind CSS.';
        $defaultAuthor = config('app.name', 'Laravel Starter');
    @endphp
    <meta name="description" content="{{ $metaDescription ?? $defaultDescription }}">
    <meta name="author" content="{{ $metaAuthor ?? $defaultAuthor }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $canonical ?? url(request()->path()) }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $ogUrl ?? url()->current() }}">
    <meta property="og:title" content="{{ $title ?? $defaultTitle }}">
    <meta property="og:description" content="{{ $metaDescription ?? $defaultDescription }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('img/placeholder.svg') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ $ogUrl ?? url()->current() }}">
    <meta property="twitter:title" content="{{ $title ?? $defaultTitle }}">
    <meta property="twitter:description" content="{{ $metaDescription ?? $defaultDescription }}">
    <meta property="twitter:image" content="{{ $ogImage ?? asset('img/placeholder.svg') }}">

    <title>{{ $title ?? $defaultTitle }}</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">

    @stack('seo')
    @stack('schema')

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex flex-col justify-between">

    <!-- Navigation -->
    <x-front.navbar></x-front.navbar>

    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <x-front.footer></x-front.footer>

    <script>
        // Scroll Reveal Animation
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -100px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.scroll-reveal').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>

</html>

@props(['active' => false])

@php
    $classes = $active
        ? 'nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group bg-blue-600 text-white font-semibold shadow-xs'
        : 'nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group text-slate-300 hover:bg-slate-800 hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} aria-current="{{ $active ? 'page' : 'false' }}">
    {{ $slot }}
</a>

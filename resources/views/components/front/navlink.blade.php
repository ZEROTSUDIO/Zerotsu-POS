@props(['active' => false])
<a {{ $attributes }}
    class="nav-link-hover text-slate-700 text-xs font-semibold tracking-wider uppercase hover:text-blue-600 transition-colors {{ $active ? 'text-blue-600 font-bold' : '' }}"
    aria-current="{{ $active ? 'page' : false }}">
    {{ $slot }}
</a>

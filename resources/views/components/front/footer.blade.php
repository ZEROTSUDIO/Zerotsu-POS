<footer class="bg-slate-900 text-slate-300 pt-16 pb-12 mt-24 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-4 gap-12 mb-12">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <img src="{{ asset('img/logo-white.svg') }}" alt="{{ config('app.name', 'Starter') }}" class="h-8 w-auto">
                </div>
                <p class="text-sm leading-relaxed text-slate-400">
                    A modern, scalable starter kit powered by Laravel, Livewire, and Tailwind CSS. Built for speed, performance, and clean code architecture.
                </p>
            </div>
            <div>
                <h5 class="text-sm font-semibold uppercase tracking-wider text-white mb-4">Quick Links</h5>
                <div class="space-y-2">
                    <a href="{{ url('/') }}" class="block text-sm text-slate-400 hover:text-white transition-colors">Home</a>
                    <a href="{{ url('/about') }}" class="block text-sm text-slate-400 hover:text-white transition-colors">About Us</a>
                    <a href="{{ url('/catalogs') }}" class="block text-sm text-slate-400 hover:text-white transition-colors">Catalog</a>
                    <a href="{{ url('/articles') }}" class="block text-sm text-slate-400 hover:text-white transition-colors">Articles</a>
                </div>
            </div>
            <div>
                <h5 class="text-sm font-semibold uppercase tracking-wider text-white mb-4">Contact Info</h5>
                <div class="space-y-2 text-sm text-slate-400">
                    <p><a href="mailto:contact@example.com" class="hover:text-white transition-colors">contact@example.com</a></p>
                    <p><a href="tel:+1234567890" class="hover:text-white transition-colors">+1 (234) 567-890</a></p>
                    <p class="pt-2 text-xs text-slate-500">
                        123 Enterprise Blvd, Suite 400<br>
                        Tech District, CA 94107
                    </p>
                </div>
            </div>
            <div>
                <h5 class="text-sm font-semibold uppercase tracking-wider text-white mb-4">Follow Us</h5>
                <div class="flex gap-4">
                    <a href="#" class="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 transition-colors" title="Twitter / X">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 transition-colors" title="GitHub">
                        <i class="fa-brands fa-github"></i>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 transition-colors" title="LinkedIn">
                        <i class="fa-brands fa-linkedin"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="pt-8 border-t border-slate-800 text-center text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel Starter') }}. All rights reserved.</p>
        </div>
    </div>
</footer>

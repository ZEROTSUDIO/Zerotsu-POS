<x-front.layout>
    <x-slot:title>About Us - {{ config('app.name', 'Laravel Starter') }}</x-slot:title>

    <section class="pt-32 pb-20 px-6">
        <div class="max-w-4xl mx-auto text-center">
            <span class="text-xs font-semibold text-blue-600 tracking-wider uppercase mb-3 inline-block">
                Company Profile
            </span>

            <h1 class="text-4xl md:text-5xl font-bold text-slate-900 mb-8 tracking-tight">
                About {{ config('app.name', 'Our Company') }}
            </h1>

            <div class="space-y-6 text-lg leading-relaxed text-slate-600 font-normal">
                <p>
                    We build modern, reliable, and high-performance solutions tailored to solve complex engineering and operational challenges. Our platform is designed with modularity, scalability, and developer ergonomics at its core.
                </p>
                <p>
                    Whether you are delivering enterprise applications or bootstrapping new initiatives, our architecture provides a battle-tested foundation with rapid iteration capabilities.
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-16 pt-12 border-t border-slate-200">
                <div>
                    <h3 class="text-3xl md:text-4xl font-bold text-slate-900 mb-1">10+</h3>
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Years Experience</p>
                </div>
                <div>
                    <h3 class="text-3xl md:text-4xl font-bold text-slate-900 mb-1">500+</h3>
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Projects Shipped</p>
                </div>
                <div>
                    <h3 class="text-3xl md:text-4xl font-bold text-slate-900 mb-1">99.9%</h3>
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Uptime SLA</p>
                </div>
                <div>
                    <h3 class="text-3xl md:text-4xl font-bold text-slate-900 mb-1">24/7</h3>
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Dedicated Support</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Visual Showcase -->
    <section class="pb-24 px-6">
        <div class="max-w-6xl mx-auto">
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-slate-100 rounded-2xl overflow-hidden aspect-video border border-slate-200 flex items-center justify-center p-8">
                    <img src="{{ asset('img/placeholder.svg') }}" alt="Engineering Operations" class="w-full h-full object-cover rounded-lg">
                </div>
                <div class="bg-slate-100 rounded-2xl overflow-hidden aspect-video border border-slate-200 flex items-center justify-center p-8">
                    <img src="{{ asset('img/placeholder.svg') }}" alt="Quality Assurance" class="w-full h-full object-cover rounded-lg">
                </div>
            </div>
        </div>
    </section>
</x-front.layout>

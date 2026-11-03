<x-front.layout>
    <x-slot:title>Contact Us - {{ config('app.name', 'Laravel Starter') }}</x-slot:title>

    <section class="pt-32 pb-24 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <span class="text-xs font-semibold text-blue-600 tracking-wider uppercase mb-2 inline-block">Get In Touch</span>
                <h1 class="text-4xl md:text-5xl font-bold text-slate-900 tracking-tight">Contact Us</h1>
                <p class="mt-4 text-slate-600 max-w-xl mx-auto">Have questions or want to learn more? Reach out to our team using the form below or our direct channels.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-12 lg:gap-16">
                <!-- Info Column -->
                <div class="space-y-8 bg-slate-50 p-8 rounded-2xl border border-slate-200">
                    <div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Headquarters</h4>
                        <p class="text-slate-600 leading-relaxed text-sm">
                            123 Enterprise Blvd, Suite 400<br>
                            Tech District, CA 94107<br>
                            United States
                        </p>
                    </div>

                    <div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Direct Inquiries</h4>
                        <div class="space-y-2 text-sm text-slate-600">
                            <p><i class="fa-solid fa-envelope mr-2 text-blue-600"></i> contact@example.com</p>
                            <p><i class="fa-solid fa-phone mr-2 text-blue-600"></i> +1 (234) 567-890</p>
                            <p><i class="fa-solid fa-clock mr-2 text-blue-600"></i> Monday - Friday: 09:00 - 18:00 (EST)</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-200">
                        <h5 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-3">Follow Us</h5>
                        <div class="flex gap-4">
                            <a href="#" class="w-10 h-10 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:text-blue-600 hover:border-blue-600 transition-colors">
                                <i class="fa-brands fa-x-twitter"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:text-blue-600 hover:border-blue-600 transition-colors">
                                <i class="fa-brands fa-github"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:text-blue-600 hover:border-blue-600 transition-colors">
                                <i class="fa-brands fa-linkedin"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Form Column -->
                <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
                    <h4 class="text-xl font-bold text-slate-900 mb-6">Send us a message</h4>

                    @if (session('success'))
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg mb-6 text-sm" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Your Full Name"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="subject" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">Subject</label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" placeholder="Inquiry topic"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            @error('subject')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="message" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">Message</label>
                            <textarea id="message" name="message" rows="5" placeholder="How can we help you?"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full bg-blue-600 text-white font-medium py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors text-sm shadow-sm">
                            Submit Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-front.layout>

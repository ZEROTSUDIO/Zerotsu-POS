<div id="advanced-form" class="page animate-slide-in" x-data>
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-slate-900 mb-2">
            Tambah Catalog
        </h1>
        <p class="text-gray-500">
            ________________
        </p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-slate-900">
                Informasi Catalog
            </h2>
        </div>
        <div class="p-6">
            <form wire:submit.prevent="createCatalogue" method="POST">
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Nama </label>
                    <input wire:model="name" type="text"
                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all"
                        placeholder="Nama Product" />
                    @error('name')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Category</label>
                    <select wire:model="category_id" name="category_id" id="category_id"
                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all">
                        <option>Pilih Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-6">
                    <label for="image" class="block text-sm font-semibold text-slate-900 mb-2">Featured
                        Image</label>
                    <div id="imageUpload" @click="$refs.image.click()"
                        class="relative border-2 border-dashed border-gray-300 rounded-xl p-12 text-center cursor-pointer hover:border-blue-600 hover:bg-gray-50 transition-all duration-300 flex flex-col justify-center items-center overflow-hidden min-h-[250px]">

                        <div wire:loading wire:target="image"
                            class="absolute inset-0 bg-white/90 flex flex-col justify-center items-center z-10 backdrop-blur-sm">
                            <i class="fas fa-circle-notch fa-spin text-4xl text-blue-600 mb-3"></i>
                            <p class="text-gray-600 font-semibold animate-pulse">Uploading Image...</p>
                        </div>

                        @if ($image)
                            <img src="{{ $image->temporaryUrl() }}"
                                class="max-h-[300px] w-full object-contain rounded-lg shadow-sm" />
                            <p class="text-xs text-gray-500 mt-2 bg-white/80 px-3 py-1 rounded-full shadow-sm">Click to
                                change</p>
                        @else
                            <i class="fas fa-cloud-upload-alt text-5xl text-blue-600 mb-4"></i>
                            <h5 class="text-lg font-semibold text-gray-700 mb-2">
                                Click to upload image
                            </h5>
                            <p class="text-sm text-gray-500">
                                PNG, JPG, GIF up to 10MB
                            </p>
                        @endif
                    </div>
                    <input wire:model.live="image" x-ref="image" type="file" id="image" class="hidden"
                        accept="image/*" />
                    @error('image')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                {{-- <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Content</label>
                    <div id="editor" class="rounded-lg border-2 border-gray-200"></div>
                </div> --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-900 mb-2">Deskripsi</label>
                    <textarea wire:model="description" name="description" id="description" cols="30" rows="10"
                        class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all"></textarea>
                    @error('description')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                        class="gradient-gold text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                        <i class="fas fa-check-circle mr-2"></i>Save
                    </button>
                    {{-- <button type="button"
                        class="bg-white border-2 border-gray-200 text-slate-700 px-8 py-3 rounded-xl font-semibold hover:bg-gray-50 transition-all duration-300">
                        <i class="fas fa-save mr-2"></i>Save Draft
                    </button> --}}
                    <button type="reset"
                        class="bg-white border-2 border-gray-200 text-slate-700 px-8 py-3 rounded-xl font-semibold hover:bg-gray-50 transition-all duration-300">
                        <i class="fas fa-times-circle mr-2"></i>Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

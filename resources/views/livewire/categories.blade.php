<div id="table" class="page animate-slide-in" x-data="{ showDeleteModal: false, deleteId: null }">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-slate-900 mb-2">
            Categories
        </h1>
        <p class="text-gray-500">
            @if ($isCreating)
                {{ $categoryId ? 'Edit Category' : 'Add New Category' }}
            @else
                Manage Product Categories
            @endif
        </p>
    </div>

    @if (session()->has('status'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
            <p class="font-medium">{{ session('status') }}</p>
        </div>
    @endif

    @if ($isCreating)
        {{-- FORM VIEW --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden animate-slide-in">
            <div
                class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200 justify-between flex">
                <h2 class="text-lg font-bold text-slate-900">
                    Category Information
                </h2>
                <button wire:click="cancel"
                    class="gradient-gold text-white px-4 py-2 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </button>
            </div>

            <div class="p-6">
                <form wire:submit.prevent="{{ $categoryId ? 'updateCategory' : 'createCategory' }}">

                    {{-- Name Input --}}
                    <x-admin.form.input label="Category Name" model="name" name="name"
                        placeholder="e.g., Tiles, Sanitary, Flooring" />

                    {{-- Description Textarea --}}
                    <x-admin.form.textarea label="Description" model="description" name="description" rows="5"
                        placeholder="Brief description of this category..." />

                    {{-- Image Upload --}}
                    <div class="mb-6">
                        <label for="image" class="block text-sm font-semibold text-slate-900 mb-2">
                            Category Image
                        </label>
                        <div id="imageUpload" @click="$refs.image.click()"
                            class="relative border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-blue-600 hover:bg-gray-50 transition-all duration-300 flex flex-col justify-center items-center overflow-hidden min-h-[250px] group">

                            <div wire:loading wire:target="image"
                                class="absolute inset-0 bg-white/90 flex flex-col justify-center items-center z-10 backdrop-blur-sm">
                                <i class="fas fa-circle-notch fa-spin text-4xl text-blue-600 mb-3"></i>
                                <p class="text-gray-600 font-semibold animate-pulse">Processing Image...</p>
                            </div>

                            @if ($image)
                                <div class="relative w-full h-full flex justify-center items-center">
                                    <img src="{{ $image->temporaryUrl() }}"
                                        class="max-h-[230px] w-full object-contain rounded-lg shadow-sm" />
                                    <div
                                        class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                                        <p
                                            class="text-white font-semibold bg-black/50 px-4 py-2 rounded-full backdrop-blur-sm">
                                            <i class="fas fa-pen mr-2"></i>Change Image
                                        </p>
                                    </div>
                                </div>
                            @elseif($categoryId && \App\Models\Category::find($categoryId)->image)
                                <div class="relative w-full h-full flex justify-center items-center">
                                    <img src="{{ asset('storage/' . \App\Models\Category::find($categoryId)->image) }}"
                                        class="max-h-[230px] w-full object-contain rounded-lg shadow-sm" />
                                    <div
                                        class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                                        <p
                                            class="text-white font-semibold bg-black/50 px-4 py-2 rounded-full backdrop-blur-sm">
                                            <i class="fas fa-pen mr-2"></i>Change Image
                                        </p>
                                    </div>
                                </div>
                            @else
                                <i class="fas fa-cloud-upload-alt text-5xl text-blue-600 mb-4"></i>
                                <h5 class="text-lg font-semibold text-gray-700 mb-2">
                                    Click to upload image
                                </h5>
                                <p class="text-sm text-gray-500">
                                    PNG, JPG, WebP up to 10MB
                                </p>
                            @endif
                        </div>
                        <input wire:model.live="image" x-ref="image" type="file" id="image" class="hidden"
                            accept="image/*" />
                        @error('image')
                            <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-3">
                        <button type="submit"
                            class="gradient-gold text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                            <i class="fas fa-check-circle mr-2"></i>{{ $categoryId ? 'Update' : 'Save' }}
                        </button>
                        <button type="button" wire:click="cancel"
                            class="bg-white border-2 border-gray-200 text-slate-700 px-8 py-3 rounded-xl font-semibold hover:bg-gray-50 transition-all duration-300">
                            <i class="fas fa-times-circle mr-2"></i>Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        {{-- TABLE VIEW --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div
                class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between items-start md:items-center">
                <h2 class="text-lg font-bold text-slate-900">
                    All Categories
                </h2>
                <button wire:click="addCategory"
                    class="gradient-gold text-white px-4 py-2 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105 w-full md:w-auto">
                    <i class="fas fa-plus-circle mr-2"></i>Add New
                </button>
            </div>

            {{-- Filters --}}
            <div class="bg-gray-50 rounded-xl p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Search</label>
                        <input type="text"
                            class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all"
                            wire:model.live.debounce.300ms="search" placeholder="Search categories..." />
                    </div>
                    <div class="flex flex-wrap items-end gap-2 mt-4 lg:mt-0">
                        <button wire:click="$refresh"
                            class="flex-1 md:flex-none flex items-center justify-center gradient-gold text-white px-6 py-2.5 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-300">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                        <button wire:click="resetFilters"
                            class="flex-1 md:flex-none flex items-center justify-center bg-white border-2 border-gray-200 text-slate-700 px-6 py-2.5 rounded-lg font-semibold hover:bg-gray-50 transition-all duration-300">
                            <i class="fas fa-redo mr-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead class="bg-gradient-to-r from-marble-gray to-marble-white">
                        <tr>
                            <th
                                class="hidden sm:table-cell px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider w-20">
                                ID
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Thumbnail
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Name
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Slug
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Description
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                                <td class="hidden sm:table-cell px-6 py-4 text-sm text-gray-800">{{ $category->id }}
                                </td>
                                <td class="px-6 py-4">
                                    <img src="{{ $category->image ? asset('storage/' . $category->image) : asset('img/default.jpg') }}"
                                        alt="{{ $category->name }}"
                                        class="w-16 h-16 object-cover rounded-md shadow-sm">
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $category->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $category->slug }}</code>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 truncate max-w-xs">
                                    {{ $category->description ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <button wire:click="editCategory({{ $category->id }})"
                                        class="text-blue-600 hover:text-blue-800 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="showDeleteModal = true; deleteId = {{ $category->id }}"
                                        class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <i class="fas fa-folder-open text-5xl mb-4"></i>
                                    <p class="text-lg">No categories found</p>
                                    <p class="text-sm">Create your first category to get started</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        {{ $categories->links('vendor.pagination.table-pagination') }}
    @endif

    {{-- Delete Confirmation Modal --}}
    <x-admin.delete-modal method="deleteCategory" message="Are you sure you want to delete this category?" />
</div>

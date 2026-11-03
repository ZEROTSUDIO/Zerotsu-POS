<div id="table" class="page animate-slide-in" x-data="{ showDeleteModal: false, deleteId: null }">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-slate-900 mb-2">
            Catalogue
        </h1>
        <p class="text-gray-500">
            @if ($isCreating)
                {{ $catalogueId ? 'Edit Data Product' : 'Tambah Data Product' }}
            @else
                Data Tabel Product
            @endif
        </p>
    </div>

    @if ($isCreating)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden animate-slide-in">
            <div
                class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200 justify-between flex">
                <h2 class="text-lg font-bold text-slate-900">
                    Informasi Catalog
                </h2>
                <button wire:click="cancel"
                    class="gradient-gold text-white px-4 py-2 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </button>
            </div>
            <div class="p-6">
                <form wire:submit.prevent="{{ $catalogueId ? 'updateCatalogue' : 'createCatalogue' }}">
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Kode </label>
                        <input wire:model="code" type="text"
                            class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all"
                            placeholder="Kode Product" />
                        @error('code')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
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
                        <select wire:model="category_id"
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
                    {{-- Product Images Section --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">
                            Product Images
                            <span class="text-xs text-gray-500">(First image will be featured - drag to reorder)</span>
                        </label>

                        {{-- Upload Box --}}
                        <div @click="$refs.images.click()"
                            class="relative border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-blue-600 hover:bg-gray-50 transition-all duration-300">

                            {{-- Loading Indicator --}}
                            <div wire:loading wire:target="images"
                                class="absolute inset-0 bg-white/90 flex flex-col justify-center items-center z-10 backdrop-blur-sm rounded-xl">
                                <i class="fas fa-circle-notch fa-spin text-4xl text-blue-600 mb-3"></i>
                                <p class="text-gray-600 font-semibold animate-pulse">Processing Images...</p>
                            </div>

                            <i class="fas fa-images text-5xl text-blue-600 mb-4"></i>
                            <h5 class="text-lg font-semibold text-gray-700 mb-2">
                                Click to upload images
                            </h5>
                            <p class="text-sm text-gray-500">
                                Multiple images allowed (PNG, JPG up to 10MB each)
                            </p>
                        </div>

                        {{-- Hidden File Input --}}
                        <input type="file" wire:model="images" multiple accept="image/*" class="hidden"
                            x-ref="images" />

                        {{-- Validation Errors --}}
                        @error('images.*')
                            <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                        @enderror

                        {{-- Display Images (Existing + New Uploads) --}}
                        @if (($catalogueId && !empty($existingImages) && count($existingImages) > 0) || !empty($newImages))
                            <div class="mt-6" x-data="imageSorter()">
                                <div class="flex justify-between items-center mb-3">
                                    <p class="text-sm font-semibold text-gray-700">
                                        Images ({{ count($existingImages) + count($newImages) }})
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-grip-vertical mr-1"></i>Drag to reorder
                                    </p>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4" @dragover.prevent @drop.prevent>

                                    {{-- Existing Images (Edit Mode) --}}
                                    @if ($catalogueId && count($existingImages) > 0)
                                        @foreach ($existingImages as $index => $img)
                                            <div class="relative group cursor-move" draggable="true"
                                                data-id="existing-{{ $img->id }}"
                                                @dragstart="dragStart($event, 'existing-{{ $img->id }}')"
                                                @dragend="dragEnd($event)" @dragover="dragOver($event)"
                                                @drop="drop($event, 'existing-{{ $img->id }}')"
                                                wire:key="existing-{{ $img->id }}">

                                                {{-- Image Preview --}}
                                                <img src="{{ asset('storage/' . $img->image_path) }}"
                                                    class="h-40 w-full object-cover rounded-xl border-2 shadow-sm transition-all duration-200"
                                                    :class="dragging ? 'border-blue-600' : 'border-gray-200'">

                                                {{-- Delete Button --}}
                                                <button type="button" wire:click="deleteImage({{ $img->id }})"
                                                    wire:confirm="Are you sure you want to delete this image?"
                                                    class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white p-2 rounded-full shadow-md transition-all opacity-0 group-hover:opacity-100">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>

                                                {{-- Featured Badge --}}
                                                @if ($index === 0)
                                                    <span
                                                        class="absolute top-2 left-2 bg-blue-600/90 text-white text-[10px] uppercase font-bold px-2 py-1 rounded shadow-sm">
                                                        Featured
                                                    </span>
                                                @endif

                                                {{-- Drag Handle --}}
                                                <div
                                                    class="absolute bottom-2 right-2 bg-white/90 text-gray-600 p-1.5 rounded shadow opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <i class="fas fa-grip-vertical text-xs"></i>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    {{-- New Uploaded Images (Before Submission) --}}
                                    @foreach ($newImages as $index => $img)
                                        <div class="relative group cursor-move animate-fade-in-up" draggable="true"
                                            data-id="new-{{ $img['id'] }}"
                                            @dragstart="dragStart($event, 'new-{{ $img['id'] }}')"
                                            @dragend="dragEnd($event)" @dragover="dragOver($event)"
                                            @drop="drop($event, 'new-{{ $img['id'] }}')"
                                            wire:key="new-{{ $img['id'] }}">

                                            <img src="{{ $img['temp_url'] }}"
                                                class="h-40 w-full object-cover rounded-xl border-2 shadow-sm transition-all duration-200"
                                                :class="dragging ? 'border-blue-600' : 'border-gray-200'">

                                            <!-- Remove Button -->
                                            <button type="button" wire:click="removeNewImage('{{ $img['id'] }}')"
                                                class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white p-2 rounded-full shadow-md transition-all opacity-0 group-hover:opacity-100">
                                                <i class="fas fa-times text-xs"></i>
                                            </button>

                                            <!-- Featured badge -->
                                            @if ($index === 0 && (!$existingImages || count($existingImages) === 0))
                                                <span
                                                    class="absolute top-2 left-2 bg-blue-600/90 text-white text-[10px] uppercase font-bold px-2 py-1 rounded shadow-sm">
                                                    Featured
                                                </span>
                                            @endif

                                            <!-- New badge -->
                                            <span
                                                class="absolute bottom-2 left-2 bg-blue-500/90 text-white text-[10px] uppercase font-bold px-2 py-1 rounded shadow-sm">
                                                New
                                            </span>

                                            <!-- Drag handle -->
                                            <div
                                                class="absolute bottom-2 right-2 bg-white/90 text-gray-600 p-1.5 rounded shadow opacity-0 group-hover:opacity-100 transition-opacity">
                                                <i class="fas fa-grip-vertical text-xs"></i>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        @endif

                        {{-- Empty State --}}
                        @if (empty($newImages) && (!$catalogueId || empty($existingImages) || count($existingImages) === 0))
                            <div
                                class="mt-4 text-center py-6 text-gray-400 border-2 border-dashed border-gray-200 rounded-xl">
                                <i class="fas fa-image text-3xl mb-2"></i>
                                <p class="text-sm">No images uploaded yet</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Deskripsi</label>
                        <textarea wire:model="description" cols="30" rows="10"
                            class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all"></textarea>
                        @error('description')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Product Specifications Section --}}
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <label class="block text-sm font-semibold text-slate-900">Product Specifications</label>
                            <button type="button" wire:click="addSpecification"
                                class="text-sm gradient-gold text-white px-4 py-2 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                                <i class="fas fa-plus-circle mr-1"></i>Add Specification
                            </button>
                        </div>

                        <div class="space-y-3">
                            <div class="space-y-3" x-data="specSorter()">
                                @foreach ($specifications as $index => $spec)
                                    <div wire:key="spec-{{ $index }}" data-index="{{ $index }}"
                                        draggable="true" @dragstart="dragStart($event, {{ $index }})"
                                        @dragend="dragEnd($event)" @dragover="dragOver($event)"
                                        @drop="drop($event, {{ $index }})"
                                        class="flex gap-3 items-start p-4 bg-gray-50 rounded-xl border-2 border-gray-200 hover:border-blue-600/50 transition-all duration-300 cursor-move">
                                        <div class="mt-2 text-gray-400">
                                            <i class="fas fa-grip-vertical"></i>
                                        </div>
                                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <input type="text"
                                                    wire:model="specifications.{{ $index }}.key"
                                                    placeholder="Specification Name (e.g., Color, size, etc.)"
                                                    class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all text-sm" />
                                                @error('specifications.' . $index . '.key')
                                                    <span
                                                        class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <input type="text"
                                                    wire:model="specifications.{{ $index }}.value"
                                                    placeholder="Values"
                                                    class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all text-sm" />
                                                @error('specifications.' . $index . '.value')
                                                    <span
                                                        class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <button type="button" wire:click="removeSpecification({{ $index }})"
                                            class="mt-0.5 text-red-600 hover:text-red-800 hover:bg-red-50 p-2.5 rounded-lg transition-all duration-300"
                                            title="Remove specification">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endforeach

                                @if (empty($specifications))
                                    <div class="text-center py-8 text-gray-400">
                                        <i class="fas fa-list-ul text-3xl mb-2"></i>
                                        <p class="text-sm">No specifications added yet. Click "Add Specification" to
                                            start.
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <p class="text-xs text-gray-500 mt-3 mb-3">
                                <i class="fas fa-grip-vertical mr-1"></i>Drag the specification row to reorder.
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                class="gradient-gold text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                                <i class="fas fa-check-circle mr-2"></i>{{ $catalogueId ? 'Update' : 'Save' }}
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
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div
                class="bg-gradient-to-r from-marble-gray to-marble-white px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h2 class="text-lg font-bold text-slate-900">
                    Catalogue
                </h2>
                <button wire:click="addCatalogue"
                    class="gradient-gold text-white px-4 py-2 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105 w-full md:w-auto">
                    <i class="fas fa-plus-circle mr-2"></i>Add New
                </button>
            </div>
            <!-- Filters -->
            <div class="bg-gray-50 rounded-xl p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Search</label>
                        <input type="text"
                            class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all"
                            wire:model.live.debounce.300ms="search" placeholder="Search products..." />
                    </div>
                    {{-- <div>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Date</label>
                        <input type="date"
                            class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all" />
                    </div> --}}
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Category</label>
                        <select wire:model.live="categoryFilter"
                            class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2 mt-4 lg:mt-0">
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
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-marble-gray to-marble-white">
                        <tr>
                            <th
                                class="hidden sm:table-cell px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider w-20">
                                Kode</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Nama</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Kategori</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Gambar</th>
                            <th
                                class="hidden md:table-cell px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider w-1/3">
                                Deskripsi</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Featured</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($catalogues as $catalogue)
                            <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                                <td class="hidden sm:table-cell px-6 py-4 text-sm text-gray-800">
                                    {{ $catalogue->code }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $catalogue->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $catalogue->category->name ?? 'Uncategorized' }}</td>
                                <td class="px-6 py-4">
                                    <img src="{{ $catalogue->image ? asset('storage/' . $catalogue->image) : asset('img/default.jpg') }}"
                                        alt="{{ $catalogue->name }}"
                                        class="w-16 h-16 md:w-24 md:h-24 object-cover rounded-md shadow-sm">
                                </td>
                                <td class="hidden md:table-cell px-6 py-4 text-sm text-gray-600 truncate max-w-xs">
                                    {{ $catalogue->description }}</td>
                                <td class="px-6 py-4 text-center">
                                    <button wire:click="toggleFeatured({{ $catalogue->id }})"
                                        class="transition-all duration-300 hover:scale-125"
                                        title="{{ $catalogue->is_featured ? 'Remove from featured' : 'Mark as featured' }}">
                                        <i
                                            class="fas fa-star {{ $catalogue->is_featured ? 'text-yellow-500' : 'text-gray-300' }} text-xl"></i>
                                    </button>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button class="text-yellow-600 hover:text-yellow-800 transition-colors"
                                            title="View"><a
                                                href="{{ url('/catalogs/' . $catalogue->category->slug . '/' . $catalogue->slug) }}"
                                                target="_blank"><i class="fas fa-eye"></i></a></button>
                                        <button wire:click="editCatalogue({{ $catalogue->id }})"
                                            class="text-blue-600 hover:text-blue-800 transition-colors"
                                            title="Edit"><i class="fas fa-edit"></i></button>
                                        <button wire:click="duplicateCatalogue({{ $catalogue->id }})"
                                            wire:confirm="Duplicate this product?"
                                            class="text-green-600 hover:text-green-800 transition-colors"
                                            title="Duplicate"><i class="fas fa-copy"></i></button>
                                        <button @click="showDeleteModal = true; deleteId = {{ $catalogue->id }}"
                                            class="text-red-600 hover:text-red-800 transition-colors"
                                            title="Delete"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            <div class="mt-6 px-6 pb-6">
                {{ $catalogues->links('vendor.pagination.table-pagination') }}
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    <x-admin.delete-modal method="deleteCatalogue" message="Are you sure you want to delete this product?" />

    @script
        <script>
            Alpine.data('imageSorter', () => ({
                draggedId: null,
                dragging: false,

                dragStart(event, id) {
                    this.draggedId = id;
                    this.dragging = true;
                    event.dataTransfer.effectAllowed = 'move';
                    event.target.classList.add('opacity-50');
                },

                dragEnd(event) {
                    this.dragging = false;
                    event.target.classList.remove('opacity-50');
                },

                dragOver(event) {
                    event.preventDefault();
                    event.dataTransfer.dropEffect = 'move';
                },

                drop(event, targetId) {
                    event.preventDefault();
                    if (this.draggedId === targetId) return;

                    const container = event.target.closest('[x-data]');
                    const items = Array.from(container.querySelectorAll('[data-id]'));

                    const draggedIndex = items.findIndex(el => el.getAttribute('data-id') === this.draggedId);
                    const targetIndex = items.findIndex(el => el.getAttribute('data-id') === targetId);

                    if (draggedIndex !== -1 && targetIndex !== -1) {
                        const draggedEl = items[draggedIndex];
                        const targetEl = items[targetIndex];

                        if (draggedIndex < targetIndex) {
                            targetEl.parentNode.insertBefore(draggedEl, targetEl.nextSibling);
                        } else {
                            targetEl.parentNode.insertBefore(draggedEl, targetEl);
                        }

                        // Get new order
                        const newOrder = Array.from(container.querySelectorAll('[data-id]'))
                            .map(el => el.getAttribute('data-id'));

                        $wire.reorderImages(newOrder);
                    }
                }
            }));

            Alpine.data('specSorter', () => ({
                draggedIndex: null,
                dragging: false,

                dragStart(event, index) {
                    this.draggedIndex = index;
                    this.dragging = true;
                    event.dataTransfer.effectAllowed = 'move';
                    event.target.classList.add('opacity-40');
                },

                dragEnd(event) {
                    this.dragging = false;
                    event.target.classList.remove('opacity-40');
                },

                dragOver(event) {
                    event.preventDefault();
                    event.dataTransfer.dropEffect = 'move';
                },

                drop(event, targetIndex) {
                    event.preventDefault();
                    if (this.draggedIndex === targetIndex) return;

                    const container = event.target.closest('[x-data]');
                    const items = Array.from(container.querySelectorAll('[data-index]'));

                    const draggedEl = items.find(el => el.getAttribute('data-index') == this.draggedIndex);
                    const targetEl = items.find(el => el.getAttribute('data-index') == targetIndex);

                    if (draggedEl && targetEl) {
                        if (parseInt(this.draggedIndex) < parseInt(targetIndex)) {
                            targetEl.parentNode.insertBefore(draggedEl, targetEl.nextSibling);
                        } else {
                            targetEl.parentNode.insertBefore(draggedEl, targetEl);
                        }

                        const newOrder = Array.from(container.querySelectorAll('[data-index]'))
                            .map(el => parseInt(el.getAttribute('data-index')));

                        $wire.reorderSpecifications(newOrder);
                    }
                }
            }));
        </script>
    @endscript
</div>

<div class="page animate-slide-in" x-data="{ showDeleteModal: false, deleteId: null }">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-slate-900 mb-2">
            Articles
        </h1>
        <p class="text-gray-500">
            @if ($isCreating)
                {{ $articleId ? 'Edit Data Artikel' : 'Tambah Data Artikel' }}
            @else
                Data Tabel Artikel
            @endif
        </p>
    </div>

    @if ($isCreating)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden animate-slide-in">
            <div
                class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200 justify-between flex">
                <h2 class="text-lg font-bold text-slate-900">Informasi Artikel</h2>
                <button wire:click="cancel"
                    class="gradient-gold text-white px-4 py-2 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </button>
            </div>
            <div class="p-6">
                <form wire:submit.prevent="saveArticle">
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-semibold text-slate-900 mb-2">Judul
                            Artikel</label>
                        <input type="text" id="title" wire:model="title"
                            class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all"
                            placeholder="Judul Artikel">
                        @error('title')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="topic" class="block text-sm font-semibold text-slate-900 mb-2">Topic</label>
                        <select wire:model="topic_id"
                            class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all">
                            <option value="">Pilih Option</option>
                            @foreach ($topics as $topic)
                                <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                            @endforeach
                        </select>
                        @error('topic_id')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Featured Image Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Featured Image</label>

                        {{-- Upload Box --}}
                        <div @click="$refs.imageInput.click()"
                            class="relative border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-blue-600 hover:bg-gray-50 transition-all duration-300">

                            {{-- Loading Indicator --}}
                            <div wire:loading wire:target="image"
                                class="absolute inset-0 bg-white/90 flex flex-col justify-center items-center z-10 backdrop-blur-sm rounded-xl">
                                <i class="fas fa-circle-notch fa-spin text-3xl text-blue-600 mb-2"></i>
                                <p class="text-xs text-gray-600 font-semibold animate-pulse">Uploading...</p>
                            </div>

                            @if ($imagePreview || $existingImage)
                                <div class="relative group mx-auto w-full max-w-2xl">
                                    <img src="{{ $imagePreview ?: asset('storage/' . $existingImage) }}"
                                        class="w-full h-auto object-cover rounded-lg shadow-md border-2 border-gray-100">
                                    <button type="button" wire:click="removeImage"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white p-1.5 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            @else
                                <i class="fas fa-cloud-upload-alt text-4xl text-blue-600 mb-3"></i>
                                <h5 class="text-sm font-semibold text-gray-700">Click to upload featured image
                                </h5>
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG up to 5MB</p>
                            @endif
                        </div>

                        <input type="file" wire:model="image" class="hidden" x-ref="imageInput" accept="image/*">
                        @error('image')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Content Editor -->
                    <div class="mb-6" wire:ignore>
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Isi Artikel</label>
                        <div x-data="{
                            content: @entangle('content').live,
                            editor: null,
                            init() {
                                const initEditor = () => {
                                    if (!window.tinymce) {
                                        setTimeout(initEditor, 100);
                                        return;
                                    }
                        
                                    window.tinymce.init({
                                        target: this.$refs.tinymceEditor,
                                        license_key: 'gpl',
                                        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table wordcount quickbars',
                                        toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | forecolor backcolor removeformat | code fullscreen',
                                        menubar: 'file edit view insert format tools table',
                                        height: 500,
                                        promotion: false,
                                        branding: false,
                                        image_advtab: true,
                                        image_caption: true,
                                        object_resizing: true,
                                        resize: true,
                                        paste_data_images: true,
                                        automatic_uploads: true,
                                        file_picker_types: 'image',
                                        file_picker_callback: (cb, value, meta) => {
                                            const input = document.createElement('input');
                                            input.setAttribute('type', 'file');
                                            input.setAttribute('accept', 'image/*');
                                            input.onchange = function() {
                                                const file = this.files[0];
                                                const reader = new FileReader();
                                                reader.onload = function() {
                                                    cb(reader.result, { title: file.name });
                                                };
                                                reader.readAsDataURL(file);
                                            };
                                            input.click();
                                        },
                                        setup: (editor) => {
                                            this.editor = editor;
                        
                                            editor.on('init', () => {
                                                if (this.content) {
                                                    editor.setContent(this.content);
                                                }
                                            });
                        
                                            editor.on('Change Input Undo Redo', () => {
                                                this.content = editor.getContent();
                                            });
                                        }
                                    });
                                };
                                initEditor();
                            },
                            destroy() {
                                if (this.editor) {
                                    this.editor.destroy();
                                }
                            }
                        }">
                            <textarea x-ref="tinymceEditor" style="min-height: 400px;"></textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
                        <button type="button" wire:click="cancel"
                            class="bg-white border-2 border-gray-200 text-gray-700 px-6 py-2.5 rounded-lg font-semibold hover:bg-gray-50 transition-all">
                            Batal
                        </button>
                        <button type="button" wire:click="saveArticle('draft')"
                            class="bg-gray-100 border border-gray-200 text-gray-700 px-8 py-2.5 rounded-lg font-semibold shadow-sm hover:bg-gray-200 transition-all duration-300">
                            <i class="fas fa-file-alt mr-2"></i>Simpan Sebagai Draft
                        </button>
                        <button type="button" wire:click="saveArticle('published')"
                            class="gradient-gold text-white px-8 py-2.5 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                            <i class="fas fa-paper-plane mr-2"></i>Publish Artikel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <!-- Table View -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden animate-slide-in">
            <div
                class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-900">Daftar Artikel</h2>
                <button wire:click="addArticle"
                    class="gradient-gold text-white px-4 py-2 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                    <i class="fas fa-plus mr-2"></i>Tambah Artikel
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Image</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Judul Artikel</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Topic</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Created At</th>
                            <th
                                class="px-6 py-4 text-center text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($articles as $article)
                            <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="w-16 h-12 rounded overflow-hidden shadow-sm border border-gray-100">
                                        <img src="{{ $article->image ? asset('storage/' . $article->image) : asset('img/placeholder-article.jpg') }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $article->title }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        {{ Str::limit(strip_tags($article->body), 60) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-200">
                                        {{ $article->topic->name ?? 'Uncategorized' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($article->status === App\Enums\ArticleStatus::Published)
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                            <i class="fas fa-check-circle mr-1"></i>Published
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 border border-yellow-200">
                                            <i class="fas fa-pencil-alt mr-1"></i>Draft
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ $article->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex justify-center gap-2">
                                        <button class="text-yellow-600 hover:text-yellow-800 transition-colors"
                                            title="View"><a href="{{ url('/articles/' . $article->slug) }}"
                                                target="_blank"><i class="fas fa-eye"></i></a></button>
                                        <button wire:click="editArticle({{ $article->id }})"
                                            class="text-blue-600 hover:text-blue-800 transition-colors"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button @click="showDeleteModal = true; deleteId = {{ $article->id }}"
                                            class="text-red-600 hover:text-red-800 transition-colors" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-newspaper text-4xl text-gray-200 mb-3"></i>
                                        <p class="text-gray-400 font-medium">Belum ada data artikel</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($articles->hasPages())
                <div class="mt-6 px-6 pb-6">
                    {{ $articles->links('vendor.pagination.table-pagination') }}
                </div>
            @endif
        </div>
    @endif

    {{-- Delete Modal Placeholder --}}
    <x-admin.delete-modal method="deleteArticle" message="Apakah Anda yakin ingin menghapus artikel ini?" />

</div>

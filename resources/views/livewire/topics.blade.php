<div id="table" class="page animate-slide-in" x-data="{ showDeleteModal: false, deleteId: null }">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-slate-900 mb-2">
            topics
        </h1>
        <p class="text-gray-500">
            @if ($isCreating)
                {{ $topicId ? 'Edit topic' : 'Add New topic' }}
            @else
                Manage Article topics
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
                    topic Information
                </h2>
                <button wire:click="cancel"
                    class="gradient-gold text-white px-4 py-2 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </button>
            </div>

            <div class="p-6">
                <form wire:submit.prevent="{{ $topicId ? 'updatetopic' : 'createtopic' }}">

                    {{-- Name Input --}}
                    <x-admin.form.input label="topic Name" model="name" name="name"
                        placeholder="e.g., Tiles, Sanitary, Flooring" />

                    {{-- Action Buttons --}}
                    <div class="flex gap-3">
                        <button type="submit"
                            class="gradient-gold text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                            <i class="fas fa-check-circle mr-2"></i>{{ $topicId ? 'Update' : 'Save' }}
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
                    All topics
                </h2>
                <button wire:click="addtopic"
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
                            wire:model.live.debounce.300ms="search" placeholder="Search topics..." />
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
                                Name
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Slug
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($topics as $topic)
                            <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                                <td class="hidden sm:table-cell px-6 py-4 text-sm text-gray-800">{{ $topic->id }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $topic->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $topic->slug }}</code>
                                </td>
                                <td class="px-6 py-4">
                                    <button wire:click="edittopic({{ $topic->id }})"
                                        class="text-blue-600 hover:text-blue-800 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="showDeleteModal = true; deleteId = {{ $topic->id }}"
                                        class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <i class="fas fa-folder-open text-5xl mb-4"></i>
                                    <p class="text-lg">No topics found</p>
                                    <p class="text-sm">Create your first topic to get started</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        {{ $topics->links('vendor.pagination.table-pagination') }}
    @endif

    {{-- Delete Confirmation Modal --}}
    <x-admin.delete-modal method="deletetopic" message="Are you sure you want to delete this topic?" />
</div>

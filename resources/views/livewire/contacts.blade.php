<div id="table" class="page animate-slide-in" x-data="{ showDeleteModal: false, deleteId: null }">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-slate-900 mb-2">
            Messages
        </h1>
        <p class="text-gray-500">
            Manage Contact Messages
        </p>
    </div>

    @if (!$viewingContact)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div
                class="bg-linear-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between items-start md:items-center">
                <h2 class="text-lg font-bold text-slate-900">
                    All Messages
                </h2>
            </div>

            <div class="bg-gray-50 rounded-xl p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-slate-900 mb-2">Search</label>
                        <input type="text"
                            class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all"
                            wire:model.live.debounce.300ms="search" placeholder="Search by name..." />
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

            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead class="bg-linear-to-r from-marble-gray to-marble-white">
                        <tr>
                            <th
                                class="hidden sm:table-cell px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider w-20">
                                ID
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Contact Info
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Subject
                            </th>
                            <th
                                class="px-6 py-4 text-xs font-bold text-slate-900 uppercase tracking-wider text-center">
                                Status
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Date
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($contacts as $contact)
                            <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                                <td class="hidden sm:table-cell px-6 py-4 text-sm text-gray-800">{{ $contact->id }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $contact->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $contact->email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $contact->subject }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($contact->status === 'new')
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">New</span>
                                    @else
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Replied</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $contact->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <button wire:click="seeDetails({{ $contact->id }})"
                                        class="text-blue-600 hover:text-blue-800 mr-3 transition-colors">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button @click="showDeleteModal = true; deleteId = {{ $contact->id }}"
                                        class="text-red-600 hover:text-red-800 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <i class="fas fa-folder-open text-5xl mb-4"></i>
                                    <p class="text-lg">No messages found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($contacts->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $contacts->links('vendor.pagination.table-pagination') }}
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden animate-slide-in">
            <!-- Header -->
            <div
                class="bg-linear-to-r from-gray-50 to-gray-100 px-8 py-6 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Message Details</h2>
                    <p class="text-sm text-gray-500">ID: #{{ $viewingContact->id }} | Received:
                        {{ $viewingContact->created_at->format('M d, Y H:i') }} </p>
                </div>
                <button wire:click="closeDetails"
                    class="bg-white border-2 border-gray-200 text-gray-600 px-4 py-2 rounded-lg font-semibold hover:bg-gray-50 transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </button>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column: Contact info & Original Message -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Original Message
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">From</p>
                                    <p class="text-slate-900 font-bold">{{ $viewingContact->name }}</p>
                                    <p class="text-sm text-blue-600">{{ $viewingContact->email }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase">Subject</p>
                                    <p class="text-slate-900 font-bold">{{ $viewingContact->subject }}</p>
                                </div>
                            </div>

                            <div
                                class="prose max-w-none text-gray-700 bg-white p-4 rounded-lg border border-gray-100 min-h-[150px]">
                                {!! nl2br(e($viewingContact->message)) !!}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 px-6">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">IP Address</p>
                                <p class="text-slate-900 font-bold">{{ $viewingContact->ip_address }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">User Agent</p>
                                <p class="text-slate-900 font-bold">{{ $viewingContact->user_agent }}</p>
                            </div>
                        </div>

                        <!-- Conversation History -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest flex items-center">
                                <i class="fas fa-history mr-2"></i> Conversation History
                            </h3>

                            @forelse ($viewingContact->replies as $reply)
                                <div class="flex flex-col {{ $reply->admin_id ? 'items-end' : 'items-start' }}">
                                    <div
                                        class="max-w-[80%] rounded-2xl p-4 shadow-sm {{ $reply->admin_id ? 'bg-blue-50 border border-blue-200 text-slate-900' : 'bg-gray-50 border border-gray-200' }}">
                                        <div class="flex items-center justify-between mb-2 gap-4">
                                            <span
                                                class="text-xs font-bold opacity-70">{{ $reply->admin?->name ?? 'System' }}
                                                (Admin)
                                            </span>
                                            <span
                                                class="text-[10px] opacity-60">{{ $reply->created_at->format('M d, H:i') }}</span>
                                        </div>
                                        <p class="text-sm whitespace-pre-line">{{ $reply->message }}</p>
                                        @if ($reply->sent_via_email)
                                            <div class="mt-2 text-[10px] opacity-50 flex items-center">
                                                <i class="fas fa-envelope mr-1"></i> Sent via Email
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-gray-400 italic text-sm">
                                    No replies yet.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Right Column: Reply Form -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-8">
                            <form wire:submit.prevent="sendReply"
                                class="bg-white rounded-xl border border-gray-200 shadow-md p-6">
                                <h3 class="text-lg font-bold text-slate-900 mb-4">Send Reply</h3>

                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-600 mb-2">Message</label>
                                    <textarea wire:model="replyMessage"
                                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all min-h-[200px] text-sm"
                                        placeholder="Type your reply here..."></textarea>
                                    @error('replyMessage')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button type="submit" wire:loading.attr="disabled"
                                    class="w-full gradient-gold text-white px-6 py-3 rounded-xl font-bold shadow-md hover:shadow-lg transition-all duration-300 flex items-center justify-center">
                                    <span wire:loading.remove>
                                        <i class="fas fa-paper-plane mr-2"></i>Send Reply
                                    </span>
                                    <span wire:loading>
                                        <i class="fas fa-spinner fa-spin mr-2"></i>Sending...
                                    </span>
                                </button>

                                <p class="text-[10px] text-gray-400 mt-4 text-center">
                                    Note: Sending this message will also email it to the user.
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    <x-admin.delete-modal method="deleteContact" message="Are you sure you want to delete this contact?" />
</div>

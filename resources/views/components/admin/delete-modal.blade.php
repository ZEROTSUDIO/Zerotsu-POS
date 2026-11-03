@props(['method', 'message' => 'Are you sure you want to delete this item?'])

{{-- Delete Confirmation Modal --}}
<div x-show="showDeleteModal" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/50" @click="showDeleteModal = false"></div>

    {{-- Modal Content --}}
    <div class="relative p-4 w-full max-w-md max-h-full" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">
        <div class="relative bg-white border border-gray-200 rounded-xl shadow-lg p-6">
            <button type="button"
                class="absolute top-3 end-2.5 text-gray-500 bg-transparent hover:bg-gray-100 hover:text-gray-700 rounded-lg text-sm w-9 h-9 ms-auto inline-flex justify-center items-center"
                @click="showDeleteModal = false">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18 17.94 6M18 18 6.06 6" />
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
            <div class="p-4 text-center">
                <svg class="mx-auto mb-4 text-red-400 w-12 h-12" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 13V8m0 8h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <h3 class="mb-6 text-gray-700 text-lg font-medium">{{ $message }}
                </h3>
                <div class="flex items-center gap-4 justify-center">
                    <button
                        @click="if(deleteId !== null) { $wire.{{ $method }}(deleteId); showDeleteModal = false; }"
                        type="button"
                        class="text-white bg-blue-600 hover:bg-blue-600/80 focus:ring-4 focus:ring-blue-600/20 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">
                        Yes, I'm sure
                    </button>
                    <button @click="showDeleteModal = false" type="button"
                        class="text-gray-700 bg-gray-100 border border-gray-200 hover:bg-gray-200 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">
                        No, cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

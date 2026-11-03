@props([
    'label' => null,
    'name' => null,
    'model' => null,
    'rows' => '10',
])

<div class="mb-6">
    @if ($label)
        <label class="block text-sm font-semibold text-slate-900 mb-2">{{ $label }}</label>
    @endif
    <textarea @if ($model) wire:model="{{ $model }}" @endif
        @if ($name) name="{{ $name }}" @endif rows="{{ $rows }}"
        {{ $attributes->merge(['class' => 'w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none transition-all']) }}></textarea>
    @if ($name)
        @error($name)
            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
        @enderror
    @endif
</div>

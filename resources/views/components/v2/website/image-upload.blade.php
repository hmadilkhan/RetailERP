@props([
    'name',
    'label',
    'previewId',
    'src' => null,
])

<label class="block rounded-lg border border-erp-line bg-slate-50 p-4">
    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">{{ $label }}</span>
    <div class="mt-3 flex items-center gap-4">
        <img id="{{ $previewId }}" src="{{ $src ?: asset('storage/images/placeholder.jpg') }}" class="h-24 w-24 rounded-lg object-cover ring-1 ring-slate-200" alt="{{ $label }}">
        <div class="min-w-0 flex-1">
            <input type="file" name="{{ $name }}" class="w-full rounded-lg border border-erp-line bg-white text-sm text-erp-text file:mr-4 file:border-0 file:bg-erp file:px-4 file:py-2 file:text-sm file:font-bold file:text-white" accept="image/*">
            @error($name) <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
        </div>
    </div>
</label>

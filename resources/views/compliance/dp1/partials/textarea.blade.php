<label class="grid gap-2 text-sm text-slate-300">
    <span>{{ $label }}</span>
    <textarea name="{{ $name }}" rows="4" {{ ($required ?? false) ? 'required' : '' }} class="border border-slate-700 bg-slate-900 px-4 py-3 text-white">{{ old($name) }}</textarea>
    @error($name)<span class="text-rose-300">{{ $message }}</span>@enderror
</label>
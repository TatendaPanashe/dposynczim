<label class="grid gap-2 text-sm text-slate-300">
    <span>{{ $label }}</span>
    <input name="{{ $name }}" type="{{ $type ?? 'text' }}" value="{{ old($name) }}" {{ ($required ?? false) ? 'required' : '' }} {!! $extra ?? '' !!} class="border border-slate-700 bg-slate-900 px-4 py-3 text-white">
    @error($name)<span class="text-rose-300">{{ $message }}</span>@enderror
</label>
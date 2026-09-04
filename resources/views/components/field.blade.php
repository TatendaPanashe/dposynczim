@props(['name', 'label', 'type' => 'text', 'required' => false, 'hint' => null])

<label {{ $attributes->merge(['class' => 'grid gap-2']) }}>
    <span class="text-sm font-semibold text-slate-700">{{ $label }} @if($required)<span class="text-cyan-700">*</span>@endif</span>
    @if($hint)<span class="-mt-1 text-xs text-slate-500">{{ $hint }}</span>@endif
    <input name="{{ $name }}" type="{{ $type }}" value="{{ old($name) }}" @required($required) {{ $attributes->except('class') }} class="form-control">
    @error($name)<span class="text-xs text-rose-700">{{ $message }}</span>@enderror
</label>
@props(['name', 'label', 'required' => false, 'hint' => null, 'rows' => 4])

<label {{ $attributes->merge(['class' => 'grid gap-2']) }}>
    <span class="text-sm font-semibold text-slate-700">{{ $label }} @if($required)<span class="text-cyan-700">*</span>@endif</span>
    @if($hint)<span class="-mt-1 text-xs text-slate-500">{{ $hint }}</span>@endif
    <textarea name="{{ $name }}" rows="{{ $rows }}" @required($required) {{ $attributes->except('class') }} class="form-control">{{ old($name) }}</textarea>
    @error($name)<span class="text-xs text-rose-700">{{ $message }}</span>@enderror
</label>
@props(['eyebrow', 'title', 'description' => null])
<div class="mb-8 flex flex-col justify-between gap-5 border-b border-slate-200 pb-7 md:flex-row md:items-end">
    <div class="max-w-2xl">
        <p class="mb-2 text-[11px] font-bold uppercase tracking-[0.22em] text-cyan-700">{{ $eyebrow }}</p>
        <h1 class="text-3xl font-bold tracking-[-0.03em] text-slate-950 lg:text-4xl">{{ $title }}</h1>
        @if($description)<p class="mt-3 max-w-xl text-sm leading-6 text-slate-500">{{ $description }}</p>@endif
    </div>
    @if(isset($actions))<div class="shrink-0">{{ $actions }}</div>@endif
</div>
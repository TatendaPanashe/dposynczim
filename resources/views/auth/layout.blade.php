<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Sign in' }} · Protego</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#0e1b23] text-white">
    <main class="grid min-h-screen lg:grid-cols-[1.1fr_0.9fr]">
        <section class="relative hidden overflow-hidden border-r border-slate-700 bg-[#102c35] p-12 lg:flex lg:flex-col lg:justify-between">
            <div class="absolute -right-32 top-20 size-96 rounded-full border border-cyan-300/20"></div>
            <div class="absolute -right-12 top-40 size-56 rounded-full border border-cyan-300/20"></div>
            <a href="{{ route('home') }}" class="relative inline-flex items-center gap-3"><span class="grid size-10 place-items-center bg-cyan-300 font-black text-[#0e1b23]">P</span><span><strong class="block">Protego</strong><small class="block text-[10px] uppercase tracking-[0.2em] text-slate-400">Zimbabwe privacy ops</small></span></a>
            <div class="relative max-w-xl"><p class="mb-5 text-sm font-bold uppercase tracking-[0.24em] text-cyan-300">Compliance, with a clear head.</p><h1 class="text-5xl font-bold leading-[1.05] tracking-[-0.05em]">The calm way to stay ready.</h1><p class="mt-6 max-w-md text-base leading-7 text-slate-300">Keep DP1, DP2, your processing register, and breach response in one secure operating rhythm.</p></div>
            <p class="relative text-xs text-slate-500">Built for Zimbabwe's Cyber and Data Protection Act · Chapter 12:07</p>
        </section>
        <section class="flex items-center justify-center bg-[#f4f7f8] px-6 py-12 text-slate-900">
            <div class="w-full max-w-md"><a href="{{ route('home') }}" class="mb-12 inline-flex items-center gap-3 lg:hidden"><span class="grid size-9 place-items-center bg-[#0e1b23] font-black text-cyan-300">P</span><strong>Protego</strong></a>@yield('content')</div>
        </section>
    </main>
</body>
</html>
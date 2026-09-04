<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Compliance workspace' }} · {{ config('app.name', 'Protego') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f4f7f8] text-slate-900 antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-[250px_1fr]">
        <aside class="border-b border-slate-200 bg-[#0e1b23] text-white lg:min-h-screen lg:border-b-0 lg:border-r lg:border-slate-800">
            <div class="flex items-center justify-between px-6 py-5 lg:block">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <span class="grid size-9 place-items-center bg-cyan-300 text-sm font-black text-[#0e1b23]">P</span>
                    <span>
                        <span class="block text-sm font-bold tracking-tight">Protego</span>
                        <span class="block text-[10px] uppercase tracking-[0.2em] text-slate-400">Zimbabwe privacy ops</span>
                    </span>
                </a>
                <details class="relative lg:hidden">
                    <summary class="grid size-9 cursor-pointer list-none place-items-center border border-slate-700 text-slate-300" aria-label="Open navigation">☰</summary>
                    <nav class="absolute right-0 top-12 z-20 grid w-64 gap-1 border border-slate-700 bg-[#0e1b23] p-3 shadow-xl">
                        <a href="{{ route('compliance.dashboard') }}" class="nav-link">◈ &nbsp; Overview</a>
                        <a href="{{ route('compliance.organizations.index') }}" class="nav-link">⌂ &nbsp; Organisations</a>
                        <a href="{{ route('compliance.dp1.create') }}" class="nav-link">▣ &nbsp; DP1 application</a>
                        <a href="{{ route('compliance.dp2.index') }}" class="nav-link">◎ &nbsp; DPO appointment</a>
                        <a href="{{ route('compliance.ropa.index') }}" class="nav-link">≡ &nbsp; Processing register</a>
                        <a href="{{ route('compliance.incidents.index') }}" class="nav-link">! &nbsp; Incidents</a>
                        <a href="{{ route('compliance.privacy-policies.create') }}" class="nav-link">▤ &nbsp; Privacy policies</a>
                    </nav>
                </details>
            </div>

            <nav class="hidden gap-1 px-4 pb-5 lg:grid">
                <p class="px-3 pb-2 pt-5 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Workspace</p>
                <a href="{{ route('compliance.dashboard') }}" class="nav-link {{ request()->routeIs('compliance.dashboard', 'home') ? 'nav-link-active' : '' }}"><span>◈</span> Overview</a>
                <a href="{{ route('compliance.organizations.index') }}" class="nav-link {{ request()->routeIs('compliance.organizations.*') ? 'nav-link-active' : '' }}"><span>⌂</span> Organisations</a>
                <a href="{{ route('compliance.dp1.create') }}" class="nav-link {{ request()->routeIs('compliance.dp1.*') ? 'nav-link-active' : '' }}"><span>▣</span> DP1 application</a>
                <a href="{{ route('compliance.dp2.index') }}" class="nav-link {{ request()->routeIs('compliance.dp2.*') ? 'nav-link-active' : '' }}"><span>◎</span> DPO appointment</a>
                <a href="{{ route('compliance.ropa.index') }}" class="nav-link {{ request()->routeIs('compliance.ropa.*') ? 'nav-link-active' : '' }}"><span>≡</span> Processing register</a>
                <a href="{{ route('compliance.incidents.index') }}" class="nav-link {{ request()->routeIs('compliance.incidents.*') ? 'nav-link-active' : '' }}"><span>!</span> Incidents</a>
                <a href="{{ route('compliance.privacy-policies.create') }}" class="nav-link {{ request()->routeIs('compliance.privacy-policies.*') ? 'nav-link-active' : '' }}"><span>▤</span> Privacy policies</a>
                <p class="px-3 pb-2 pt-8 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Reference</p>
                <div class="px-3 text-xs leading-5 text-slate-400">Act Chapter 12:07<br>S.I. 155 of 2024<br>DP1 · DP2 · DP3</div>
            </nav>
        </aside>

        <div class="min-w-0">
            <header class="flex min-h-16 items-center justify-between border-b border-slate-200 bg-white px-6 lg:px-10">
                <div class="text-sm text-slate-500">{{ $eyebrow ?? 'Compliance workspace' }}</div>
                <div class="flex items-center gap-3">
                    <span class="hidden text-right sm:block"><span class="block text-xs font-semibold">{{ auth()->user()?->name ?? 'Preview workspace' }}</span><span class="block text-[11px] text-slate-500">{{ auth()->user()?->activeOrganization()?->name ?? 'No organisation selected' }}</span></span>
                    <span class="grid size-9 place-items-center rounded-full bg-cyan-100 text-sm font-bold text-cyan-900">{{ substr(auth()->user()?->name ?? 'P', 0, 1) }}</span>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="text-xs font-bold text-slate-500 hover:text-slate-900">Sign out</button></form>
                </div>
            </header>
            <main class="mx-auto w-full max-w-[1480px] px-6 py-8 lg:px-10 lg:py-10">
                @if (session('success'))
                    <div class="mb-6 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">Please check the marked fields and try again.</div>
                @endif
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
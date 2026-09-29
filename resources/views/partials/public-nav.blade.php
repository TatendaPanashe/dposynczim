<header class="fixed inset-x-0 top-0 z-40 border-b border-white/10 bg-[#071013]/86 backdrop-blur-xl">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
            <span class="grid size-10 place-items-center bg-[#b9ff4f] text-sm font-black text-[#071013] shadow-[0_0_28px_rgba(185,255,79,0.35)]">DZ</span>
            <span>
                <strong class="block text-sm tracking-wide text-white">DPOSync Zim</strong>
                <small class="block text-[11px] uppercase tracking-[0.18em] text-slate-400">DPO · SI · Compliance</small>
            </span>
        </a>

        <nav class="hidden items-center gap-1 text-sm font-bold text-slate-300 lg:flex">
            <a href="{{ route('home') }}" class="px-3 py-2 transition hover:text-white {{ request()->routeIs('home') ? 'text-[#b9ff4f]' : '' }}">Home</a>
            <a href="{{ route('services') }}" class="px-3 py-2 transition hover:text-white {{ request()->routeIs('services') ? 'text-[#b9ff4f]' : '' }}">How it works</a>
            <a href="{{ route('products') }}" class="px-3 py-2 transition hover:text-white {{ request()->routeIs('products') ? 'text-[#b9ff4f]' : '' }}">Platform</a>
            <a href="{{ route('contact') }}" class="px-3 py-2 transition hover:text-white {{ request()->routeIs('contact') ? 'text-[#b9ff4f]' : '' }}">Contact</a>
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ route('login') }}" class="hidden border border-white/15 bg-white/5 px-4 py-2 text-xs font-black uppercase tracking-wide text-white transition hover:border-[#b9ff4f] hover:bg-[#b9ff4f] hover:text-[#071013] sm:inline-flex">Login</a>
            <a href="{{ route('register') }}" class="bg-white px-4 py-2 text-xs font-black uppercase tracking-wide text-[#071013] shadow-lg shadow-black/20 transition hover:bg-[#b9ff4f]">Start</a>
        </div>
    </div>
</header>

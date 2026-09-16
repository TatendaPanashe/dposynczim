<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Kodomo Technologies provides data protection as a service, AI automation, software development, and networking services for growing organizations.">
    <title>Services · Kodomo Technologies</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#081114] text-white antialiased">
    <header class="fixed inset-x-0 top-0 z-30 border-b border-white/10 bg-[#081114]/88 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                <span class="grid size-10 place-items-center bg-[#b7ff4a] text-sm font-black text-[#081114]">KT</span>
                <span>
                    <strong class="block text-sm tracking-wide">Kodomo Technologies</strong>
                    <small class="block text-[11px] uppercase tracking-[0.18em] text-slate-400">Build · Secure · Automate</small>
                </span>
            </a>
            <nav class="hidden items-center gap-6 text-sm font-bold text-slate-300 md:flex">
                <a href="{{ route('home') }}" class="hover:text-white">Home</a>
                <a href="{{ route('services') }}" class="text-[#b7ff4a]">Services</a>
                <a href="mailto:info@kodomotech.org" class="hover:text-white">info@kodomotech.org</a>
            </nav>
            <a href="{{ route('project-brief.create') }}" class="bg-white px-4 py-2 text-sm font-black text-[#081114] transition hover:bg-[#b7ff4a]">Start a project</a>
        </div>
    </header>

    <main>
        <section class="relative overflow-hidden px-5 pb-16 pt-32 lg:px-8 lg:pb-24 lg:pt-40">
            <div class="absolute inset-0 -z-10 bg-[linear-gradient(135deg,#081114_0%,#123138_46%,#102019_100%)]"></div>
            <div class="absolute inset-x-0 bottom-0 -z-10 h-40 bg-[linear-gradient(180deg,transparent,#f5f7f2)]"></div>

            <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-[0.98fr_1.02fr] lg:items-center">
                <div>
                    <p class="mb-5 text-sm font-black uppercase tracking-[0.24em] text-[#b7ff4a]">Our services</p>
                    <h1 class="max-w-4xl text-5xl font-black leading-[0.94] tracking-tight sm:text-6xl lg:text-7xl">Secure, automate, build, and connect your operations.</h1>
                    <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-300">Kodomo Technologies helps organizations protect personal data, automate repetitive work with AI, build reliable software, and maintain dependable networks.</p>
                    <div class="mt-9 flex flex-wrap gap-3">
                        <a href="{{ route('project-brief.create') }}" class="bg-[#b7ff4a] px-6 py-4 text-sm font-black text-[#081114] transition hover:bg-white">Talk to us</a>
                        <a href="{{ route('home') }}#products" class="border border-white/20 px-6 py-4 text-sm font-black text-white transition hover:border-[#b7ff4a] hover:text-[#b7ff4a]">View products</a>
                    </div>
                </div>

                <div class="grid gap-3 border border-white/12 bg-white/[0.04] p-4 shadow-2xl shadow-black/30">
                    <div class="border-b border-white/10 pb-4">
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">Service stack</p>
                        <p class="mt-1 text-sm font-bold text-slate-200">Built for compliance-heavy and growth-focused teams</p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="bg-[#10252b] p-5">
                            <p class="font-mono text-xs text-[#b7ff4a]">DPAAS</p>
                            <p class="mt-4 text-2xl font-black">Data protection</p>
                            <p class="mt-2 text-sm leading-6 text-slate-400">Privacy operations, records, breach readiness, policy support, and compliance workflows.</p>
                        </div>
                        <div class="bg-[#10252b] p-5">
                            <p class="font-mono text-xs text-[#b7ff4a]">AI</p>
                            <p class="mt-4 text-2xl font-black">Automation</p>
                            <p class="mt-2 text-sm leading-6 text-slate-400">Smart assistants, workflow automation, document handling, and operational intelligence.</p>
                        </div>
                        <div class="bg-[#10252b] p-5">
                            <p class="font-mono text-xs text-[#b7ff4a]">DEV</p>
                            <p class="mt-4 text-2xl font-black">Software</p>
                            <p class="mt-2 text-sm leading-6 text-slate-400">Custom business systems, web applications, integrations, dashboards, and portals.</p>
                        </div>
                        <div class="bg-[#10252b] p-5">
                            <p class="font-mono text-xs text-[#b7ff4a]">NET</p>
                            <p class="mt-4 text-2xl font-black">Networking</p>
                            <p class="mt-2 text-sm leading-6 text-slate-400">Secure connectivity, office networks, cloud infrastructure, monitoring, and support.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-[#f5f7f2] px-5 py-20 text-[#081114] lg:px-8 lg:py-24">
            <div class="mx-auto max-w-7xl">
                <div class="max-w-3xl">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-[#497112]">What we deliver</p>
                    <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">Practical technology services with governance built in.</h2>
                </div>
                <div class="mt-12 grid gap-4 md:grid-cols-2">
                    <article class="border border-[#d7dfcd] bg-white p-6">
                        <p class="font-mono text-xs font-bold text-[#497112]">01</p>
                        <h3 class="mt-6 text-2xl font-black">Data Protection as a Service</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">We help organizations manage privacy obligations through records of processing, policy templates, breach incident support, compliance documents, and data protection workflows that can be maintained month after month.</p>
                    </article>
                    <article class="border border-[#d7dfcd] bg-white p-6">
                        <p class="font-mono text-xs font-bold text-[#497112]">02</p>
                        <h3 class="mt-6 text-2xl font-black">AI automation</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">We design AI-assisted workflows that reduce manual admin, organize documents, answer internal questions, generate reports, and connect teams to the information they need faster.</p>
                    </article>
                    <article class="border border-[#d7dfcd] bg-white p-6">
                        <p class="font-mono text-xs font-bold text-[#497112]">03</p>
                        <h3 class="mt-6 text-2xl font-black">Software development</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">We build reliable web platforms, business applications, customer portals, integrations, and internal tools with clear interfaces and dependable backend operations.</p>
                    </article>
                    <article class="border border-[#d7dfcd] bg-white p-6">
                        <p class="font-mono text-xs font-bold text-[#497112]">04</p>
                        <h3 class="mt-6 text-2xl font-black">Networking</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">We plan, configure, and support office and cloud networks, including secure connectivity, infrastructure monitoring, backup planning, and practical technical support.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="bg-white px-5 py-20 text-[#081114] lg:px-8 lg:py-24">
            <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.75fr_1.25fr] lg:items-start">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-[#497112]">How we work</p>
                    <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">From assessment to managed improvement.</h2>
                </div>
                <div class="grid gap-4">
                    <div class="border border-slate-200 bg-[#f8faf7] p-6">
                        <h3 class="text-xl font-black">Assess</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">We map the current process, risks, systems, and infrastructure so the solution starts from real operational needs.</p>
                    </div>
                    <div class="border border-slate-200 bg-[#f8faf7] p-6">
                        <h3 class="text-xl font-black">Implement</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">We configure, build, integrate, and document the service so it can be used by your team with confidence.</p>
                    </div>
                    <div class="border border-slate-200 bg-[#f8faf7] p-6">
                        <h3 class="text-xl font-black">Operate</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">We support ongoing improvements, monitoring, maintenance, and compliance workflows as your organization grows.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-white/10 bg-[#081114] px-5 py-8 lg:px-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 text-sm text-slate-400 md:flex-row md:items-center md:justify-between">
            <p><strong class="text-white">Kodomo Technologies</strong> · Data protection, AI automation, software development, and networking.</p>
            <a href="mailto:info@kodomotech.org" class="font-bold text-[#b7ff4a]">info@kodomotech.org</a>
        </div>
    </footer>
</body>
</html>

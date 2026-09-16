<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Kodomo Technologies builds modern websites, hosting platforms, DevOps workflows, and network infrastructure for growing teams.">
    <title>Kodomo Technologies · Digital infrastructure studio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#081114] text-white antialiased">
    <header class="fixed inset-x-0 top-0 z-30 border-b border-white/10 bg-[#081114]/88 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                <span class="grid size-10 place-items-center bg-[#b7ff4a] text-sm font-black text-[#081114]">KT</span>
                <span>
                    <strong class="block text-sm tracking-wide">Kodomo Technologies</strong>
                    <small class="block text-[11px] uppercase tracking-[0.18em] text-slate-400">Build · Host · Operate</small>
                </span>
            </a>
            <nav class="hidden items-center gap-6 text-sm font-bold text-slate-300 md:flex">
                <a href="{{ route('services') }}" class="hover:text-white">Services</a>
                <a href="#products" class="hover:text-white">Products</a>
                <a href="mailto:info@kodomotech.org" class="hover:text-white">info@kodomotech.org</a>
            </nav>
            <a href="#products" class="bg-white px-4 py-2 text-sm font-black text-[#081114] transition hover:bg-[#b7ff4a]">View products</a>
        </div>
    </header>

    <main>
        <section class="relative overflow-hidden px-5 pb-16 pt-32 lg:px-8 lg:pb-24 lg:pt-40">
            <div class="absolute inset-0 -z-10 bg-[linear-gradient(135deg,#081114_0%,#123138_46%,#102019_100%)]"></div>
            <div class="absolute inset-x-0 bottom-0 -z-10 h-40 bg-[linear-gradient(180deg,transparent,#f5f7f2)]"></div>

            <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-[1.02fr_0.98fr] lg:items-center">
                <div>
                    <p class="mb-5 text-sm font-black uppercase tracking-[0.24em] text-[#b7ff4a]">Kodomo Technologies</p>
                    <h1 class="max-w-4xl text-5xl font-black leading-[0.94] tracking-tight sm:text-6xl lg:text-7xl">State of the art digital systems for serious operators.</h1>
                    <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-300">We design, host, deploy, and connect web platforms that stay fast, secure, and useful after launch.</p>
                    <div class="mt-9 flex flex-wrap gap-3">
                        <a href="{{ route('services') }}" class="bg-[#b7ff4a] px-6 py-4 text-sm font-black text-[#081114] transition hover:bg-white">Explore services</a>
                        <a href="{{ route('project-brief.create') }}" class="border border-white/20 px-6 py-4 text-sm font-black text-white transition hover:border-[#b7ff4a] hover:text-[#b7ff4a]">Start a project</a>
                    </div>
                </div>

                <div class="relative">
                    <div class="grid gap-3 border border-white/12 bg-white/[0.04] p-4 shadow-2xl shadow-black/30">
                        <div class="flex items-center justify-between border-b border-white/10 pb-4">
                            <div>
                                <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">Operations console</p>
                                <p class="mt-1 text-sm font-bold text-slate-200">Live service stack</p>
                            </div>
                            <span class="rounded-full bg-[#b7ff4a] px-3 py-1 text-xs font-black text-[#081114]">ONLINE</span>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="bg-[#10252b] p-5">
                                <p class="font-mono text-xs text-[#b7ff4a]">WEB</p>
                                <p class="mt-4 text-3xl font-black">99.9%</p>
                                <p class="mt-1 text-sm text-slate-400">availability target</p>
                            </div>
                            <div class="bg-[#10252b] p-5">
                                <p class="font-mono text-xs text-[#b7ff4a]">DEPLOY</p>
                                <p class="mt-4 text-3xl font-black">CI/CD</p>
                                <p class="mt-1 text-sm text-slate-400">repeatable releases</p>
                            </div>
                            <div class="bg-[#10252b] p-5">
                                <p class="font-mono text-xs text-[#b7ff4a]">HOST</p>
                                <p class="mt-4 text-3xl font-black">Cloud</p>
                                <p class="mt-1 text-sm text-slate-400">managed infrastructure</p>
                            </div>
                            <div class="bg-[#10252b] p-5">
                                <p class="font-mono text-xs text-[#b7ff4a]">NET</p>
                                <p class="mt-4 text-3xl font-black">Secure</p>
                                <p class="mt-1 text-sm text-slate-400">connected teams</p>
                            </div>
                             <div class="bg-[#10252b] p-5">
                                <p class="font-mono text-xs text-[#b7ff4a]">DPO as a Service</p>
                                <p class="mt-4 text-3xl font-black">Compliance</p>
                                <p class="mt-1 text-sm text-slate-400">DP1, DP2 and DP3 </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="services" class="bg-[#f5f7f2] px-5 py-20 text-[#081114] lg:px-8 lg:py-24">
            <div class="mx-auto max-w-7xl">
                <div class="max-w-3xl">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-[#497112]">Services</p>
                    <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">Web platforms built with the backend in mind.</h2>
                </div>
                <div class="mt-12 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <article class="border border-[#d7dfcd] bg-white p-6">
                        <p class="font-mono text-xs font-bold text-[#497112]">01</p>
                        <h3 class="mt-6 text-xl font-black">Web design</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Sharp, responsive sites and product interfaces designed around trust, clarity, and conversion.</p>
                    </article>
                    <article class="border border-[#d7dfcd] bg-white p-6">
                        <p class="font-mono text-xs font-bold text-[#497112]">02</p>
                        <h3 class="mt-6 text-xl font-black">Web hosting</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Managed hosting for business websites and apps, with the operational basics handled properly.</p>
                    </article>
                    <article class="border border-[#d7dfcd] bg-white p-6">
                        <p class="font-mono text-xs font-bold text-[#497112]">03</p>
                        <h3 class="mt-6 text-xl font-black">DevOps</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Deployment pipelines, server automation, monitoring, backups, and release workflows teams can trust.</p>
                    </article>
                    <article class="border border-[#d7dfcd] bg-white p-6">
                        <p class="font-mono text-xs font-bold text-[#497112]">04</p>
                        <h3 class="mt-6 text-xl font-black">Networking</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Office and cloud networking, secure connectivity, infrastructure planning, and practical support.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="bg-[#101b1f] px-5 py-20 text-white lg:px-8 lg:py-24">
            <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.72fr_1.28fr] lg:items-start">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-[#b7ff4a]">Meet the team</p>
                    <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">The people behind the build.</h2>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <article class="border border-white/10 bg-white/[0.04] p-6">
                        <div class="grid size-14 place-items-center bg-[#b7ff4a] text-lg font-black text-[#081114]">OC</div>
                        <h3 class="mt-6 text-2xl font-black">Oliver Chimuka</h3>
                        <p class="mt-2 text-sm font-bold uppercase tracking-[0.16em] text-[#b7ff4a]">CEO & Co-Founder</p>
                        <p class="mt-4 text-sm leading-6 text-slate-300">Leads product direction, full stack developer who builds the infrastructure, client strategy, and the operating vision for Kodomo Technologies.</p>
                    </article>
                    <article class="border border-white/10 bg-white/[0.04] p-6">
                        <div class="grid size-14 place-items-center bg-[#b7ff4a] text-lg font-black text-[#081114]">TC</div>
                        <h3 class="mt-6 text-2xl font-black">Tatenda P. Chiota</h3>
                        <p class="mt-2 text-sm font-bold uppercase tracking-[0.16em] text-[#b7ff4a]">CTO & Co-founder</p>
                        <p class="mt-4 text-sm leading-6 text-slate-300">Builds the infrastructure, deployment workflows, and systems reliability behind Kodomo platforms.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="products" class="bg-white px-5 py-20 text-[#081114] lg:px-8 lg:py-24">
            <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.75fr_1.25fr]">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-[#497112]">Products</p>
                    <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">Kodomo-built applications in production.</h2>
                    <p class="mt-5 text-base leading-7 text-slate-600">Explore the platforms connected to our current operating stack.</p>
                </div>
                <div class="grid gap-4">
                    <a href="{{ route('login') }}" class="group border border-slate-200 bg-[#f8faf7] p-6 transition hover:border-[#b7ff4a] hover:bg-[#f0ffe0]">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-mono text-xs font-bold uppercase tracking-[0.18em] text-[#497112]">Compliance</p>
                                <h3 class="mt-2 text-2xl font-black">DPOSyncZim</h3>
                                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Zimbabwe privacy operations, ROPA exports, DP1/DP2 documents, and payment-gated downloads.</p>
                            </div>
                            <span class="shrink-0 text-sm font-black text-[#497112] group-hover:text-[#081114]">Open app</span>
                        </div>
                    </a>
                    <a href="https://post.kodomotech.org" class="group border border-slate-200 bg-[#f8faf7] p-6 transition hover:border-[#b7ff4a] hover:bg-[#f0ffe0]">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-mono text-xs font-bold uppercase tracking-[0.18em] text-[#497112]">Postal operations</p>
                                <h3 class="mt-2 text-2xl font-black">ZimPost App</h3>
                                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">A connected platform for postal workflows and digital service delivery.</p>
                            </div>
                            <span class="shrink-0 text-sm font-black text-[#497112] group-hover:text-[#081114]">post.kodomotech.org</span>
                        </div>
                    </a>
                    <a href="https://gruma.kodomotech.org" class="group border border-slate-200 bg-[#f8faf7] p-6 transition hover:border-[#b7ff4a] hover:bg-[#f0ffe0]">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-mono text-xs font-bold uppercase tracking-[0.18em] text-[#497112]">Business platform</p>
                                <h3 class="mt-2 text-2xl font-black">Gruma</h3>
                                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">A production web application hosted and operated under the Kodomo Technologies stack.</p>
                            </div>
                            <span class="shrink-0 text-sm font-black text-[#497112] group-hover:text-[#081114]">gruma.kodomotech.org</span>
                        </div>
                    </a>
                    <a href="#" class="group border border-slate-200 bg-[#f8faf7] p-6 transition hover:border-[#b7ff4a] hover:bg-[#f0ffe0]">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-mono text-xs font-bold uppercase tracking-[0.18em] text-[#497112]">Accounting system</p>
                                <h3 class="mt-2 text-2xl font-black">Acc263</h3>
                                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">An accounting platform for finance workflows, records, and business reporting.</p>
                            </div>
                            <span class="shrink-0 text-sm font-black text-[#497112] group-hover:text-[#081114]">Coming soon</span>
                        </div>
                    </a>
                     <a href="#" class="group border border-slate-200 bg-[#f8faf7] p-6 transition hover:border-[#b7ff4a] hover:bg-[#f0ffe0]">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-mono text-xs font-bold uppercase tracking-[0.18em] text-[#497112]">Human Resource system</p>
                                <h3 class="mt-2 text-2xl font-black">hr263</h3>
                                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">A human resource platform for all your human resources needs, records, and business reporting.</p>
                            </div>
                            <span class="shrink-0 text-sm font-black text-[#497112] group-hover:text-[#081114]">Coming soon</span>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-white/10 bg-[#081114] px-5 py-8 lg:px-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 text-sm text-slate-400 md:flex-row md:items-center md:justify-between">
            <p><strong class="text-white">Kodomo Technologies</strong> · Web design, hosting, DevOps, and networking.</p>
            <a href="mailto:info@kodomotech.org" class="font-bold text-[#b7ff4a]">info@kodomotech.org</a>
        </div>
    </footer>
</body>
</html>

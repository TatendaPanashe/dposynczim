<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Protection Zimbabwe · DPOSync Zim</title>
    @include('partials.public-seo', [
        'title' => 'Data Protection Zimbabwe · DPOSync Zim',
        'description' => 'DPOSync Zim is a Zimbabwe data protection and privacy compliance platform for DPOs, system integrators, and organisations preparing DP1, DP2, ROPA, privacy policies, breach records, and POTRAZ-ready submissions.',
        'canonical' => route('home'),
        'jsonLd' => [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => 'DPOSync Zim',
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'description' => 'Zimbabwe data protection and privacy compliance workspace for DPOs, system integrators, and organisations.',
            'url' => route('home'),
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Kodomo Technologies',
                'email' => 'info@kodomotech.org',
            ],
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'Zimbabwe',
            ],
            'offers' => [
                '@type' => 'Offer',
                'availability' => 'https://schema.org/InStock',
                'url' => route('register'),
            ],
        ],
    ])
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#071013] text-white antialiased">
    @include('partials.public-nav')

    <main>
        <section class="relative min-h-[760px] overflow-hidden px-5 pb-20 pt-32 lg:px-8 lg:pt-40">
            <img src="{{ asset('images/kodomo-hero-ops.png') }}" alt="Compliance operations workspace" class="absolute inset-0 h-full w-full object-cover">
            <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(7,16,19,0.99)_0%,rgba(7,16,19,0.88)_42%,rgba(7,16,19,0.34)_78%,rgba(7,16,19,0.12)_100%)]"></div>
            <div class="absolute inset-x-0 bottom-0 h-40 bg-[linear-gradient(180deg,transparent,#f6f8f4)]"></div>

            <div class="relative mx-auto flex max-w-7xl flex-col justify-center pt-14">
                <div class="max-w-4xl">
                    <p class="mb-5 text-sm font-black uppercase tracking-[0.24em] text-[#b9ff4f]">DPOSync Zim</p>
                    <h1 class="text-5xl font-black leading-[0.96] tracking-tight sm:text-6xl lg:text-7xl">Data protection compliance software for Zimbabwe.</h1>
                    <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-200">One clean workspace for DPOs, system integrators, and organisations to prepare DP1, DP2, ROPA, privacy policies, breach records, payments, and regulator-ready submissions.</p>
                    <div class="mt-9 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="bg-[#b9ff4f] px-6 py-4 text-sm font-black uppercase tracking-wide text-[#071013] shadow-[0_18px_60px_rgba(185,255,79,0.22)] transition hover:bg-white">Start compliance workspace</a>
                        <a href="{{ route('services') }}" class="border border-white/20 bg-white/5 px-6 py-4 text-sm font-black uppercase tracking-wide text-white transition hover:border-[#b9ff4f] hover:text-[#b9ff4f]">How it works</a>
                    </div>
                </div>

                <div class="mt-14 grid max-w-4xl gap-3 sm:grid-cols-3">
                    @foreach([
                        ['DP1 · DP2 · DP3', 'Official workflow coverage'],
                        ['SI-ready', 'Manage multiple client workspaces'],
                        ['POTRAZ-focused', 'Documents prepared for local compliance'],
                    ] as [$value, $label])
                        <div class="border border-white/12 bg-white/[0.08] p-4 backdrop-blur">
                            <p class="text-2xl font-black">{{ $value }}</p>
                            <p class="mt-1 text-xs font-bold uppercase tracking-wide text-slate-300">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-[#f6f8f4] px-5 py-20 text-[#071013] lg:px-8 lg:py-24">
            <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.78fr_1.22fr] lg:items-start">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-[#497112]">What DPOSync Zim does</p>
                    <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">Turns privacy compliance into a repeatable operating system.</h2>
                    <p class="mt-5 text-base leading-7 text-slate-600">Instead of scattered spreadsheets, email threads, and half-finished templates, every organisation gets a structured workspace with clear records, forms, and next actions.</p>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach([
                        ['DPO profile', 'Save officer details once, then reuse them across DP2 appointments and client workspaces.'],
                        ['Organisation workspaces', 'Keep every client, subsidiary, or business unit isolated with its own filings, payments, and records.'],
                        ['Regulator-ready documents', 'Prepare DP1 registrations, DP2 appointments, breach incident records, ROPA exports, and privacy policies.'],
                        ['Operational control', 'Track monthly access, downloads, submissions, and evidence without losing context between clients.'],
                    ] as [$title, $copy])
                        <article class="border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-[#b9ff4f] hover:shadow-xl hover:shadow-slate-200/70">
                            <h3 class="text-xl font-black">{{ $title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $copy }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-white px-5 py-20 text-[#071013] lg:px-8 lg:py-24">
            <div class="mx-auto max-w-7xl">
                <div class="max-w-3xl">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-[#497112]">How it works</p>
                    <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">From DPO setup to downloadable compliance evidence.</h2>
                </div>

                <div class="mt-12 grid gap-4 lg:grid-cols-4">
                    @foreach([
                        ['01', 'Create your DPO profile', 'Add registration, contact details, qualifications, certification status, and reporting line.'],
                        ['02', 'Add organisations', 'Create a dedicated workspace for each organisation you support as a DPO or SI.'],
                        ['03', 'Complete workflows', 'Capture DP1, DP2, breach, ROPA, and privacy policy information using guided forms.'],
                        ['04', 'Download and submit', 'Pay for monthly access where required, download records, and send POTRAZ-ready submissions.'],
                    ] as [$number, $title, $copy])
                        <article class="border border-slate-200 bg-[#f8faf7] p-6">
                            <p class="font-mono text-sm font-bold text-[#497112]">{{ $number }}</p>
                            <h3 class="mt-8 text-xl font-black">{{ $title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $copy }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-[#101b1f] px-5 py-20 text-white lg:px-8 lg:py-24">
            <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.75fr_1.25fr] lg:items-start">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-[#b9ff4f]">For system integrators</p>
                    <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">A compliance layer for every SI delivering digital transformation.</h2>
                    <p class="mt-5 text-base leading-7 text-slate-300">When an SI rolls out software, networks, cloud systems, payments, or data platforms, privacy obligations follow. DPOSync Zim gives integrators a practical way to help clients document that responsibility.</p>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach([
                        ['Client portfolio control', 'Move between client organisations without mixing records, evidence, appointments, or submissions.'],
                        ['Implementation support', 'Map systems, personal data flows, processing purposes, incidents, and policy requirements into usable records.'],
                        ['Recurring compliance service', 'Package DPO support, document maintenance, privacy reviews, and monthly evidence as an ongoing SI service.'],
                        ['Local language of compliance', 'Built around Zimbabwean DP workflows, POTRAZ forms, practical DPO operations, and regulator-facing documentation.'],
                    ] as [$title, $copy])
                        <article class="border border-white/10 bg-white/[0.04] p-6">
                            <h3 class="text-xl font-black">{{ $title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-300">{{ $copy }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-[#b9ff4f] px-5 py-16 text-[#071013] lg:px-8">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-[#35550c]">Ready when compliance matters</p>
                    <h2 class="mt-2 text-3xl font-black tracking-tight lg:text-4xl">Set up your DPO workspace and bring every client into order.</h2>
                </div>
                <a href="{{ route('register') }}" class="inline-flex bg-[#071013] px-6 py-4 text-sm font-black uppercase tracking-wide text-white transition hover:bg-white hover:text-[#071013]">Create account</a>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Compliance Engine Zimbabwe · DPOSync Zim</title>
    @include('partials.public-seo', [
        'title' => 'Compliance Engine Zimbabwe · DPOSync Zim',
        'description' => 'DPOSync Zim is a Zimbabwe compliance engine for organisations, DPOs, and system integrators managing obligations, calendars, tasks, evidence, reports, DP1, DP2, ROPA, privacy policies, breach records, and POTRAZ-ready submissions.',
        'canonical' => route('home'),
        'jsonLd' => [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => 'DPOSync Zim',
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'description' => 'Zimbabwe compliance engine for obligations, privacy operations, evidence, reporting, and regulator-ready records.',
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
        <section class="relative min-h-[840px] overflow-hidden px-5 pb-24 pt-32 lg:px-8 lg:pt-40">
            <img src="{{ asset('images/dposync-compliance-engine-hero.png') }}" alt="DPOSync Zim compliance engine dashboard" class="absolute inset-0 h-full w-full object-cover">
            <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(3,7,18,0.98)_0%,rgba(8,47,115,0.92)_36%,rgba(8,47,115,0.45)_68%,rgba(3,7,18,0.1)_100%)]"></div>
            <div class="absolute inset-x-0 top-0 h-48 bg-[linear-gradient(180deg,rgba(3,7,18,0.9),transparent)]"></div>
            <div class="absolute inset-x-0 bottom-0 h-56 bg-[linear-gradient(180deg,transparent,#eff6ff)]"></div>

            <div class="relative mx-auto grid max-w-7xl gap-12 pt-14 lg:grid-cols-[0.95fr_1.05fr] lg:items-end">
                <div class="max-w-4xl pb-8">
                    <div class="mb-6 inline-flex items-center gap-3 border border-blue-200/20 bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-[0.22em] text-blue-100 shadow-2xl shadow-blue-950/20 backdrop-blur">
                        <span class="size-2 bg-blue-200"></span>
                        DPOSync Zim
                    </div>
                    <h1 class="text-5xl font-black leading-[0.92] tracking-tight sm:text-6xl lg:text-7xl">Compliance, beautifully under control.</h1>
                    <p class="mt-7 max-w-2xl text-lg leading-8 text-blue-50">Run obligations, calendar deadlines, tasks, evidence, reports, DPO records, DP1, DP2, ROPA, privacy policies, breach records, payments, and regulator-ready submissions from one polished workspace.</p>
                    <div class="mt-9 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="bg-white px-6 py-4 text-sm font-black uppercase tracking-wide text-blue-950 shadow-[0_22px_70px_rgba(147,197,253,0.26)] transition hover:bg-blue-100">Start compliance engine</a>
                        <a href="{{ route('services') }}" class="border border-white/20 bg-white/10 px-6 py-4 text-sm font-black uppercase tracking-wide text-white backdrop-blur transition hover:border-white hover:bg-white/15">How it works</a>
                    </div>

                    <div class="mt-14 grid max-w-4xl gap-3 sm:grid-cols-3">
                        @foreach([
                            ['360°', 'Obligations, tasks, evidence'],
                            ['$1.99', 'Monthly form downloads'],
                            ['POTRAZ', 'Regulator-ready records'],
                        ] as [$value, $label])
                            <div class="border border-white/12 bg-white/[0.1] p-4 shadow-2xl shadow-blue-950/20 backdrop-blur">
                                <p class="text-3xl font-black">{{ $value }}</p>
                                <p class="mt-1 text-xs font-bold uppercase tracking-wide text-blue-100">{{ $label }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="hidden pb-8 lg:block">
                    <div class="ml-auto max-w-md border border-white/12 bg-white/[0.08] p-5 shadow-2xl shadow-blue-950/40 backdrop-blur-xl">
                        <div class="border border-white/10 bg-blue-950/60 p-5">
                            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-[0.2em] text-blue-200">Live workspace</p>
                                    <h2 class="mt-2 text-2xl font-black">Readiness engine</h2>
                                </div>
                                <span class="bg-blue-100 px-3 py-1 text-xs font-black text-blue-950">86%</span>
                            </div>
                            <div class="mt-5 grid gap-3">
                                @foreach([
                                    ['Annual policy review', 'Due in 6 days', 'w-[78%]'],
                                    ['DP2 appointment record', 'Ready', 'w-full'],
                                    ['Evidence checklist', '12 open items', 'w-[52%]'],
                                ] as [$title, $status, $width])
                                    <div class="border border-white/10 bg-white/[0.06] p-4">
                                        <div class="flex items-center justify-between gap-4">
                                            <p class="text-sm font-bold">{{ $title }}</p>
                                            <p class="text-xs font-black uppercase tracking-wide text-blue-200">{{ $status }}</p>
                                        </div>
                                        <div class="mt-3 h-2 bg-white/10">
                                            <div class="h-2 bg-blue-200 {{ $width }}"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-[#eff6ff] px-5 py-20 text-[#071013] lg:px-8 lg:py-24">
            <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.78fr_1.22fr] lg:items-start">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-800">What DPOSync Zim does</p>
                    <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">Turns compliance into a repeatable operating system.</h2>
                    <p class="mt-5 text-base leading-7 text-slate-600">Instead of scattered spreadsheets, email threads, and half-finished templates, every organisation gets a structured engine for obligations, filings, evidence, incidents, reports, and next actions.</p>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach([
                        ['Compliance calendar', 'Track obligations, deadlines, reminders, owner actions, checklist progress, and review cycles from one place.'],
                        ['Organisation workspaces', 'Keep every client, subsidiary, or business unit isolated with its own filings, payments, tasks, incidents, and evidence.'],
                        ['Regulator-ready records', 'Prepare DP1 registrations, DP2 appointments, breach incident records, ROPA exports, privacy policies, and supporting evidence.'],
                        ['Management reporting', 'See compliance status, overdue work, upcoming obligations, activity history, and evidence gaps without losing context between organisations.'],
                    ] as [$title, $copy])
                        <article class="border border-blue-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-blue-500 hover:shadow-xl hover:shadow-blue-100">
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
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-800">How it works</p>
                    <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">From obligations to downloadable compliance evidence.</h2>
                </div>

                <div class="mt-12 grid gap-4 lg:grid-cols-4">
                    @foreach([
                        ['01', 'Create organisations', 'Set up each entity, client, subsidiary, or business unit with its own compliance workspace.'],
                        ['02', 'Load obligations', 'Use the compliance calendar to assign owners, due dates, checklists, reminders, and evidence requirements.'],
                        ['03', 'Complete workflows', 'Manage tasks, DP1, DP2, breach, ROPA, privacy policy, catalogue, and reporting workflows from guided screens.'],
                        ['04', 'Report and submit', 'Download records, review status, manage access payments, and send POTRAZ-ready submissions where required.'],
                    ] as [$number, $title, $copy])
                        <article class="border border-blue-100 bg-blue-50 p-6">
                            <p class="font-mono text-sm font-bold text-blue-800">{{ $number }}</p>
                            <h3 class="mt-8 text-xl font-black">{{ $title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $copy }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-[#082f73] px-5 py-20 text-white lg:px-8 lg:py-24">
            <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.75fr_1.25fr] lg:items-start">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-200">For operators, DPOs, and SIs</p>
                    <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">A compliance layer for every organisation handling regulated work.</h2>
                    <p class="mt-5 text-base leading-7 text-blue-100">When teams roll out systems, handle personal data, manage vendors, run payments, or support clients, obligations follow. DPOSync Zim gives them a practical engine to assign work, keep evidence, and document responsibility.</p>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach([
                        ['Portfolio control', 'Move between organisations without mixing obligations, evidence, appointments, incidents, payments, or submissions.'],
                        ['Operational accountability', 'Assign owners, track checklists, log activity, store evidence, and keep a clear record of completed compliance work.'],
                        ['Recurring compliance service', 'Package DPO support, obligation reviews, document maintenance, privacy reviews, and monthly evidence as an ongoing service.'],
                        ['Local language of compliance', 'Built around Zimbabwean compliance operations, DP workflows, POTRAZ forms, and regulator-facing documentation.'],
                    ] as [$title, $copy])
                        <article class="border border-white/10 bg-white/[0.04] p-6">
                            <h3 class="text-xl font-black">{{ $title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-blue-100">{{ $copy }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-[#dbeafe] px-5 py-16 text-[#071013] lg:px-8">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-800">Ready when compliance matters</p>
                    <h2 class="mt-2 text-3xl font-black tracking-tight lg:text-4xl">Set up your compliance engine and bring every obligation into order.</h2>
                </div>
                <a href="{{ route('register') }}" class="inline-flex bg-blue-900 px-6 py-4 text-sm font-black uppercase tracking-wide text-white transition hover:bg-blue-700">Create account</a>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>

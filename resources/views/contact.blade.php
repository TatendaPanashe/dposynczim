<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Compliance Support · DPOSync Zim</title>
    @include('partials.public-seo', [
        'title' => 'Contact Compliance Support · DPOSync Zim',
        'description' => 'Contact DPOSync Zim for Zimbabwe compliance engine onboarding, organisation workspaces, DPO workflows, system integrator partnerships, payments, downloads, and regulator-ready evidence.',
        'canonical' => route('contact'),
    ])
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#071013] text-white antialiased">
    @include('partials.public-nav')

    <main>
        <section class="relative overflow-hidden px-5 pb-20 pt-32 lg:px-8 lg:pt-40">
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_78%_18%,rgba(147,197,253,0.28),transparent_30%),linear-gradient(135deg,#071013_0%,#082f73_56%,#123039_100%)]"></div>
            <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-[0.82fr_1.18fr] lg:items-start">
                <div>
                    <p class="mb-5 text-sm font-black uppercase tracking-[0.24em] text-blue-200">Contact DPOSync Zim</p>
                    <h1 class="max-w-4xl text-5xl font-black leading-[0.94] tracking-tight sm:text-6xl lg:text-7xl">Let’s put your compliance engine in motion.</h1>
                    <p class="mt-7 max-w-2xl text-lg leading-8 text-blue-100">Talk to us about organisation workspaces, compliance calendars, evidence workflows, DPO records, SI delivery, $1.99 monthly download access, and regulator-ready documentation.</p>
                    <div class="mt-10 grid max-w-2xl gap-3 sm:grid-cols-3">
                        @foreach([
                            ['Onboard', 'Set up your first workspace'],
                            ['Operate', 'Run tasks and evidence'],
                            ['Export', 'Unlock monthly downloads'],
                        ] as [$title, $copy])
                            <div class="border border-white/10 bg-white/[0.08] p-4 backdrop-blur">
                                <p class="text-xl font-black">{{ $title }}</p>
                                <p class="mt-1 text-xs font-bold uppercase tracking-wide text-blue-100">{{ $copy }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="border border-white/12 bg-white/[0.08] p-5 shadow-2xl shadow-blue-950/30 backdrop-blur">
                    <div class="grid gap-4">
                        <a href="mailto:info@kodomotech.org" class="group border border-white/10 bg-white/[0.07] p-6 transition hover:border-blue-200 hover:bg-white/[0.1]">
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-blue-200">Email</p>
                            <h2 class="mt-4 break-words text-3xl font-black">info@kodomotech.org</h2>
                            <p class="mt-3 text-sm leading-6 text-blue-100">Use email for onboarding, payment/download questions, DPO support, SI partnerships, and platform guidance.</p>
                            <span class="mt-6 inline-flex text-sm font-black uppercase tracking-wide text-white group-hover:text-blue-100">Send email</span>
                        </a>
                        <a href="{{ route('project-brief.create') }}" class="group border border-blue-200 bg-blue-100 p-6 text-blue-950 transition hover:bg-white">
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-blue-800">Structured request</p>
                            <h2 class="mt-4 text-3xl font-black">Describe your compliance need</h2>
                            <p class="mt-3 text-sm leading-6 text-blue-950/80">Share the organisation type, number of workspaces, DPO status, SI role, document downloads needed, and workflows you want to manage.</p>
                            <span class="mt-6 inline-flex text-sm font-black uppercase tracking-wide text-blue-900">Start request</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-[#eff6ff] px-5 py-20 text-[#071013] lg:px-8 lg:py-24">
            <div class="mx-auto max-w-7xl">
                <div class="max-w-3xl">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-800">What we can help with</p>
                    <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">Support for setup, operations, and regulator-ready records.</h2>
                </div>

                <div class="mt-12 grid gap-4 md:grid-cols-3">
                @foreach([
                    ['Workspace onboarding', 'Set up organisations, users, compliance calendars, owners, and first obligation templates.'],
                    ['DPO and DP records', 'Prepare DPO profiles, DP1, DP2, ROPA, privacy policies, incident records, and POTRAZ-facing evidence.'],
                    ['SI partnerships', 'Add DPOSync Zim to client implementations, managed services, handovers, and recurring compliance reviews.'],
                ] as [$title, $copy])
                    <article class="border border-blue-100 bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black">{{ $title }}</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $copy }}</p>
                    </article>
                @endforeach
                </div>
            </div>
        </section>

        <section class="bg-white px-5 py-20 text-[#071013] lg:px-8 lg:py-24">
            <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:items-start">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-800">Before you contact us</p>
                    <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">A few details help us respond faster.</h2>
                    <p class="mt-5 text-base leading-7 text-slate-600">If you can, include what type of organisation you support, whether you are working as a DPO, internal team, or system integrator, and which outputs you need to download or submit.</p>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach([
                        ['Workspace scope', 'How many organisations, clients, or departments do you want to manage?'],
                        ['Workflow needs', 'Do you need obligations, tasks, DP1, DP2, DP3, ROPA, policies, reports, or all of them?'],
                        ['Download access', 'Will your team need the $1.99 monthly form download access for one or more workspaces?'],
                        ['Timeline', 'Are you preparing for onboarding, a regulator submission, an audit, or an internal review?'],
                    ] as [$title, $copy])
                        <article class="border border-blue-100 bg-blue-50 p-6">
                            <h3 class="text-xl font-black">{{ $title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $copy }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-[#082f73] px-5 py-16 text-white lg:px-8">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-200">Ready to talk?</p>
                    <h2 class="mt-2 text-3xl font-black tracking-tight lg:text-4xl">Send the brief and we’ll help map the best setup.</h2>
                </div>
                <a href="{{ route('project-brief.create') }}" class="inline-flex bg-white px-6 py-4 text-sm font-black uppercase tracking-wide text-blue-900 transition hover:bg-blue-100">Describe your need</a>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>

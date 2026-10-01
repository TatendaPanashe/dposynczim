<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Protection Services for DPOs and SIs · DPOSync Zim</title>
    @include('partials.public-seo', [
        'title' => 'Data Protection Services for DPOs and SIs · DPOSync Zim',
        'description' => 'Learn how DPOSync Zim supports Zimbabwean data protection officers, system integrators, and organisations with privacy compliance workflows, DP records, policies, incidents, and POTRAZ-ready evidence.',
        'canonical' => route('services'),
    ])
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#071013] text-white antialiased">
    @include('partials.public-nav')

    <main>
        <section class="relative overflow-hidden px-5 pb-20 pt-32 lg:px-8 lg:pt-40">
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_78%_18%,rgba(185,255,79,0.22),transparent_28%),linear-gradient(135deg,#071013_0%,#123039_54%,#071013_100%)]"></div>
            <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-[0.9fr_1.1fr] lg:items-end">
                <div>
                    <p class="mb-5 text-sm font-black uppercase tracking-[0.24em] text-[#b9ff4f]">How it works</p>
                    <h1 class="max-w-4xl text-5xl font-black leading-[0.96] tracking-tight sm:text-6xl lg:text-7xl">Clear steps for serious data protection operations.</h1>
                    <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-300">DPOSync Zim guides a DPO or SI from setup to client workspace, then from structured forms to downloadable compliance evidence.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach([
                        ['01', 'Profile', 'Capture DPO details, qualifications, registration, contacts, and reporting line once.'],
                        ['02', 'Workspace', 'Add each organisation and keep its filings, incidents, and payments separate.'],
                        ['03', 'Workflow', 'Complete DP1, DP2, ROPA, privacy policy, and breach records through guided screens.'],
                        ['04', 'Evidence', 'Download records, manage access payments, and prepare information for POTRAZ submission.'],
                    ] as [$number, $title, $copy])
                        <div class="border border-white/10 bg-white/[0.06] p-5">
                            <p class="font-mono text-xs text-[#b9ff4f]">{{ $number }}</p>
                            <h2 class="mt-6 text-xl font-black">{{ $title }}</h2>
                            <p class="mt-3 text-sm leading-6 text-slate-300">{{ $copy }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-[#f6f8f4] px-5 py-20 text-[#071013] lg:px-8 lg:py-24">
            <div class="mx-auto max-w-7xl">
                <div class="max-w-3xl">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-[#497112]">Who uses it</p>
                    <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">Built for the people responsible for making compliance real.</h2>
                </div>
                <div class="mt-12 grid gap-4 md:grid-cols-3">
                    @foreach([
                        ['Data protection officers', 'Keep officer details current, prepare appointment records, and manage ongoing compliance tasks across every organisation you support.'],
                        ['System integrators', 'Add privacy operations to client implementations, document data flows, and create recurring compliance support around digital systems.'],
                        ['Organisations', 'Move from ad hoc compliance to a controlled workspace with structured filings, records, policy content, and incident evidence.'],
                    ] as [$title, $copy])
                        <article class="border border-slate-200 bg-white p-6 shadow-sm">
                            <h3 class="text-xl font-black">{{ $title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $copy }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-white px-5 py-20 text-[#071013] lg:px-8 lg:py-24">
            <div class="mx-auto max-w-7xl">
                <div class="grid gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:items-start">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-[#497112]">SI delivery model</p>
                        <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">A stronger handover for every technology project.</h2>
                        <p class="mt-5 text-base leading-7 text-slate-600">New systems create new data responsibilities. SIs can use DPOSync Zim to help clients identify personal data processing, assign DPO responsibility, prepare records, and maintain compliance after go-live.</p>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach([
                            ['Before implementation', 'Record the client, processing purposes, likely data categories, systems, vendors, and privacy risks.'],
                            ['During implementation', 'Align forms, roles, policies, and ROPA entries with the solution being deployed.'],
                            ['After implementation', 'Keep documents, incidents, payments, and evidence available as a managed compliance service.'],
                            ['Across clients', 'Use one login to support multiple organisations while keeping each workspace cleanly separated.'],
                        ] as [$title, $copy])
                            <article class="border border-slate-200 bg-[#f8faf7] p-6">
                                <h3 class="text-xl font-black">{{ $title }}</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-600">{{ $copy }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>

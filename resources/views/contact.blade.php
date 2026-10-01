<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Data Protection Support · DPOSync Zim</title>
    @include('partials.public-seo', [
        'title' => 'Contact Data Protection Support · DPOSync Zim',
        'description' => 'Contact DPOSync Zim for data protection, DPO, system integrator, and organisation privacy compliance support in Zimbabwe.',
        'canonical' => route('contact'),
    ])
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#071013] text-white antialiased">
    @include('partials.public-nav')

    <main>
        <section class="relative overflow-hidden px-5 pb-20 pt-32 lg:px-8 lg:pt-40">
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_74%_18%,rgba(185,255,79,0.2),transparent_30%),linear-gradient(135deg,#071013_0%,#123039_58%,#071013_100%)]"></div>
            <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-[0.88fr_1.12fr] lg:items-start">
                <div>
                    <p class="mb-5 text-sm font-black uppercase tracking-[0.24em] text-[#b9ff4f]">Contact DPOSync Zim</p>
                    <h1 class="max-w-4xl text-5xl font-black leading-[0.96] tracking-tight sm:text-6xl lg:text-7xl">Bring your data protection operations into one workspace.</h1>
                    <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-300">Talk to us if you are a DPO, an SI supporting client systems, or an organisation that needs a cleaner way to manage Zimbabwean data protection workflows.</p>
                </div>

                <div class="grid gap-4">
                    <a href="mailto:info@kodomotech.org" class="border border-white/10 bg-white/[0.06] p-6 transition hover:border-[#b9ff4f]">
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-[#b9ff4f]">Email</p>
                        <h2 class="mt-4 text-3xl font-black">info@kodomotech.org</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-300">Use email for onboarding, DPO support, SI partnership, and platform questions.</p>
                    </a>
                    <a href="{{ route('project-brief.create') }}" class="border border-[#b9ff4f] bg-[#b9ff4f] p-6 text-[#071013] transition hover:bg-white">
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-[#35550c]">Structured request</p>
                        <h2 class="mt-4 text-3xl font-black">Describe your compliance need</h2>
                        <p class="mt-3 text-sm leading-6 text-[#26350f]">Share the organisation type, number of clients, DPO status, SI role, and the workflows you want to manage.</p>
                    </a>
                </div>
            </div>
        </section>

        <section class="bg-[#f6f8f4] px-5 py-20 text-[#071013] lg:px-8 lg:py-24">
            <div class="mx-auto grid max-w-7xl gap-4 md:grid-cols-3">
                @foreach([
                    ['DPO onboarding', 'Set up your officer profile, client organisations, and first DP workflows.'],
                    ['SI partnerships', 'Add DPOSync Zim to client implementations and recurring managed compliance services.'],
                    ['Organisation support', 'Prepare records, policy material, incidents, and appointment evidence with a clear operating rhythm.'],
                ] as [$title, $copy])
                    <article class="border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black">{{ $title }}</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $copy }}</p>
                    </article>
                @endforeach
            </div>
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>

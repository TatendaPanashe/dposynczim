<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Protection Compliance Platform · DPOSync Zim</title>
    @include('partials.public-seo', [
        'title' => 'Data Protection Compliance Platform · DPOSync Zim',
        'description' => 'Explore the DPOSync Zim data protection platform for DPO profiles, SI client workspaces, DP1, DP2, ROPA, privacy policies, incidents, payments, and POTRAZ-ready submissions in Zimbabwe.',
        'canonical' => route('products'),
    ])
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#071013] text-white antialiased">
    @include('partials.public-nav')

    <main>
        <section class="relative overflow-hidden px-5 pb-20 pt-32 lg:px-8 lg:pt-40">
            <div class="absolute inset-0 -z-10 bg-[linear-gradient(135deg,#071013_0%,#102d35_52%,#16300f_100%)]"></div>
            <div class="mx-auto max-w-7xl">
                <p class="mb-5 text-sm font-black uppercase tracking-[0.24em] text-[#b9ff4f]">Platform</p>
                <h1 class="max-w-5xl text-5xl font-black leading-[0.96] tracking-tight sm:text-6xl lg:text-7xl">One data protection platform for the privacy work that usually gets scattered.</h1>
                <p class="mt-7 max-w-3xl text-lg leading-8 text-slate-300">DPOSync Zim brings the core data protection operations into a single workflow so DPOs and SIs can support organisations with speed, structure, and evidence.</p>
            </div>
        </section>

        <section class="bg-[#f6f8f4] px-5 py-20 text-[#071013] lg:px-8 lg:py-24">
            <div class="mx-auto grid max-w-7xl gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach([
                    ['DPO profile', 'Officer readiness', 'Store official contact details, qualifications, certification status, reporting line, and appointment details for reuse across client records.', route('register')],
                    ['Organisation workspaces', 'SI portfolio control', 'Create separate spaces for each client or business unit so forms, evidence, payments, and incidents never bleed together.', route('register')],
                    ['DP1 registration', 'Controller registration', 'Capture organisation details, processing context, DPO information, and the supporting content needed for registration workflows.', route('login')],
                    ['DP2 appointment', 'DPO appointment', 'Prepare appointed officer details, reporting lines, qualifications, declaration details, payment access, and downloadable appointment evidence.', route('login')],
                    ['ROPA and policies', 'Operational records', 'Maintain processing records and generate privacy policy material from structured organisational information.', route('login')],
                    ['Incidents and DP3', 'Breach response', 'Record data breach incidents, prepare evidence, manage access payments, and keep regulator-facing incident records available.', route('login')],
                ] as [$name, $category, $copy, $href])
                    <a href="{{ $href }}" class="group flex min-h-72 flex-col justify-between border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-[#b9ff4f] hover:shadow-xl hover:shadow-slate-200/70">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-[#497112]">{{ $category }}</p>
                            <h2 class="mt-8 text-3xl font-black tracking-tight">{{ $name }}</h2>
                            <p class="mt-4 text-sm leading-6 text-slate-600">{{ $copy }}</p>
                        </div>
                        <span class="mt-8 text-sm font-black text-[#497112] group-hover:text-[#071013]">Open workspace</span>
                    </a>
                @endforeach
            </div>
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>

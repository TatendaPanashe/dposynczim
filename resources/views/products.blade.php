<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Compliance Engine Platform · DPOSync Zim</title>
    @include('partials.public-seo', [
        'title' => 'Compliance Engine Platform · DPOSync Zim',
        'description' => 'Explore the DPOSync Zim compliance engine for organisation workspaces, obligations, calendar tasks, evidence, reports, DPO records, DP1, DP2, ROPA, privacy policies, incidents, and $1.99 monthly form downloads.',
        'canonical' => route('products'),
    ])
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#071013] text-white antialiased">
    @include('partials.public-nav')

    <main>
        <section class="relative overflow-hidden px-5 pb-20 pt-32 lg:px-8 lg:pt-40">
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_82%_18%,rgba(147,197,253,0.28),transparent_30%),linear-gradient(135deg,#071013_0%,#082f73_54%,#123039_100%)]"></div>
            <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
                <div>
                    <p class="mb-5 text-sm font-black uppercase tracking-[0.24em] text-blue-200">Platform</p>
                    <h1 class="max-w-5xl text-5xl font-black leading-[0.96] tracking-tight sm:text-6xl lg:text-7xl">The compliance engine behind every workspace.</h1>
                    <p class="mt-7 max-w-3xl text-lg leading-8 text-slate-300">DPOSync Zim brings obligations, calendar tasks, evidence, reporting, DPO records, privacy forms, incidents, payments, and regulator-ready outputs into one structured platform.</p>
                    <div class="mt-9 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="bg-white px-6 py-4 text-sm font-black uppercase tracking-wide text-[#082f73] shadow-[0_18px_60px_rgba(147,197,253,0.22)] transition hover:bg-blue-100">Create workspace</a>
                        <a href="{{ route('services') }}" class="border border-white/20 bg-white/5 px-6 py-4 text-sm font-black uppercase tracking-wide text-white transition hover:border-white hover:bg-white/10">How it works</a>
                    </div>
                </div>

                <div class="border border-white/12 bg-white/[0.08] p-5 shadow-2xl shadow-black/30 backdrop-blur">
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach([
                            ['Obligations', 'Calendar, owners, due dates'],
                            ['Evidence', 'Files, logs, downloads'],
                            ['Privacy', 'DP1, DP2, ROPA, DP3'],
                            ['$1.99 / month', 'Unlock form downloads'],
                        ] as [$title, $copy])
                            <div class="border border-white/10 bg-white/[0.06] p-5">
                                <h2 class="text-2xl font-black">{{ $title }}</h2>
                                <p class="mt-2 text-sm font-bold text-blue-100">{{ $copy }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 border border-blue-200 bg-blue-100 p-5 text-blue-950">
                        <p class="text-xs font-black uppercase tracking-[0.2em]">Download access</p>
                        <p class="mt-2 text-3xl font-black">$1.99 monthly</p>
                        <p class="mt-2 text-sm leading-6">Pay once per workspace for the calendar month to download official forms and document exports covered by the app.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-[#f6f8f4] px-5 py-20 text-[#071013] lg:px-8 lg:py-24">
            <div class="mx-auto max-w-7xl">
                <div class="max-w-3xl">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-800">Platform modules</p>
                    <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">Everything connects back to the organisation workspace.</h2>
                    <p class="mt-5 text-base leading-7 text-slate-600">Each module helps teams move from loose compliance admin to a managed record of obligations, evidence, and regulator-facing outputs.</p>
                </div>

                <div class="mt-12 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach([
                    ['Organisation workspaces', 'Workspace foundation', 'Create separate spaces for each client, subsidiary, department, or business unit so obligations, evidence, payments, incidents, and records never bleed together.', route('register')],
                    ['Compliance calendar', 'Obligations engine', 'Track recurring and one-off duties with owners, due dates, checklist items, reminders, waivers, completion history, and CSV exports.', route('register')],
                    ['Tasks and evidence', 'Operational control', 'Turn obligations into actionable work, attach proof, log activity, and keep the audit trail close to the compliance item it supports.', route('login')],
                    ['DPO and DP workflows', 'Privacy readiness', 'Maintain DPO profiles, DP1 registrations, DP2 appointments, DP3 incident records, ROPA exports, privacy policies, and POTRAZ-ready submissions.', route('login')],
                    ['Reports and dashboards', 'Management visibility', 'Review readiness, overdue work, upcoming obligations, evidence gaps, completed items, and organisation-level compliance status.', route('login')],
                    ['$1.99 monthly downloads', 'Simple access pricing', 'Pay $1.99 once per workspace for the current month to download available official forms and exports without paying for each separate document.', route('register')],
                ] as [$name, $category, $copy, $href])
                    <a href="{{ $href }}" class="group flex min-h-72 flex-col justify-between border border-blue-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-blue-500 hover:shadow-xl hover:shadow-blue-100">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-blue-800">{{ $category }}</p>
                            <h2 class="mt-8 text-3xl font-black tracking-tight">{{ $name }}</h2>
                            <p class="mt-4 text-sm leading-6 text-slate-600">{{ $copy }}</p>
                        </div>
                        <span class="mt-8 text-sm font-black text-blue-800 group-hover:text-[#071013]">Open workspace</span>
                    </a>
                @endforeach
                </div>
            </div>
        </section>

        <section class="bg-white px-5 py-20 text-[#071013] lg:px-8 lg:py-24">
            <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.85fr_1.15fr] lg:items-start">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-800">Download pricing</p>
                    <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">$1.99 monthly access for form downloads.</h2>
                    <p class="mt-5 text-base leading-7 text-slate-600">The platform lets teams prepare records freely in the workspace. When a workspace needs official document downloads or supported regulator-facing outputs, monthly access unlocks downloads for that workspace for the current calendar month.</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    @foreach([
                        ['One monthly payment', 'Pay $1.99 once for the selected workspace in the current calendar month.'],
                        ['Download available forms', 'Use the monthly access to download the official forms and document exports supported inside the app.'],
                        ['Workspace-based access', 'Each organisation workspace keeps its own payment status, records, forms, and exports separate.'],
                        ['Built for repeat use', 'Prepare, review, update, download, and submit records as compliance work continues through the month.'],
                    ] as [$title, $copy])
                        <article class="border border-blue-100 bg-blue-50 p-6">
                            <h3 class="text-xl font-black">{{ $title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $copy }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-[#082f73] px-5 py-20 text-white lg:px-8 lg:py-24">
            <div class="mx-auto max-w-7xl">
                <div class="max-w-3xl">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-200">Built for compliance operations</p>
                    <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">Not just forms. A working record of what your organisation did.</h2>
                </div>
                <div class="mt-12 grid gap-4 md:grid-cols-4">
                    @foreach([
                        ['Plan', 'Know what is due and who owns it.'],
                        ['Do', 'Complete tasks, workflows, and checklists.'],
                        ['Prove', 'Attach evidence and keep activity history.'],
                        ['Report', 'See readiness, gaps, and outputs.'],
                    ] as [$title, $copy])
                        <article class="border border-white/10 bg-white/[0.06] p-6">
                            <h3 class="text-xl font-black">{{ $title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-blue-100">{{ $copy }}</p>
                        </article>
                    @endforeach
                </div>
                <div class="mt-12 flex flex-col gap-4 border border-white/10 bg-white/[0.06] p-6 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.2em] text-blue-200">Ready to run it?</p>
                        <h3 class="mt-2 text-3xl font-black">Create a workspace and unlock downloads when you need them.</h3>
                    </div>
                    <a href="{{ route('register') }}" class="inline-flex bg-white px-6 py-4 text-sm font-black uppercase tracking-wide text-blue-900 transition hover:bg-blue-100">Start now</a>
                </div>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>

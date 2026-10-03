<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>How DPOSync Zim Works · Compliance Engine Walkthrough</title>
    @include('partials.public-seo', [
        'title' => 'How DPOSync Zim Works · Compliance Engine Walkthrough',
        'description' => 'See how DPOSync Zim works step by step: create organisations, load compliance obligations, manage tasks and evidence, complete DP workflows, report status, and prepare regulator-ready records.',
        'canonical' => route('services'),
    ])
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#071013] text-white antialiased">
    @include('partials.public-nav')

    <main>
        <section class="relative overflow-hidden px-5 pb-20 pt-32 lg:px-8 lg:pt-40">
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_78%_18%,rgba(59,130,246,0.32),transparent_28%),linear-gradient(135deg,#071013_0%,#123039_54%,#082f73_100%)]"></div>
            <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-[0.85fr_1.15fr] lg:items-center">
                <div>
                    <p class="mb-5 text-sm font-black uppercase tracking-[0.24em] text-[#93c5fd]">How it works</p>
                    <h1 class="max-w-4xl text-5xl font-black leading-[0.96] tracking-tight sm:text-6xl lg:text-7xl">From scattered compliance work to one operating engine.</h1>
                    <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-300">DPOSync Zim gives each organisation a controlled workflow for obligations, calendar deadlines, tasks, evidence, DPO records, privacy forms, incidents, reports, and regulator-ready submissions.</p>
                    <div class="mt-9 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="bg-white px-6 py-4 text-sm font-black uppercase tracking-wide text-[#082f73] shadow-[0_18px_60px_rgba(147,197,253,0.22)] transition hover:bg-[#dbeafe]">Start setup</a>
                        <a href="{{ route('products') }}" class="border border-white/20 bg-white/5 px-6 py-4 text-sm font-black uppercase tracking-wide text-white transition hover:border-white hover:bg-white/10">See platform</a>
                    </div>
                </div>

                <div class="border border-white/12 bg-white/[0.08] p-4 shadow-2xl shadow-black/30 backdrop-blur md:p-6">
                    <div class="border border-white/10 bg-[#071013]/80 p-5">
                        <div class="flex items-center justify-between border-b border-white/10 pb-4">
                            <div>
                                <p class="text-xs font-black uppercase tracking-[0.2em] text-blue-200">Compliance cockpit</p>
                                <h2 class="mt-2 text-2xl font-black">Organisation readiness</h2>
                            </div>
                            <span class="bg-blue-100 px-3 py-1 text-xs font-black text-blue-900">Live</span>
                        </div>
                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            @foreach([
                                ['86%', 'Ready'],
                                ['12', 'Open tasks'],
                                ['4', 'Due soon'],
                            ] as [$value, $label])
                                <div class="border border-white/10 bg-white/[0.06] p-4">
                                    <p class="text-3xl font-black text-white">{{ $value }}</p>
                                    <p class="mt-1 text-xs font-bold uppercase tracking-wide text-blue-200">{{ $label }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-5 grid gap-3">
                            @foreach([
                                ['Calendar obligation', 'Submit annual policy review evidence', 'Due in 6 days'],
                                ['Privacy workflow', 'DP2 appointment details completed', 'Ready for export'],
                                ['Incident record', 'Access request evidence attached', 'Logged today'],
                            ] as [$type, $title, $status])
                                <div class="grid gap-3 border border-white/10 bg-white/[0.04] p-4 sm:grid-cols-[1fr_auto] sm:items-center">
                                    <div>
                                        <p class="text-xs font-black uppercase tracking-[0.18em] text-blue-200">{{ $type }}</p>
                                        <p class="mt-1 font-bold text-white">{{ $title }}</p>
                                    </div>
                                    <span class="text-sm font-bold text-slate-300">{{ $status }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-[#f6f8f4] px-5 py-20 text-[#071013] lg:px-8 lg:py-24">
            <div class="mx-auto max-w-7xl">
                <div class="grid gap-8 lg:grid-cols-[0.72fr_1.28fr] lg:items-start">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-800">Step by step</p>
                        <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">The app turns compliance into a managed workflow.</h2>
                        <p class="mt-5 text-base leading-7 text-slate-600">Each step creates structure: who owns the work, what evidence is needed, what is overdue, what can be exported, and what leadership can review.</p>
                    </div>
                    <div class="grid gap-4">
                        @foreach([
                            ['01', 'Create an organisation workspace', 'Add the organisation, subsidiary, department, or client you want to manage. Each workspace keeps its own obligations, forms, payments, incidents, and records separate.', ['Entity profile', 'Client separation', 'Workspace switching']],
                            ['02', 'Load obligations into the calendar', 'Use the compliance calendar to track recurring and one-off duties. Add due dates, owners, checklists, reminders, review windows, and the evidence expected for each obligation.', ['Due dates', 'Owners', 'Checklist items']],
                            ['03', 'Assign and complete tasks', 'Teams work from task lists instead of loose messages. Each task can move through progress, waiver, completion, evidence upload, and activity history so the audit trail stays intact.', ['My tasks', 'Status tracking', 'Activity logs']],
                            ['04', 'Run privacy and regulator workflows', 'Complete guided workflows for DPO profiles, DP1 registrations, DP2 appointments, ROPA records, privacy policies, breach incidents, and POTRAZ-facing documents.', ['DP1 / DP2 / DP3', 'ROPA', 'Policies']],
                            ['05', 'Attach evidence and manage access', 'Store supporting documents, export registers, download records, and manage payment-gated access where required. The result is evidence that can be reviewed or submitted.', ['Evidence', 'Downloads', 'Payments']],
                            ['06', 'Report status and close gaps', 'Use reports and dashboards to see overdue obligations, upcoming deadlines, completed work, missing evidence, and organisation-level readiness before a review or submission.', ['Reports', 'Readiness', 'Gaps']],
                        ] as [$number, $title, $copy, $tags])
                            <article class="grid gap-4 border border-blue-100 bg-white p-5 shadow-sm md:grid-cols-[5rem_1fr]">
                                <div class="grid size-16 place-items-center bg-blue-800 font-mono text-xl font-black text-white">{{ $number }}</div>
                                <div>
                                    <h3 class="text-2xl font-black">{{ $title }}</h3>
                                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $copy }}</p>
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @foreach($tags as $tag)
                                            <span class="bg-blue-50 px-3 py-1 text-xs font-black uppercase tracking-wide text-blue-800">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white px-5 py-20 text-[#071013] lg:px-8 lg:py-24">
            <div class="mx-auto max-w-7xl">
                <div class="grid gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:items-start">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-800">Visual workflow</p>
                        <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">Every module feeds the same evidence trail.</h2>
                        <p class="mt-5 text-base leading-7 text-slate-600">The application is not just a form library. Calendar obligations, privacy records, incidents, exports, and reports all connect back to the same organisation workspace.</p>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach([
                            ['1. Workspace', 'The organisation profile anchors everything: obligations, DPO records, documents, users, payments, reports, and incident history.'],
                            ['2. Obligations', 'Calendar items define what must happen, who owns it, when it is due, and which evidence proves completion.'],
                            ['3. Workflows', 'Guided screens capture DP1, DP2, ROPA, privacy policy, incident, and catalogue information in structured records.'],
                            ['4. Outputs', 'Dashboards, reports, downloads, activity logs, and regulator-ready documents show what was done and what still needs attention.'],
                        ] as [$title, $copy])
                            <article class="border border-blue-100 bg-blue-50 p-6">
                                <h3 class="text-xl font-black">{{ $title }}</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-600">{{ $copy }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>

                <div class="mt-14 grid gap-4 border border-blue-100 bg-[#f8fbff] p-5 md:grid-cols-4">
                    @foreach([
                        ['Organisation', 'Profile, users, client context'],
                        ['Calendar', 'Obligations, dates, reminders'],
                        ['Evidence', 'Files, logs, exports, payments'],
                        ['Reports', 'Status, gaps, readiness'],
                    ] as [$title, $copy])
                        <div class="border-l-4 border-blue-700 bg-white p-5 shadow-sm">
                            <h3 class="text-lg font-black">{{ $title }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $copy }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-[#082f73] px-5 py-20 text-white lg:px-8 lg:py-24">
            <div class="mx-auto max-w-7xl">
                <div class="max-w-3xl">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-200">Who sees what</p>
                    <h2 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">Different teams use the same engine from different angles.</h2>
                </div>
                <div class="mt-12 grid gap-4 md:grid-cols-3">
                    @foreach([
                        ['Compliance teams', 'Monitor due dates, complete checklists, attach evidence, update statuses, and prepare management-ready reports.'],
                        ['DPOs and privacy officers', 'Maintain DPO details, DP appointments, processing registers, privacy policies, incidents, and regulator-facing records.'],
                        ['System integrators', 'Manage multiple client workspaces, document data flows, support implementation handover, and package recurring compliance services.'],
                    ] as [$title, $copy])
                        <article class="border border-white/10 bg-white/[0.06] p-6">
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
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-blue-800">End result</p>
                    <h2 class="mt-2 text-3xl font-black tracking-tight lg:text-4xl">A live compliance record your team can actually operate.</h2>
                </div>
                <a href="{{ route('register') }}" class="inline-flex bg-blue-900 px-6 py-4 text-sm font-black uppercase tracking-wide text-white transition hover:bg-blue-700">Create workspace</a>
            </div>
        </section>
    </main>

    @include('partials.public-footer')
</body>
</html>

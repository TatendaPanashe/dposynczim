@extends('layouts.app')

@section('content')
<div class="d-flex flex-column gap-3 flex-lg-row align-items-lg-end justify-content-lg-between mb-4">
    <div>
        <div class="text-uppercase text-primary fw-bold small tracking-wide">Compliance calendar</div>
        <h1 class="display-6 fw-bold mb-2">Obligations calendar</h1>
        <p class="text-secondary mb-0">A live calendar view of regulatory, contractual, internal and data-protection work.</p>
    </div>
    <div class="btn-group flex-wrap" role="group" aria-label="Calendar controls">
        <a href="{{ route('compliance.calendar.index', array_merge(request()->query(), ['month' => $calendarMonth->subMonth()->format('Y-m-01')])) }}" class="btn btn-outline-secondary"><i class="bi bi-chevron-left"></i> Previous</a>
        <a href="{{ route('compliance.calendar.index', array_merge(request()->query(), ['month' => now()->format('Y-m-01')])) }}" class="btn btn-outline-secondary"><i class="bi bi-dot"></i> Today</a>
        <a href="{{ route('compliance.calendar.index', array_merge(request()->query(), ['month' => $calendarMonth->addMonth()->format('Y-m-01')])) }}" class="btn btn-outline-secondary">Next <i class="bi bi-chevron-right"></i></a>
        <a href="{{ route('compliance.calendar.export', request()->query()) }}" class="btn btn-dark"><i class="bi bi-download"></i> Export CSV</a>
    </div>
</div>

<section class="row g-3 mb-4">
    <div class="col-6 col-xl"><div class="metric-card"><div class="metric-label"><i class="bi bi-activity me-1"></i>Active</div><p class="metric-value">{{ $calendarStats['active'] }}</p></div></div>
    <div class="col-6 col-xl"><div class="metric-card"><div class="metric-label"><i class="bi bi-calendar-day me-1"></i>Due today</div><p class="metric-value">{{ $calendarStats['dueToday'] }}</p></div></div>
    <div class="col-6 col-xl"><div class="metric-card"><div class="metric-label"><i class="bi bi-clock-history me-1"></i>Due in 7 days</div><p class="metric-value">{{ $calendarStats['due7'] }}</p></div></div>
    <div class="col-6 col-xl"><div class="metric-card"><div class="metric-label"><i class="bi bi-exclamation-octagon me-1"></i>Overdue</div><p class="metric-value text-danger">{{ $calendarStats['overdue'] }}</p></div></div>
    <div class="col-12 col-xl"><div class="metric-card"><div class="metric-label"><i class="bi bi-graph-up-arrow me-1"></i>Score</div><p class="metric-value">{{ $calendarStats['score'] }}%</p></div></div>
</section>

<section class="row g-4">
    <div class="col-xl-8">
        <div class="bootstrap-surface overflow-hidden">
            <div class="d-flex flex-column flex-md-row justify-content-md-between gap-3 align-items-md-center p-4 border-bottom">
                <div>
                    <h2 class="h5 fw-bold mb-1">{{ $calendarMonth->format('F Y') }}</h2>
                    <p class="small text-secondary mb-0">Month view</p>
                </div>
                <div class="d-flex flex-wrap gap-3 small text-secondary">
                    <span><span class="badge rounded-pill text-bg-success me-1">&nbsp;</span>Completed</span>
                    <span><span class="badge rounded-pill text-bg-warning me-1">&nbsp;</span>Due soon</span>
                    <span><span class="badge rounded-pill text-bg-danger me-1">&nbsp;</span>Overdue</span>
                    <span><span class="badge rounded-pill text-bg-info me-1">&nbsp;</span>In progress</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <div class="calendar-board">
                    <div class="calendar-weekdays">
                        @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                            <div class="calendar-weekday">{{ $day }}</div>
                        @endforeach
                    </div>
                    <div class="calendar-grid">
                        @foreach($calendarDays as $day)
                            @php($items = $monthObligations->get($day->toDateString(), collect()))
                            <div class="calendar-day {{ $day->month === $calendarMonth->month ? '' : 'calendar-day-muted' }}">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="calendar-date {{ $day->isToday() ? 'calendar-date-today' : '' }}">{{ $day->day }}</span>
                                    @if($items->isNotEmpty())
                                        <span class="badge text-bg-light">{{ $items->count() }}</span>
                                    @endif
                                </div>
                                <div class="d-grid gap-1 mt-3">
                                    @foreach($items->take(3) as $item)
                                        @php($chip = $item->status === 'completed' ? 'calendar-chip-completed' : ($item->due_at->isPast() ? 'calendar-chip-overdue' : ($item->due_at->lte(today()->addDays(7)) ? 'calendar-chip-soon' : 'calendar-chip-progress')))
                                        <a href="{{ route('compliance.calendar.show', $item) }}" class="calendar-chip {{ $chip }}">{{ $item->title }}</a>
                                    @endforeach
                                    @if($items->count() > 3)
                                        <span class="small fw-semibold text-secondary">+{{ $items->count() - 3 }} more</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="d-grid gap-4">
            <div class="bootstrap-surface p-4">
                <h2 class="h5 fw-bold mb-3"><i class="bi bi-lightning-charge text-primary me-2"></i>Today's actions</h2>
                <div class="list-group list-group-flush">
                    @forelse($todayActions as $item)
                        <a href="{{ route('compliance.calendar.show', $item) }}" class="list-group-item list-group-item-action px-0">
                            <span class="fw-semibold">{{ $item->title }}</span>
                            <span class="d-block small text-secondary">{{ $item->due_at->format('d M Y') }} · {{ $item->assignedUser?->name ?? 'Unassigned' }}</span>
                        </a>
                    @empty
                        <p class="text-secondary mb-0">No actions due today.</p>
                    @endforelse
                </div>
            </div>

            <div class="bootstrap-surface p-4">
                <h2 class="h5 fw-bold mb-3"><i class="bi bi-shield-exclamation text-danger me-2"></i>High and critical risk</h2>
                <div class="list-group list-group-flush">
                    @forelse($upcomingCritical as $item)
                        <a href="{{ route('compliance.calendar.show', $item) }}" class="list-group-item list-group-item-action px-0">
                            <span class="fw-semibold">{{ $item->title }}</span>
                            <span class="d-block small text-secondary">{{ str($item->risk_level)->headline() }} · {{ $item->due_at->format('d M Y') }}</span>
                        </a>
                    @empty
                        <p class="text-secondary mb-0">No high or critical-risk obligations are upcoming.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

<section class="row g-4 mt-1">
    <div class="col-xl-4">
        <div class="bootstrap-surface p-4">
            <h2 class="h5 fw-bold mb-3"><i class="bi bi-plus-circle text-primary me-2"></i>Add obligation</h2>
            <form method="POST" action="{{ route('compliance.calendar.store') }}" class="d-grid gap-3">
                @csrf
                <select name="template_id" class="form-select">
                    <option value="">Custom obligation</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}">{{ $template->title }} · {{ $template->short_code }}</option>
                    @endforeach
                </select>
                <input name="title" class="form-control" placeholder="Custom title">
                <input name="category" class="form-control" placeholder="Custom category">
                <input name="due_at" type="date" class="form-control" required>
                <div class="row g-3">
                    <div class="col-md-6 col-xl-12"><select name="assigned_user_id" class="form-select"><option value="">Owner</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></div>
                    <div class="col-md-6 col-xl-12"><select name="reviewer_user_id" class="form-select"><option value="">Reviewer</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6 col-xl-12"><select name="frequency" class="form-select"><option value="once-off">Once-off</option><option value="monthly">Monthly</option><option value="quarterly">Quarterly</option><option value="semi-annual">Semi-annual</option><option value="annual">Annual</option><option value="custom">Custom</option></select></div>
                    <div class="col-md-6 col-xl-12"><select name="risk_level" class="form-select"><option value="medium">Medium</option><option value="low">Low</option><option value="high">High</option><option value="critical">Critical</option></select></div>
                </div>
                <textarea name="notes" class="form-control" rows="3" placeholder="Notes"></textarea>
                <button class="btn btn-dark"><i class="bi bi-calendar-plus"></i> Add to calendar</button>
            </form>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="bootstrap-surface overflow-hidden">
            <form method="GET" class="row g-3 align-items-end p-4 border-bottom">
                <input type="hidden" name="month" value="{{ $calendarMonth->format('Y-m-01') }}">
                <div class="col-md-4 col-xl-2"><label class="form-label small fw-bold">Status</label><select name="status" class="form-select"><option value="">All</option>@foreach(['not_started','in_progress','submitted','completed','overdue','waived'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ str($status)->headline() }}</option>@endforeach</select></div>
                <div class="col-md-4 col-xl-2"><label class="form-label small fw-bold">Category</label><select name="category" class="form-select"><option value="">All</option>@foreach($categories as $category)<option value="{{ $category->name }}" @selected(($filters['category'] ?? '') === $category->name)>{{ $category->name }}</option>@endforeach</select></div>
                <div class="col-md-4 col-xl-2"><label class="form-label small fw-bold">Risk</label><select name="risk_level" class="form-select"><option value="">All</option>@foreach(['low','medium','high','critical'] as $risk)<option value="{{ $risk }}" @selected(($filters['risk_level'] ?? '') === $risk)>{{ str($risk)->headline() }}</option>@endforeach</select></div>
                <div class="col-md-4 col-xl-2"><label class="form-label small fw-bold">From</label><input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control"></div>
                <div class="col-md-4 col-xl-2"><label class="form-label small fw-bold">To</label><input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control"></div>
                <div class="col-md-4 col-xl-2"><button class="btn btn-outline-secondary w-100"><i class="bi bi-funnel"></i> Filter</button></div>
            </form>

            <div class="list-group list-group-flush">
                @forelse($obligations as $obligation)
                    @php($badge = $obligation->status === 'completed' ? 'success' : ($obligation->due_at->isPast() ? 'danger' : ($obligation->due_at->lte(today()->addDays(7)) ? 'warning' : 'info')))
                    <a href="{{ route('compliance.calendar.show', $obligation) }}" class="list-group-item list-group-item-action p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-md-between gap-3">
                            <div>
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <span class="badge text-bg-{{ $badge }}">{{ str($obligation->status)->headline() }}</span>
                                    <span class="badge text-bg-light">{{ $obligation->category }}</span>
                                    <span class="badge text-bg-light">{{ str($obligation->risk_level)->headline() }}</span>
                                </div>
                                <div class="fw-bold">{{ $obligation->title }}</div>
                                <div class="small text-secondary">{{ $obligation->regulator ?? 'Internal' }} · {{ $obligation->assignedUser?->name ?? 'Unassigned' }}</div>
                            </div>
                            <div class="fw-bold text-nowrap"><i class="bi bi-calendar-event me-1"></i>{{ $obligation->due_at->format('d M Y') }}</div>
                        </div>
                    </a>
                @empty
                    <div class="p-5 text-center text-secondary">No obligations match the current filters.</div>
                @endforelse
            </div>
            <div class="p-4">{{ $obligations->links() }}</div>
        </div>
    </div>
</section>

<section class="bootstrap-surface mt-4 p-4">
    <h2 class="h5 fw-bold mb-3"><i class="bi bi-link-45deg text-primary me-2"></i>Linked data-protection feed</h2>
    <div class="row g-3">
        @forelse($feedItems as $item)
            <div class="col-md-6 col-xl-4">
                <div class="border rounded-4 p-3 h-100">
                    <div class="fw-semibold">{{ $item['title'] }}</div>
                    <div class="small text-secondary">{{ class_basename($item['type']) }} #{{ $item['id'] }} · {{ $item['status'] }} · due {{ \Illuminate\Support\Carbon::parse($item['due_at'])->format('d M Y') }}</div>
                </div>
            </div>
        @empty
            <div class="col-12"><p class="text-secondary mb-0">No dated data-protection records are available for the active organisation yet.</p></div>
        @endforelse
    </div>
</section>
@endsection

@extends('layouts.app')

@section('content')
<x-page-heading eyebrow="Obligation detail" title="{{ $obligation->title }}" description="{{ $obligation->category }} · due {{ $obligation->due_at->format('d M Y') }}">
    <x-slot:actions><a href="{{ route('compliance.calendar.index') }}" class="button-secondary">Back to calendar</a></x-slot:actions>
</x-page-heading>

<section class="grid gap-6 xl:grid-cols-[1fr_0.7fr]">
    <div class="surface p-5">
        @if($canManageWorkspace)
        <form method="POST" action="{{ route('compliance.calendar.update', $obligation) }}" class="grid gap-4">
            @csrf @method('PUT')
            <div class="grid gap-3 sm:grid-cols-3">
                <select name="status" class="form-control">@foreach(['not_started','in_progress','submitted','completed','overdue','waived'] as $status)<option value="{{ $status }}" @selected($obligation->status === $status)>{{ str($status)->headline() }}</option>@endforeach</select>
                <select name="assigned_user_id" class="form-control"><option value="">Owner</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected($obligation->assigned_user_id === $user->id)>{{ $user->name }}</option>@endforeach</select>
                <select name="reviewer_user_id" class="form-control"><option value="">Reviewer</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected($obligation->reviewer_user_id === $user->id)>{{ $user->name }}</option>@endforeach</select>
            </div>
            <input type="date" name="due_at" value="{{ $obligation->due_at->toDateString() }}" class="form-control">
            <textarea name="notes" rows="4" class="form-control">{{ old('notes', $obligation->notes) }}</textarea>
            <button class="button-primary">Save changes</button>
        </form>
        @else
            <div class="grid gap-3 sm:grid-cols-3">
                <div class="border border-slate-200 bg-blue-50 p-4"><p class="text-xs font-bold uppercase tracking-wide text-slate-500">Status</p><p class="mt-2 font-bold">{{ str($obligation->status)->headline() }}</p></div>
                <div class="border border-slate-200 bg-blue-50 p-4"><p class="text-xs font-bold uppercase tracking-wide text-slate-500">Owner</p><p class="mt-2 font-bold">{{ $obligation->assignedUser?->name ?? 'Unassigned' }}</p></div>
                <div class="border border-slate-200 bg-blue-50 p-4"><p class="text-xs font-bold uppercase tracking-wide text-slate-500">Reviewer</p><p class="mt-2 font-bold">{{ $obligation->reviewer?->name ?? 'Not set' }}</p></div>
            </div>
            @if($obligation->notes)
                <div class="mt-4 border border-slate-200 p-4 text-sm leading-6 text-slate-600">{{ $obligation->notes }}</div>
            @endif
        @endif

        <form method="POST" action="{{ route('compliance.calendar.checklist', $obligation) }}" class="mt-6 border-t border-slate-200 pt-5">
            @csrf
            <h2 class="font-bold">Checklist</h2>
            <div class="mt-3 grid gap-2">
                @foreach($obligation->checklistItems as $item)
                    <label class="flex items-center gap-3 text-sm"><input type="checkbox" name="checklist[]" value="{{ $item->id }}" @checked($item->is_completed)> {{ $item->title }}</label>
                @endforeach
            </div>
            <button class="button-secondary mt-4">Save checklist</button>
        </form>
    </div>

    <div class="grid gap-6">
        <form method="POST" enctype="multipart/form-data" action="{{ route('compliance.calendar.complete', $obligation) }}" class="surface p-5">
            @csrf
            <h2 class="font-bold">Complete with evidence</h2>
            <input type="file" name="evidence" class="form-control mt-4">
            <textarea name="notes" rows="3" class="form-control mt-3" placeholder="Completion notes"></textarea>
            <button class="button-primary mt-3 w-full">Mark completed</button>
        </form>
        <form method="POST" action="{{ route('compliance.calendar.waive', $obligation) }}" class="surface p-5">
            @csrf
            <h2 class="font-bold">Waive occurrence</h2>
            <textarea name="waiver_reason" rows="3" class="form-control mt-4" placeholder="Mandatory waiver reason" required></textarea>
            <button class="button-secondary mt-3 w-full">Waive</button>
        </form>
        <div class="surface p-5">
            <h2 class="font-bold">Evidence and activity</h2>
            <div class="mt-4 grid gap-2 text-sm">
                @foreach($obligation->evidence as $evidence)<div class="border border-slate-200 p-3">{{ $evidence->label }}<span class="block text-xs text-slate-500">{{ $evidence->created_at->format('d M Y H:i') }}</span></div>@endforeach
                @foreach($obligation->activityLogs as $log)<div class="border border-slate-200 p-3">{{ str($log->action)->headline() }}<span class="block text-xs text-slate-500">{{ $log->created_at->format('d M Y H:i') }}</span></div>@endforeach
            </div>
        </div>
    </div>
</section>
@endsection

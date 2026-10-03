@extends('layouts.app')

@section('content')
<x-page-heading eyebrow="Assigned work" title="My compliance tasks" description="Open obligations assigned to you or awaiting your review." />
<div class="surface divide-y divide-slate-100">
    @forelse($obligations as $obligation)
        <a href="{{ route('compliance.calendar.show', $obligation) }}" class="flex items-center justify-between px-5 py-4 hover:bg-slate-50"><span><strong>{{ $obligation->title }}</strong><span class="block text-sm text-slate-500">{{ $obligation->category }} · {{ str($obligation->status)->headline() }}</span></span><span class="text-sm font-bold">{{ $obligation->due_at->format('d M Y') }}</span></a>
    @empty
        <div class="p-8 text-center text-sm text-slate-500">No open tasks assigned to you.</div>
    @endforelse
</div>
<div class="mt-4">{{ $obligations->links() }}</div>
@endsection

@extends('layouts.app')

@section('content')
<x-page-heading eyebrow="Admin" title="Compliance catalogue" description="Editable obligation templates used when organisations subscribe to calendar work." />
<section class="grid gap-6 xl:grid-cols-[0.7fr_1.3fr]">
    <form method="POST" action="{{ route('compliance.catalogue.store') }}" class="surface grid gap-3 p-5">
        @csrf
        <input name="title" class="form-control" placeholder="Template title" required>
        <input name="short_code" class="form-control" placeholder="Short code" required>
        <select name="compliance_category_id" class="form-control"><option value="">Category</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select>
        <input name="regulator" class="form-control" placeholder="Regulator">
        <input name="jurisdiction" class="form-control" placeholder="Jurisdiction" value="Zimbabwe/SADC">
        <select name="frequency" class="form-control"><option value="annual">Annual</option><option value="monthly">Monthly</option><option value="quarterly">Quarterly</option><option value="semi-annual">Semi-annual</option><option value="once-off">Once-off</option><option value="custom">Custom</option></select>
        <select name="risk_level" class="form-control"><option value="medium">Medium</option><option value="low">Low</option><option value="high">High</option><option value="critical">Critical</option></select>
        <input name="reminder_days" class="form-control" value="30,14,7,1">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="evidence_required" value="1" checked> Evidence required</label>
        <textarea name="description" rows="3" class="form-control" placeholder="Description"></textarea>
        <textarea name="checklist_items" rows="4" class="form-control" placeholder="Checklist items, one per line"></textarea>
        <button class="button-primary">Add template</button>
    </form>
    <div class="surface divide-y divide-slate-100">
        @foreach($templates as $template)
            <div class="px-5 py-4"><div class="flex flex-wrap items-center gap-2"><strong>{{ $template->title }}</strong><span class="text-xs font-bold text-slate-500">{{ $template->short_code }}</span><x-status :label="$template->is_active ? 'Active' : 'Inactive'" :tone="$template->is_active ? 'green' : 'amber'" /></div><p class="mt-1 text-sm text-slate-500">{{ $template->category?->name }} · {{ $template->regulator }} · {{ str($template->risk_level)->headline() }}</p></div>
        @endforeach
        <div class="p-4">{{ $templates->links() }}</div>
    </div>
</section>
@endsection

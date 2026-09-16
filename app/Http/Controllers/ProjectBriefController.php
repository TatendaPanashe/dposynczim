<?php

namespace App\Http\Controllers;

use App\Mail\ProjectBriefSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ProjectBriefController extends Controller
{
    public function create(): View
    {
        return view('project-brief.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'organization' => ['nullable', 'string', 'max:160'],
            'service' => ['required', 'string', 'in:data-protection,ai-automation,software-development,networking,mixed'],
            'budget' => ['nullable', 'string', 'max:80'],
            'timeline' => ['nullable', 'string', 'max:80'],
            'project_goal' => ['required', 'string', 'max:1200'],
            'features' => ['nullable', 'string', 'max:1800'],
            'references' => ['nullable', 'string', 'max:1000'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        Mail::to(config('mail.project_brief_to'))->send(
            new ProjectBriefSubmitted(
                brief: $validated,
                images: $request->file('images', []),
            )
        );

        return redirect()
            ->route('project-brief.create')
            ->with('status', 'Thanks. Your project brief has been sent to Kodomo Technologies.');
    }
}

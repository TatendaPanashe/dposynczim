<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Send Kodomo Technologies a detailed project brief with your requirements and reference images.">
    <title>Start a Project · Kodomo Technologies</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#081114] text-white antialiased">
    <header class="border-b border-white/10 bg-[#081114]/88">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                <span class="grid size-10 place-items-center bg-[#b7ff4a] text-sm font-black text-[#081114]">KT</span>
                <span>
                    <strong class="block text-sm tracking-wide">Kodomo Technologies</strong>
                    <small class="block text-[11px] uppercase tracking-[0.18em] text-slate-400">Project brief</small>
                </span>
            </a>
            <nav class="hidden items-center gap-6 text-sm font-bold text-slate-300 md:flex">
                <a href="{{ route('home') }}" class="hover:text-white">Home</a>
                <a href="{{ route('services') }}" class="hover:text-white">Services</a>
                <a href="mailto:info@kodomotech.org" class="hover:text-white">info@kodomotech.org</a>
            </nav>
        </div>
    </header>

    <main class="bg-[#f5f7f2] px-5 py-12 text-[#081114] lg:px-8 lg:py-16">
        <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.7fr_1.3fr]">
            <section>
                <p class="text-sm font-black uppercase tracking-[0.22em] text-[#497112]">Start a project</p>
                <h1 class="mt-4 text-4xl font-black tracking-tight lg:text-5xl">Tell us what you want to build.</h1>
                <p class="mt-5 text-base leading-7 text-slate-600">Share the goal, must-have features, timeline, budget range, and any reference images. Your submission is emailed directly to the Kodomo Technologies team.</p>
            </section>

            <section class="border border-[#d7dfcd] bg-white p-6 shadow-sm lg:p-8">
                @if (session('status'))
                    <div class="mb-6 border border-[#b7ff4a] bg-[#f0ffe0] p-4 text-sm font-bold text-[#081114]">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('project-brief.store') }}" enctype="multipart/form-data" class="grid gap-6">
                    @csrf

                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="grid gap-2">
                            <span class="text-sm font-black">Name</span>
                            <input name="name" value="{{ old('name') }}" required class="border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#497112]">
                            @error('name') <span class="text-sm font-bold text-red-700">{{ $message }}</span> @enderror
                        </label>
                        <label class="grid gap-2">
                            <span class="text-sm font-black">Email</span>
                            <input type="email" name="email" value="{{ old('email') }}" required class="border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#497112]">
                            @error('email') <span class="text-sm font-bold text-red-700">{{ $message }}</span> @enderror
                        </label>
                        <label class="grid gap-2">
                            <span class="text-sm font-black">Phone</span>
                            <input name="phone" value="{{ old('phone') }}" class="border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#497112]">
                            @error('phone') <span class="text-sm font-bold text-red-700">{{ $message }}</span> @enderror
                        </label>
                        <label class="grid gap-2">
                            <span class="text-sm font-black">Organization</span>
                            <input name="organization" value="{{ old('organization') }}" class="border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#497112]">
                            @error('organization') <span class="text-sm font-bold text-red-700">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <label class="grid gap-2">
                            <span class="text-sm font-black">Service</span>
                            <select name="service" required class="border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#497112]">
                                <option value="">Select one</option>
                                <option value="data-protection" @selected(old('service') === 'data-protection')>Data protection as a service</option>
                                <option value="ai-automation" @selected(old('service') === 'ai-automation')>AI automation</option>
                                <option value="software-development" @selected(old('service') === 'software-development')>Software development</option>
                                <option value="networking" @selected(old('service') === 'networking')>Networking</option>
                                <option value="mixed" @selected(old('service') === 'mixed')>A mix of services</option>
                            </select>
                            @error('service') <span class="text-sm font-bold text-red-700">{{ $message }}</span> @enderror
                        </label>
                        <label class="grid gap-2">
                            <span class="text-sm font-black">Budget range</span>
                            <input name="budget" value="{{ old('budget') }}" placeholder="Optional" class="border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#497112]">
                            @error('budget') <span class="text-sm font-bold text-red-700">{{ $message }}</span> @enderror
                        </label>
                        <label class="grid gap-2">
                            <span class="text-sm font-black">Timeline</span>
                            <input name="timeline" value="{{ old('timeline') }}" placeholder="Optional" class="border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#497112]">
                            @error('timeline') <span class="text-sm font-bold text-red-700">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <label class="grid gap-2">
                        <span class="text-sm font-black">What do you want built?</span>
                        <textarea name="project_goal" rows="5" required class="border border-slate-300 px-4 py-3 text-sm leading-6 outline-none focus:border-[#497112]">{{ old('project_goal') }}</textarea>
                        @error('project_goal') <span class="text-sm font-bold text-red-700">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2">
                        <span class="text-sm font-black">Features, pages, automations, or integrations</span>
                        <textarea name="features" rows="5" class="border border-slate-300 px-4 py-3 text-sm leading-6 outline-none focus:border-[#497112]">{{ old('features') }}</textarea>
                        @error('features') <span class="text-sm font-bold text-red-700">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2">
                        <span class="text-sm font-black">Reference links or notes</span>
                        <textarea name="references" rows="4" class="border border-slate-300 px-4 py-3 text-sm leading-6 outline-none focus:border-[#497112]">{{ old('references') }}</textarea>
                        @error('references') <span class="text-sm font-bold text-red-700">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2">
                        <span class="text-sm font-black">Reference images</span>
                        <input type="file" name="images[]" accept="image/png,image/jpeg,image/webp" multiple class="border border-dashed border-slate-300 bg-[#f8faf7] px-4 py-5 text-sm file:mr-4 file:border-0 file:bg-[#081114] file:px-4 file:py-2 file:text-sm file:font-black file:text-white">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Up to 5 images, 5MB each. JPG, PNG, or WebP.</span>
                        @error('images') <span class="text-sm font-bold text-red-700">{{ $message }}</span> @enderror
                        @error('images.*') <span class="text-sm font-bold text-red-700">{{ $message }}</span> @enderror
                    </label>

                    <button class="bg-[#081114] px-6 py-4 text-sm font-black text-white transition hover:bg-[#497112]">Send project brief</button>
                </form>
            </section>
        </div>
    </main>
</body>
</html>

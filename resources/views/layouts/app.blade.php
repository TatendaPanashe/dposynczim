<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Compliance workspace' }} · DPOSync Zim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-body">
    @php
        $dataProtectionOpen = request()->routeIs('compliance.dp1.*', 'compliance.dp2.*', 'compliance.ropa.*', 'compliance.forms.*', 'compliance.incidents.*', 'compliance.privacy-policies.*', 'compliance.dpo-profile.*');
        $adminOpen = request()->routeIs('compliance.organizations.*', 'compliance.users.*', 'compliance.catalogue.*', 'admin.*');
        $canManageWorkspace = auth()->user()?->canManageComplianceWorkspace() ?? false;
    @endphp

    <div class="app-shell d-lg-grid">
        <aside class="app-sidebar offcanvas-lg offcanvas-start flex-shrink-0" tabindex="-1" id="workspaceSidebar" aria-labelledby="workspaceSidebarLabel">
            <div class="offcanvas-header border-bottom border-white border-opacity-10">
                <a href="{{ route('home') }}" class="brand-lockup text-decoration-none">
                    <span class="brand-mark">DZ</span>
                    <span>
                        <span id="workspaceSidebarLabel" class="brand-title">DPOSync Zim</span>
                        <span class="brand-subtitle">Zimbabwe privacy ops</span>
                    </span>
                </a>
                <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#workspaceSidebar" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body flex-column p-3">
                <nav class="nav nav-pills flex-column gap-1">
                    <div class="sidebar-section">Workspace</div>
                    <a href="{{ route('compliance.dashboard') }}" class="sidebar-link {{ request()->routeIs('compliance.dashboard', 'home') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i><span>Overview</span></a>
                    <a href="{{ route('compliance.calendar.index') }}" class="sidebar-link {{ request()->routeIs('compliance.calendar.*') ? 'active' : '' }}"><i class="bi bi-calendar3"></i><span>Calendar</span></a>
                    <a href="{{ route('compliance.tasks.index') }}" class="sidebar-link {{ request()->routeIs('compliance.tasks.*') ? 'active' : '' }}"><i class="bi bi-check2-square"></i><span>My tasks</span></a>
                    @if($canManageWorkspace)
                        <a href="{{ route('compliance.reports.index') }}" class="sidebar-link {{ request()->routeIs('compliance.reports.*') ? 'active' : '' }}"><i class="bi bi-bar-chart-line"></i><span>Reports</span></a>
                    @endif
                </nav>

                @if($canManageWorkspace)
                <div class="accordion sidebar-accordion mt-3" id="sidebarNavigation">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $dataProtectionOpen ? '' : 'collapsed' }}" type="button" data-sidebar-toggle="dataProtectionNav" aria-expanded="{{ $dataProtectionOpen ? 'true' : 'false' }}" aria-controls="dataProtectionNav">
                                <i class="bi bi-shield-lock"></i><span>Data protection</span>
                            </button>
                        </h2>
                        <div id="dataProtectionNav" class="sidebar-panel" data-sidebar-panel @if(! $dataProtectionOpen) hidden @endif>
                            <div class="accordion-body">
                                <a href="{{ route('compliance.dpo-profile.edit') }}" class="sidebar-sublink {{ request()->routeIs('compliance.dpo-profile.*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i>DPO profile</a>
                                <a href="{{ route('compliance.dp1.create') }}" class="sidebar-sublink {{ request()->routeIs('compliance.dp1.*') ? 'active' : '' }}"><i class="bi bi-file-earmark-text"></i>DP1 application</a>
                                <a href="{{ route('compliance.dp2.index') }}" class="sidebar-sublink {{ request()->routeIs('compliance.dp2.*') ? 'active' : '' }}"><i class="bi bi-person-check"></i>DPO appointment</a>
                                <a href="{{ route('compliance.ropa.index') }}" class="sidebar-sublink {{ request()->routeIs('compliance.ropa.*') ? 'active' : '' }}"><i class="bi bi-table"></i>Processing register</a>
                                <a href="{{ route('compliance.forms.index') }}" class="sidebar-sublink {{ request()->routeIs('compliance.forms.*') ? 'active' : '' }}"><i class="bi bi-ui-checks"></i>Compliance forms</a>
                                <a href="{{ route('compliance.incidents.index') }}" class="sidebar-sublink {{ request()->routeIs('compliance.incidents.*') ? 'active' : '' }}"><i class="bi bi-exclamation-triangle"></i>Incidents</a>
                                <a href="{{ route('compliance.privacy-policies.create') }}" class="sidebar-sublink {{ request()->routeIs('compliance.privacy-policies.*') ? 'active' : '' }}"><i class="bi bi-file-lock2"></i>Privacy policies</a>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $adminOpen ? '' : 'collapsed' }}" type="button" data-sidebar-toggle="adminNav" aria-expanded="{{ $adminOpen ? 'true' : 'false' }}" aria-controls="adminNav">
                                <i class="bi bi-gear"></i><span>Administration</span>
                            </button>
                        </h2>
                        <div id="adminNav" class="sidebar-panel" data-sidebar-panel @if(! $adminOpen) hidden @endif>
                            <div class="accordion-body">
                                <a href="{{ route('compliance.organizations.index') }}" class="sidebar-sublink {{ request()->routeIs('compliance.organizations.*') ? 'active' : '' }}"><i class="bi bi-buildings"></i>Organisations</a>
                                <a href="{{ route('compliance.users.index') }}" class="sidebar-sublink {{ request()->routeIs('compliance.users.*') ? 'active' : '' }}"><i class="bi bi-people"></i>Users</a>
                                @if(auth()->user()?->isAdmin())
                                    <a href="{{ route('compliance.catalogue.index') }}" class="sidebar-sublink {{ request()->routeIs('compliance.catalogue.*') ? 'active' : '' }}"><i class="bi bi-collection"></i>Catalogue</a>
                                    <a href="{{ route('admin.dashboard') }}" class="sidebar-sublink {{ request()->routeIs('admin.*') ? 'active' : '' }}"><i class="bi bi-sliders"></i>Admin</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="sidebar-reference mt-auto">
                    <div class="sidebar-section mb-2">Reference</div>
                    <div>Act Chapter 12:07</div>
                    <div>S.I. 155 of 2024</div>
                    <div>DP1 · DP2 · DP3</div>
                </div>
            </div>
        </aside>

        <div class="app-main w-100">
            <header class="app-topbar">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#workspaceSidebar" aria-controls="workspaceSidebar" aria-label="Open navigation">
                        <i class="bi bi-list"></i>
                    </button>
                    <span class="topbar-eyebrow">{{ $eyebrow ?? 'Compliance workspace' }}</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="d-none d-sm-block text-end">
                        <div class="small fw-semibold">{{ auth()->user()?->name ?? 'Preview workspace' }}</div>
                        <div class="topbar-org">{{ auth()->user()?->activeOrganization()?->name ?? 'No client selected' }}</div>
                    </div>
                    <span class="user-avatar">{{ substr(auth()->user()?->name ?? 'D', 0, 1) }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-link btn-sm text-secondary fw-semibold text-decoration-none">Sign out</button>
                    </form>
                </div>
            </header>

            <main class="app-content">
                @if (session('success'))
                    <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm">Please check the marked fields and try again.</div>
                @endif
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

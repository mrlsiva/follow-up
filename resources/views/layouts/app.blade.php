<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $title ?? 'Leadflow CRM' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="brand"><span class="brand-mark">↗</span><span>leadflow</span></a>
        <div class="eyebrow">Workspace</div>
        <nav>
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span>◫</span> Overview</a>
            <a class="nav-link {{ request()->routeIs('leads.*') ? 'active' : '' }}" href="{{ route('leads.index') }}"><span>◌</span> Leads <b>{{ \App\Models\Lead::count() }}</b></a>
            <a class="nav-link" href="{{ route('leads.create') }}"><span>＋</span> Add lead</a>
            <a class="nav-link" href="{{ route('leads.import') }}"><span>⇧</span> Import file</a>
            <div class="eyebrow nav-section-label">Manage</div>
            <a class="nav-link" href="{{ route('settings.sources') }}"><span>◉</span> Lead sources</a>
            <a class="nav-link" href="{{ route('settings.services') }}"><span>◇</span> Services</a>
        </nav>
        <div class="sidebar-bottom">
            <div class="user-chip">
                <span class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</span>
                <span><strong>{{ auth()->user()->name }}</strong><small>Sales workspace</small></span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout" type="submit">Sign out <span>↗</span></button>
            </form>
        </div>
    </aside>
    <main class="main-content">
        <header class="topbar">
            <div class="mobile-brand">leadflow</div>
            <div class="topbar-actions">
                <span class="date-label">{{ now()->format('D, d M Y') }}</span>
                <a class="button button-primary" href="{{ route('leads.create') }}">＋ New lead</a>
            </div>
        </header>
        @if(session('success'))
            <div class="flash">{{ session('success') }}<span>✓</span></div>
        @endif
        @if(($errors ?? collect())->isNotEmpty())
            <div class="flash error">{{ $errors->first() }}<span>!</span></div>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>

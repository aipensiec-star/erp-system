<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ERP Systém')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
<nav class="topnav">
    <a href="{{ route('dashboard') }}" class="brand">ERP Systém</a>
    <div class="nav-links">
        <a href="{{ route('dashboard') }}">Přehled</a>
        <a href="{{ route('companies.index') }}">Firmy</a>
        <a href="{{ route('backups.index') }}">Zálohy</a>
    </div>
    <div class="nav-right">
        <span class="muted">{{ auth()->user()->name ?? '' }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-link">Odhlásit</button>
        </form>
    </div>
</nav>
<main class="container">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @yield('content')
</main>
@stack('scripts')
</body>
</html>

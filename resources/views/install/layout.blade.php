<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalace – ERP Systém</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<main class="container" style="max-width: 720px;">
    <h1 style="text-align:center;">Instalace ERP Systému</h1>
    <div class="install-steps">
        <span class="{{ $step === 1 ? 'active' : '' }}">1. Kontrola prostředí</span>
        <span class="{{ $step === 2 ? 'active' : '' }}">2. Databáze</span>
        <span class="{{ $step === 3 ? 'active' : '' }}">3. Administrátor</span>
        <span class="{{ $step === 4 ? 'active' : '' }}">4. Hotovo</span>
    </div>
    @if (isset($errors) && $errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card">
        @yield('content')
    </div>
</main>
</body>
</html>

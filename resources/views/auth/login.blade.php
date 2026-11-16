<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přihlášení – ERP Systém</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<main class="container" style="max-width: 420px; margin-top: 80px;">
    <div class="card">
        <h1>Přihlášení</h1>
        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Heslo</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group checkbox">
                <label>
                    <input type="checkbox" name="remember"> Zůstat přihlášen
                </label>
            </div>
            <button type="submit" class="btn">Přihlásit se</button>
        </form>
    </div>
</main>
</body>
</html>

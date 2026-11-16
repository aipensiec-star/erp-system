@extends('install.layout', ['step' => 3])

@section('content')
    <h1>Administrátorský účet</h1>
    <form method="POST" action="{{ route('install.account.save') }}">
        @csrf
        <div class="form-group">
            <label for="name">Jméno</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
        </div>
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="password">Heslo (min. 8 znaků)</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Heslo znovu</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
            </div>
        </div>
        <button type="submit" class="btn">Dokončit instalaci</button>
    </form>
@endsection

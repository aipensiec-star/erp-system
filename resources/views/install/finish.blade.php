@extends('install.layout', ['step' => 4])

@section('content')
    <h1>Instalace byla dokončena</h1>
    <p>Systém je připraven k používání. Přihlaste se svým administrátorským účtem.</p>
    <a href="{{ route('login') }}" class="btn">Přihlásit se</a>
@endsection

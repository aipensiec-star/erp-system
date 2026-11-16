@extends('layouts.app')

@section('title', 'Přehled')

@section('content')
    <div class="card">
        <h1>Přehled</h1>
        <p>Vítejte v ERP systému. Zatím je k dispozici základ a CRM – firmy, kontaktní osoby a ověření přes ARES.</p>
        <a href="{{ route('companies.create') }}" class="btn">Přidat firmu</a>
    </div>
@endsection

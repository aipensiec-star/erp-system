@extends('layouts.app')

@section('title', $company->name)

@section('content')
    <div class="card">
        <div class="actions" style="justify-content: space-between;">
            <h1 style="margin: 0;">{{ $company->name }}</h1>
            <div class="actions">
                <a href="{{ route('companies.edit', $company) }}" class="btn btn-secondary">Upravit firmu</a>
                <a href="{{ route('companies.index') }}" class="btn btn-secondary">Zpět na seznam</a>
            </div>
        </div>
        <table style="margin-top: 16px;">
            <tr><th style="width: 200px;">IČO</th><td>{{ $company->ico }}</td></tr>
            <tr><th>DIČ</th><td>{{ $company->dic }} {{ $company->is_vat_payer ? '(plátce DPH)' : '(neplátce DPH)' }}</td></tr>
            <tr><th>Adresa</th><td>{{ $company->street }}, {{ $company->zip }} {{ $company->city }}, {{ $company->country }}</td></tr>
            <tr><th>Bankovní účet</th><td>{{ $company->bank_account }} {{ $company->iban }}</td></tr>
            <tr><th>Platební podmínky</th><td>{{ $company->payment_terms_days }} dní</td></tr>
            @if ($company->note)
                <tr><th>Poznámka</th><td>{{ $company->note }}</td></tr>
            @endif
        </table>
    </div>

    <div class="card">
        <h2 style="margin-top: 0;">Kontaktní osoby</h2>
        @if ($company->contactPersons->isEmpty())
            <p class="muted">Žádné kontaktní osoby.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Jméno</th>
                        <th>Pozice</th>
                        <th>E-mail</th>
                        <th>Telefon</th>
                        <th>Hlavní</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($company->contactPersons as $contactPerson)
                        <tr>
                            <td>{{ $contactPerson->name }}</td>
                            <td>{{ $contactPerson->position }}</td>
                            <td>{{ $contactPerson->email }}</td>
                            <td>{{ $contactPerson->phone }}</td>
                            <td>{{ $contactPerson->is_primary ? '★' : '' }}</td>
                            <td class="actions">
                                <form method="POST" action="{{ route('kontakty.destroy', [$company, $contactPerson]) }}"
                                    onsubmit="return confirm('Smazat kontakt {{ $contactPerson->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Smazat</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <h3 style="margin-bottom: 8px;">Přidat kontaktní osobu</h3>
        <form method="POST" action="{{ route('firmy.kontakty.store', $company) }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="cp-name">Jméno *</label>
                    <input type="text" name="name" id="cp-name" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label for="cp-position">Pozice</label>
                    <input type="text" name="position" id="cp-position" value="{{ old('position') }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="cp-email">E-mail</label>
                    <input type="email" name="email" id="cp-email" value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label for="cp-phone">Telefon</label>
                    <input type="text" name="phone" id="cp-phone" value="{{ old('phone') }}">
                </div>
                <div class="form-group checkbox" style="align-self: end;">
                    <label>
                        <input type="checkbox" name="is_primary" value="1"> Hlavní kontakt
                    </label>
                </div>
            </div>
            <button type="submit" class="btn">Přidat kontakt</button>
        </form>
    </div>
@endsection

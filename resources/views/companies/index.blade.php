@extends('layouts.app')

@section('title', 'Firmy')

@section('content')
    <div class="card">
        <div class="actions" style="justify-content: space-between; margin-bottom: 16px;">
            <h1 style="margin: 0;">Firmy</h1>
            <a href="{{ route('companies.create') }}" class="btn">Přidat firmu</a>
        </div>
        @if ($companies->isEmpty())
            <p class="muted">Zatím nejsou žádné firmy. Začněte přidáním první firmy.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Název</th>
                        <th>IČO</th>
                        <th>DIČ</th>
                        <th>Město</th>
                        <th>Plátce DPH</th>
                        <th>Kontakty</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($companies as $company)
                        <tr>
                            <td><a href="{{ route('companies.show', $company) }}">{{ $company->name }}</a></td>
                            <td>{{ $company->ico }}</td>
                            <td>{{ $company->dic }}</td>
                            <td>{{ $company->city }}</td>
                            <td>{{ $company->is_vat_payer ? 'ano' : 'ne' }}</td>
                            <td>{{ $company->contactPersons->count() }}</td>
                            <td class="actions">
                                <a href="{{ route('companies.edit', $company) }}" class="btn btn-secondary">Upravit</a>
                                <form method="POST" action="{{ route('companies.destroy', $company) }}"
                                    onsubmit="return confirm('Opravdu smazat firmu {{ $company->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Smazat</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top: 16px;">{{ $companies->links() }}</div>
        @endif
    </div>
@endsection

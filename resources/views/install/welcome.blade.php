@extends('install.layout', ['step' => 1])

@section('content')
    <h1>Kontrola prostředí</h1>
    <table>
        @foreach ($requirements as $requirement)
            <tr>
                <td>{{ $requirement['label'] }}</td>
                <td class="{{ $requirement['ok'] ? 'req-ok' : 'req-fail' }}">
                    {{ $requirement['ok'] ? '✓' : '✗' }} {{ $requirement['value'] }}
                </td>
            </tr>
        @endforeach
    </table>
    <p style="margin-top: 16px;">
        @if ($requirementsMet)
            <a href="{{ route('install.database') }}" class="btn">Pokračovat na databázi</a>
        @else
            <span class="req-fail">Některé požadavky nejsou splněny. Opravte je a obnovte stránku.</span>
        @endif
    </p>
@endsection

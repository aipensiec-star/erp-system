@extends('layouts.app')

@section('title', 'Zálohy')

@section('content')
    <div class="card">
        <div class="actions" style="justify-content: space-between; margin-bottom: 16px;">
            <h1 style="margin: 0;">Zálohy systému</h1>
            <form method="POST" action="{{ route('backups.store') }}">
                @csrf
                <button type="submit" class="btn">Vytvořit zálohu</button>
            </form>
        </div>
        <p class="muted">Záloha obsahuje kompletní dump databáze, soubor .env a data v storage/app. Stáhněte si ji a uložte mimo server.</p>
        @if (empty($backups))
            <p class="muted">Zatím nejsou žádné zálohy.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Soubor</th>
                        <th>Vytvořeno</th>
                        <th>Velikost</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($backups as $backup)
                        <tr>
                            <td>{{ $backup['filename'] }}</td>
                            <td>{{ $backup['created_at'] }}</td>
                            <td>{{ number_format($backup['size'] / 1024, 1) }} kB</td>
                            <td class="actions">
                                <a href="{{ route('backups.download', $backup['filename']) }}" class="btn btn-secondary">Stáhnout</a>
                                <form method="POST" action="{{ route('backups.destroy', $backup['filename']) }}"
                                    onsubmit="return confirm('Smazat zálohu {{ $backup['filename'] }}?')">
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
    </div>
@endsection
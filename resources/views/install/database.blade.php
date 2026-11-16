@extends('install.layout', ['step' => 2])

@section('content')
    <h1>Připojení k databázi</h1>
    <form method="POST" action="{{ route('install.database.save') }}">
        @csrf
        <div class="form-group">
            <label for="driver">Typ databáze</label>
            <select name="driver" id="driver" onchange="toggleMysql(this.value)">
                <option value="mysql" selected>MySQL / MariaDB (doporučeno pro hosting)</option>
                <option value="sqlite">SQLite (pro testování)</option>
            </select>
        </div>
        <div id="mysql-fields">
            <div class="form-row">
                <div class="form-group">
                    <label for="host">Server</label>
                    <input type="text" name="host" id="host" value="127.0.0.1">
                </div>
                <div class="form-group">
                    <label for="port">Port</label>
                    <input type="text" name="port" id="port" value="3306">
                </div>
            </div>
            <div class="form-group">
                <label for="database">Název databáze</label>
                <input type="text" name="database" id="database">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Uživatel</label>
                    <input type="text" name="username" id="username">
                </div>
                <div class="form-group">
                    <label for="password">Heslo</label>
                    <input type="password" name="password" id="password">
                </div>
            </div>
        </div>
        <div class="form-group" id="sqlite-fields" style="display: none;">
            <label for="database_sqlite">Soubor databáze (relativně k adresáři database/)</label>
            <input type="text" name="database_sqlite" id="database_sqlite" value="database.sqlite" disabled>
        </div>
        <button type="submit" class="btn">Otestovat připojení a pokračovat</button>
    </form>
    <script>
        function toggleMysql(driver) {
            document.getElementById('mysql-fields').style.display = driver === 'mysql' ? 'block' : 'none';
            document.getElementById('sqlite-fields').style.display = driver === 'sqlite' ? 'block' : 'none';
            document.getElementById('database').disabled = driver !== 'mysql';
            document.getElementById('database_sqlite').disabled = driver !== 'sqlite';
        }
    </script>
@endsection

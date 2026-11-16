@extends('layouts.app')

@section('title', 'Úprava firmy – '.$company->name)

@push('scripts')
<script>
    document.getElementById('ares-btn').addEventListener('click', async function () {
        const ico = document.getElementById('ico').value.trim();
        const status = document.getElementById('ares-status');
        if (!/^[0-9]{8}$/.test(ico)) {
            status.textContent = 'Zadejte 8 číslic IČO.';
            return;
        }
        status.textContent = 'Ověřuji v ARES…';
        try {
            const res = await fetch('{{ url('firmy/ares') }}/' + ico);
            const json = await res.json();
            if (!json.ok) {
                status.textContent = json.message;
                return;
            }
            const d = json.data;
            document.getElementById('name').value = d.name;
            document.getElementById('dic').value = d.dic ?? '';
            document.getElementById('street').value = d.street ?? '';
            document.getElementById('city').value = d.city ?? '';
            document.getElementById('zip').value = d.zip ?? '';
            document.getElementById('country').value = d.country ?? 'Česká republika';
            if (d.is_vat_payer !== null && d.is_vat_payer !== undefined) {
                document.getElementById('is_vat_payer').checked = d.is_vat_payer;
            }
            status.textContent = 'Údaje z ARES vyplněny.';
        } catch (e) {
            status.textContent = 'Ověření selhalo: ' + e.message;
        }
    });
</script>
@endpush

@section('content')
    <div class="card">
        <h1>Úprava firmy – {{ $company->name }}</h1>
        @include('companies._form', ['company' => $company])
    </div>
@endsection

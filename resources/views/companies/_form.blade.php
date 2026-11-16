<form method="POST" action="{{ $company->exists ? route('companies.update', $company) : route('companies.store') }}">
    @csrf
    @if ($company->exists)
        @method('PUT')
    @endif
    <div class="form-row">
        <div class="form-group">
            <label for="name">Název firmy *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $company->name) }}" required>
        </div>
        <div class="form-group">
            <label for="ico">IČO (8 číslic)</label>
            <div class="actions">
                <input type="text" name="ico" id="ico" value="{{ old('ico', $company->ico) }}" maxlength="8">
                <button type="button" class="btn btn-secondary" id="ares-btn">Ověřit v ARES</button>
            </div>
            <small id="ares-status" class="muted"></small>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label for="dic">DIČ</label>
            <input type="text" name="dic" id="dic" value="{{ old('dic', $company->dic) }}">
        </div>
        <div class="form-group checkbox">
            <label>
                <input type="checkbox" name="is_vat_payer" id="is_vat_payer" value="1"
                    @checked(old('is_vat_payer', $company->is_vat_payer))>
                Plátce DPH
            </label>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label for="street">Ulice a číslo</label>
            <input type="text" name="street" id="street" value="{{ old('street', $company->street) }}">
        </div>
        <div class="form-group">
            <label for="city">Město</label>
            <input type="text" name="city" id="city" value="{{ old('city', $company->city) }}">
        </div>
        <div class="form-group">
            <label for="zip">PSČ</label>
            <input type="text" name="zip" id="zip" value="{{ old('zip', $company->zip) }}" maxlength="10">
        </div>
    </div>
    <div class="form-group">
        <label for="country">Země</label>
        <input type="text" name="country" id="country" value="{{ old('country', $company->country) }}">
    </div>
    <div class="form-row">
        <div class="form-group">
            <label for="bank_account">Bankovní účet</label>
            <input type="text" name="bank_account" id="bank_account" value="{{ old('bank_account', $company->bank_account) }}" placeholder="např. 123456789/0800">
        </div>
        <div class="form-group">
            <label for="iban">IBAN</label>
            <input type="text" name="iban" id="iban" value="{{ old('iban', $company->iban) }}">
        </div>
    </div>
    <div class="form-group">
        <label for="payment_terms_days">Platební podmínky (dní) *</label>
        <input type="number" name="payment_terms_days" id="payment_terms_days" value="{{ old('payment_terms_days', $company->payment_terms_days ?? 14) }}" min="0" max="365" required>
    </div>
    <div class="form-group">
        <label for="note">Poznámka</label>
        <textarea name="note" id="note" rows="3">{{ old('note', $company->note) }}</textarea>
    </div>
    <div class="actions">
        <button type="submit" class="btn">Uložit</button>
        <a href="{{ route('companies.index') }}" class="btn btn-secondary">Zrušit</a>
    </div>
</form>

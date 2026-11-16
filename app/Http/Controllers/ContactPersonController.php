<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ContactPerson;
use Illuminate\Http\Request;

class ContactPersonController extends Controller
{
    public function store(Request $request, Company $company)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'note' => ['nullable', 'string'],
            'is_primary' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Jméno kontaktní osoby je povinné.',
            'email.email' => 'Zadejte platný e-mail.',
        ]);

        $data['is_primary'] = (bool) ($data['is_primary'] ?? false);

        if ($data['is_primary']) {
            $company->contactPersons()->update(['is_primary' => false]);
        }

        $company->contactPersons()->create($data);

        return redirect()->route('companies.show', $company)->with('success', 'Kontaktní osoba byla uložena.');
    }

    public function update(Request $request, Company $company, ContactPerson $contactPerson)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'note' => ['nullable', 'string'],
            'is_primary' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Jméno kontaktní osoby je povinné.',
            'email.email' => 'Zadejte platný e-mail.',
        ]);

        $data['is_primary'] = (bool) ($data['is_primary'] ?? false);

        if ($data['is_primary']) {
            $company->contactPersons()->whereKeyNot($contactPerson->getKey())->update(['is_primary' => false]);
        }

        $contactPerson->update($data);

        return redirect()->route('companies.show', $company)->with('success', 'Kontaktní osoba byla upravena.');
    }

    public function destroy(Company $company, ContactPerson $contactPerson)
    {
        $contactPerson->delete();

        return redirect()->route('companies.show', $company)->with('success', 'Kontaktní osoba byla smazána.');
    }
}

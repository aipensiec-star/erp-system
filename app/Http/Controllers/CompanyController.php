<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\AresService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::query()
            ->with('contactPersons')
            ->orderBy('name')
            ->paginate(20);

        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        $company = new Company(['payment_terms_days' => 14, 'country' => 'Česká republika']);

        return view('companies.create', compact('company'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Company::create($data);

        return redirect()->route('companies.index')->with('success', 'Firma byla uložena.');
    }

    public function show(Company $company)
    {
        $company->load('contactPersons');

        return view('companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $company->update($this->validated($request));

        return redirect()->route('companies.index')->with('success', 'Firma byla upravena.');
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('companies.index')->with('success', 'Firma byla smazána.');
    }

    public function aresLookup(AresService $ares, string $ico)
    {
        try {
            $data = $ares->lookupByIco($ico);

            if (! empty($data['dic'])) {
                try {
                    $data['is_vat_payer'] = $ares->isVatPayer($data['dic']);
                } catch (\Throwable) {
                    $data['is_vat_payer'] = null;
                }
            }

            return response()->json(['ok' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'ico' => ['nullable', 'digits:8'],
            'dic' => ['nullable', 'string', 'max:20'],
            'is_vat_payer' => ['nullable', 'boolean'],
            'street' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'zip' => ['nullable', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:60'],
            'bank_account' => ['nullable', 'string', 'max:30'],
            'bank_code' => ['nullable', 'string', 'max:10'],
            'iban' => ['nullable', 'string', 'max:40'],
            'payment_terms_days' => ['required', 'integer', 'min:0', 'max:365'],
            'note' => ['nullable', 'string'],
        ], $this->messages());
    }

    private function messages(): array
    {
        return [
            'name.required' => 'Název firmy je povinný.',
            'ico.digits' => 'IČO musí mít přesně 8 číslic.',
            'payment_terms_days.required' => 'Zadejte platební podmínky.',
            'payment_terms_days.integer' => 'Platební podmínky musí být celé číslo.',
        ];
    }
}

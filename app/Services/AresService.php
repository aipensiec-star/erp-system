<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AresService
{
    public function lookupByIco(string $ico): array
    {
        $ico = preg_replace('/\D/', '', $ico);

        if (! $this->isValidIco($ico)) {
            throw new \InvalidArgumentException('Neplatné IČO – musí mít 8 číslic a platný kontrolní součet.');
        }

        $response = Http::timeout(15)
            ->acceptJson()
            ->get("https://ares.gov.cz/ekonomicke-subjekty-v2/ekonomicke-subjekty/{$ico}");

        if ($response->failed()) {
            throw new \RuntimeException('ARES je nedostupný nebo subjekt nebyl nalezen (HTTP '.$response->status().').');
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new \RuntimeException('ARES vrátil neplatnou odpověď – zkuste to prosím znovu.');
        }

        if (empty($data['obchodniJmeno'])) {
            throw new \RuntimeException('Subjekt s tímto IČO nebyl v ARES nalezen.');
        }

        $sidlo = $data['sidlo'] ?? [];

        return [
            'name' => $data['obchodniJmeno'],
            'ico' => $ico,
            'dic' => $data['dic'] ?? null,
            'street' => trim(($sidlo['nazevUlice'] ?? '').' '.($sidlo['cisloDomovni'] ?? '').(! empty($sidlo['cisloOrientacni']) ? '/'.$sidlo['cisloOrientacni'] : '')) ?: null,
            'city' => $sidlo['nazevObce'] ?? null,
            'zip' => isset($sidlo['psc']) ? str_pad((string) $sidlo['psc'], 5, '0', STR_PAD_LEFT) : null,
            'country' => $sidlo['nazevStatu'] ?? 'Česká republika',
        ];
    }

    public function isVatPayer(string $dic): bool
    {
        $vatNumber = strtoupper(preg_replace('/^CZ/i', '', trim($dic)));

        if (! preg_match('/^\d{8,10}$/', $vatNumber)) {
            throw new \InvalidArgumentException('Neplatný formát DIČ (očekává se CZ + 8–10 číslic).');
        }

        $response = Http::timeout(15)
            ->get("https://ec.europa.eu/taxation_customs/vies/rest-api/ms/CZ/vat/{$vatNumber}");

        if ($response->failed()) {
            throw new \RuntimeException('Registr plátců DPH (VIES) je nedostupný.');
        }

        return (bool) ($response->json('valid') ?? false);
    }

    public function isValidIco(string $ico): bool
    {
        if (! preg_match('/^\d{8}$/', $ico)) {
            return false;
        }

        $digits = array_map('intval', str_split($ico));
        $weights = [8, 7, 6, 5, 4, 3, 2];
        $sum = 0;

        for ($i = 0; $i < 7; $i++) {
            $sum += $digits[$i] * $weights[$i];
        }

        $checksum = (10 - ($sum % 10)) % 10;

        return $checksum === $digits[7];
    }
}

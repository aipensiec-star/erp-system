<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'ico',
        'dic',
        'is_vat_payer',
        'street',
        'city',
        'zip',
        'country',
        'bank_account',
        'bank_code',
        'iban',
        'payment_terms_days',
        'note',
    ];

    protected $casts = [
        'is_vat_payer' => 'boolean',
        'payment_terms_days' => 'integer',
    ];

    public function contactPersons(): HasMany
    {
        return $this->hasMany(ContactPerson::class);
    }

    public function primaryContact(): ?ContactPerson
    {
        return $this->contactPersons()->orderByDesc('is_primary')->first();
    }
}

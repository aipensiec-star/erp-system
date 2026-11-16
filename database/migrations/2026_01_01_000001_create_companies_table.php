<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ico', 15)->nullable()->index();
            $table->string('dic', 20)->nullable();
            $table->boolean('is_vat_payer')->default(false);
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('zip', 10)->nullable();
            $table->string('country', 60)->default('Česká republika');
            $table->string('bank_account')->nullable();
            $table->string('bank_code', 10)->nullable();
            $table->string('iban', 40)->nullable();
            $table->unsignedSmallInteger('payment_terms_days')->default(14);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

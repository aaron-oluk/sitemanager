<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('base_currency', 3)->default('USD');
            $table->string('target_currency', 3);
            $table->decimal('rate', 15, 8);
            $table->timestamp('last_updated');
            $table->string('source')->nullable(); // API source
            $table->text('raw_data')->nullable(); // Store raw API response
            $table->timestamps();
            
            $table->unique(['base_currency', 'target_currency']);
            $table->index(['target_currency', 'last_updated']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
    }
};

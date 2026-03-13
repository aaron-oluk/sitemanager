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
        Schema::table('websites', function (Blueprint $table) {
            $table->foreignId('domain_id')->nullable()->after('domain')->constrained()->onDelete('set null');
            $table->string('currency', 3)->default('USD')->after('amount_paid');
            $table->boolean('domain_purchased')->default(false)->after('domain_id');
            $table->decimal('domain_base_cost', 10, 2)->default(0)->after('domain_purchased');
            $table->decimal('domain_tax_amount', 10, 2)->default(0)->after('domain_base_cost');
            $table->decimal('domain_transaction_fee', 10, 2)->default(0)->after('domain_tax_amount');
            $table->decimal('domain_total_cost', 10, 2)->default(0)->after('domain_transaction_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->string('item_type');          // hosting | domain | email
            $table->string('label');              // human-readable description
            $table->decimal('unit_cost', 10, 2); // base cost before tax/fees
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('transaction_fee', 10, 2);
            $table->decimal('total_amount', 10, 2); // unit_cost + tax + fee
            $table->string('currency', 10)->default('USD');
            $table->unsignedBigInteger('reference_id')->nullable(); // domain/email/website id
            $table->timestamps();
        });

        // Drop the JSON blob now that we have a proper table
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('line_items');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_line_items');

        Schema::table('payments', function (Blueprint $table) {
            $table->json('line_items')->nullable()->after('notes');
        });
    }
};

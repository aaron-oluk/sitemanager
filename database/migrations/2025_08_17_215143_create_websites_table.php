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
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('domain');
            $table->unsignedBigInteger('domain_id')->nullable();
            $table->string('host_server');
            $table->date('deployment_date');
            $table->decimal('amount_paid', 10, 2);
            $table->boolean('amount_includes_domain')->default(false);
            $table->string('currency', 3)->default('USD');
            $table->boolean('domain_purchased')->default(false);
            $table->decimal('domain_base_cost', 10, 2)->default(0);
            $table->decimal('domain_tax_amount', 10, 2)->default(0);
            $table->decimal('domain_transaction_fee', 10, 2)->default(0);
            $table->decimal('domain_total_cost', 10, 2)->default(0);
            $table->string('status')->default('active');
            $table->text('description')->nullable();
            $table->string('client_name')->nullable();
            $table->string('client_email')->nullable();
            $table->timestamps();
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

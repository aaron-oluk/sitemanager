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
        Schema::table('domains', function (Blueprint $table) {
            $table->string('hosting_plan')->default('monthly')->after('annual_cost');
            $table->decimal('monthly_cost', 10, 2)->nullable()->after('hosting_plan');
            $table->decimal('quarterly_cost', 10, 2)->nullable()->after('monthly_cost');
            $table->decimal('yearly_cost', 10, 2)->nullable()->after('quarterly_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn(['hosting_plan', 'monthly_cost', 'quarterly_cost', 'yearly_cost']);
        });
    }
};

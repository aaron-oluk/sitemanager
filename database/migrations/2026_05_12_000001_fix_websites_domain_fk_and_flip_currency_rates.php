<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add proper FK constraint for websites.domain_id
        Schema::table('websites', function (Blueprint $table) {
            $table->foreign('domain_id')->references('id')->on('domains')->onDelete('set null');
        });

        // Flip stored currency rates from target→USD to USD→target (conventional direction).
        // Previous code stored 1/api_rate; new code stores the api_rate directly.
        if (Schema::hasTable('currency_rates')) {
            DB::table('currency_rates')->where('rate', '>', 0)->update([
                'rate' => DB::raw('1 / rate'),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropForeign(['domain_id']);
        });

        if (Schema::hasTable('currency_rates')) {
            DB::table('currency_rates')->where('rate', '>', 0)->update([
                'rate' => DB::raw('1 / rate'),
            ]);
        }
    }
};

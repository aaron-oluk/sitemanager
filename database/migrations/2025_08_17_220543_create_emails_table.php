<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->string('email_address')->unique();
            $table->string('provider');
            $table->string('hosting_plan')->nullable();
            $table->decimal('monthly_cost', 8, 2)->default(0);
            $table->date('start_date');
            $table->date('renewal_date');
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('website_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('domain_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};

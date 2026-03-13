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
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->string('email_address')->unique();
            $table->string('provider'); // Gmail, Outlook, custom hosting, etc.
            $table->string('hosting_plan')->nullable(); // Basic, Business, Enterprise
            $table->decimal('monthly_cost', 8, 2)->default(0);
            $table->date('start_date');
            $table->date('renewal_date');
            $table->string('status')->default('active'); // active, suspended, cancelled
            $table->text('notes')->nullable();
            $table->string('associated_website')->nullable(); // Link to website if applicable
            $table->foreignId('domain_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};

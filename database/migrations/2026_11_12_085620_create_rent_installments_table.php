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
        Schema::create('rent_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained()->cascadeOnDelete();
            $table->date('period_start');
            $table->date('due_date')->index();
            $table->decimal('amount_due', 12, 2);
            $table->timestamp('pre_reminded_at')->nullable();
            $table->timestamp('overdue_reminded_at')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();
            $table->unique(['lease_id', 'period_start']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_installments');
    }
};

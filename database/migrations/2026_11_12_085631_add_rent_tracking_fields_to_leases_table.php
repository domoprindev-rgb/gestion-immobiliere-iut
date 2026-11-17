<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->unsignedTinyInteger('due_day')->default(5);
            $table->date('rent_tracking_from')->nullable();
        });

        DB::table('leases')
            ->where('status', 'actif')
            ->update(['rent_tracking_from' => now()->startOfMonth()->toDateString()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->dropColumn(['due_day', 'rent_tracking_from']);
        });
    }
};

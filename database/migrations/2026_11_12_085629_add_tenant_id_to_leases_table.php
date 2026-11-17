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
            $table->foreignId('tenant_id')->nullable()->after('property_id')->constrained()->restrictOnDelete();
        });

        DB::table('leases')->orderBy('id')->each(function (object $lease): void {
            $tenantId = null;

            if ($lease->tenant_email !== null && trim($lease->tenant_email) !== '') {
                $tenantId = DB::table('tenants')
                    ->whereRaw('LOWER(email) = ?', [mb_strtolower(trim($lease->tenant_email))])
                    ->value('id');
            }

            if ($tenantId === null) {
                $tenantId = DB::table('tenants')->insertGetId([
                    'name' => $lease->tenant_name ?: 'Locataire sans nom',
                    'email' => $lease->tenant_email,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('leases')->where('id', $lease->id)->update(['tenant_id' => $tenantId]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
        });
    }
};

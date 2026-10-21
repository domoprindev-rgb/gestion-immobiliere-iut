<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (! Schema::hasColumn('properties', 'title')) {
                $table->string('title')->nullable();
            }

            if (! Schema::hasColumn('properties', 'address')) {
                $table->string('address')->nullable();
            }

            if (! Schema::hasColumn('properties', 'price')) {
                $table->decimal('price', 12, 2)->nullable();
            }

            if (! Schema::hasColumn('properties', 'type')) {
                $table->string('type')->nullable();
            }

            if (! Schema::hasColumn('properties', 'status')) {
                $table->string('status')->default('disponible');
                $table->index('status');
            }
        });

        Schema::table('leases', function (Blueprint $table) {
            if (! Schema::hasColumn('leases', 'property_id')) {
                $table->foreignId('property_id')->nullable()->constrained()->cascadeOnDelete();
            }

            if (! Schema::hasColumn('leases', 'tenant_name')) {
                $table->string('tenant_name')->nullable();
            }

            if (! Schema::hasColumn('leases', 'tenant_email')) {
                $table->string('tenant_email')->nullable();
            }

            if (! Schema::hasColumn('leases', 'start_date')) {
                $table->date('start_date')->nullable();
            }

            if (! Schema::hasColumn('leases', 'end_date')) {
                $table->date('end_date')->nullable();
            }

            if (! Schema::hasColumn('leases', 'rent_amount')) {
                $table->decimal('rent_amount', 12, 2)->nullable();
            }

            if (! Schema::hasColumn('leases', 'status')) {
                $table->string('status')->default('actif');
                $table->index('status');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'lease_id')) {
                $table->foreignId('lease_id')->nullable()->constrained()->cascadeOnDelete();
            }

            if (! Schema::hasColumn('payments', 'amount')) {
                $table->decimal('amount', 12, 2)->nullable();
            }

            if (! Schema::hasColumn('payments', 'payment_date')) {
                $table->date('payment_date')->nullable();
                $table->index('payment_date');
            }

            if (! Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }

            if (! Schema::hasColumn('payments', 'receipt_number')) {
                $table->string('receipt_number')->nullable();
                $table->unique('receipt_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['receipt_number']);
            $table->dropIndex(['payment_date']);
            $table->dropConstrainedForeignId('lease_id');
            $table->dropColumn(['amount', 'payment_date', 'payment_method', 'receipt_number']);
        });

        Schema::table('leases', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropConstrainedForeignId('property_id');
            $table->dropColumn(['tenant_name', 'tenant_email', 'start_date', 'end_date', 'rent_amount', 'status']);
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['title', 'address', 'price', 'type', 'status']);
        });
    }
};

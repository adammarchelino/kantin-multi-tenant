<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('commission_schemes', 'active_tenant_lock')) {
            Schema::table('commission_schemes', function (Blueprint $table): void {
                $table->unsignedBigInteger('active_tenant_lock')->nullable()->storedAs('CASE WHEN valid_to IS NULL THEN tenant_id ELSE NULL END');
            });
        }
        if (! Schema::hasIndex('commission_schemes', 'uq_commission_active_per_tenant')) {
            Schema::table('commission_schemes', fn (Blueprint $table) => $table->unique('active_tenant_lock', 'uq_commission_active_per_tenant'));
        }

        if (! Schema::hasColumn('tenant_bank_accounts', 'primary_tenant_lock')) {
            Schema::table('tenant_bank_accounts', function (Blueprint $table): void {
                $table->unsignedBigInteger('primary_tenant_lock')->nullable()->storedAs('CASE WHEN is_primary = 1 THEN tenant_id ELSE NULL END');
            });
        }
        if (! Schema::hasIndex('tenant_bank_accounts', 'uq_primary_bank_per_tenant')) {
            Schema::table('tenant_bank_accounts', fn (Blueprint $table) => $table->unique('primary_tenant_lock', 'uq_primary_bank_per_tenant'));
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::unprepared("CREATE TRIGGER audit_logs_immutable_update BEFORE UPDATE ON audit_logs BEGIN SELECT RAISE(ABORT, 'audit logs are append-only'); END");
            DB::unprepared("CREATE TRIGGER audit_logs_immutable_delete BEFORE DELETE ON audit_logs BEGIN SELECT RAISE(ABORT, 'audit logs are append-only'); END");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::unprepared('DROP TRIGGER IF EXISTS audit_logs_immutable_update');
            DB::unprepared('DROP TRIGGER IF EXISTS audit_logs_immutable_delete');
        }
        Schema::table('tenant_bank_accounts', function (Blueprint $table): void {
            $table->dropUnique('uq_primary_bank_per_tenant');
            $table->dropColumn('primary_tenant_lock');
        });

        Schema::table('commission_schemes', function (Blueprint $table): void {
            $table->dropUnique('uq_commission_active_per_tenant');
            $table->dropColumn('active_tenant_lock');
        });
    }
};

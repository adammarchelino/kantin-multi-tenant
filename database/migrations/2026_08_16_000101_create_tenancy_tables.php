<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('canteens', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('slug', 100)->unique();
            $table->string('name', 120);
            $table->decimal('tax_rate', 6, 4)->default(0);
            $table->decimal('service_fee_rate', 6, 4)->default(0);
            $table->string('status', 20)->default('active');
            $table->timestamps(6);
        });

        Schema::create('tenants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('canteen_id')->constrained()->restrictOnDelete();
            $table->string('code', 30);
            $table->string('slug', 100);
            $table->string('display_name', 120);
            $table->string('status', 20)->default('pending');
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->unique(['canteen_id', 'code']);
            $table->unique(['canteen_id', 'slug']);
            $table->unique(['id', 'canteen_id']);
        });

        Schema::create('tenant_balances', function (Blueprint $table): void {
            $table->foreignId('tenant_id')->primary()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('available_amount')->default(0);
            $table->unsignedBigInteger('held_amount')->default(0);
            $table->timestamps(6);
        });

        $this->addBalanceChecks();

        Schema::create('tenant_bank_accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('account_number_cipher');
            $table->string('account_last4', 4);
            $table->string('bank_code', 20);
            $table->string('account_holder', 120);
            $table->string('status', 20)->default('unverified');
            $table->boolean('is_primary')->default(false);
            $table->timestamps(6);

            $table->unique(['tenant_id', 'id']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->string('phone_e164', 20)->nullable()->unique()->after('email');
        });

        Schema::create('user_canteen_roles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('canteen_id')->constrained()->cascadeOnDelete();
            $table->string('role', 30);
            $table->timestamps(6);

            $table->unique(['user_id', 'canteen_id', 'role']);
        });

        Schema::create('user_tenant_roles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('role', 30);
            $table->timestamps(6);

            $table->unique(['user_id', 'tenant_id', 'role']);
            $table->index(['tenant_id', 'user_id']);
        });
    }

    public function down(): void
    {
        $this->dropBalanceChecks();

        Schema::dropIfExists('user_tenant_roles');
        Schema::dropIfExists('user_canteen_roles');
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['phone_e164']);
            $table->dropColumn('phone_e164');
        });
        Schema::dropIfExists('tenant_bank_accounts');
        Schema::dropIfExists('tenant_balances');
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('canteens');
    }

    private function addBalanceChecks(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE tenant_balances ADD CONSTRAINT chk_tenant_balance_nonneg CHECK (available_amount >= 0 AND held_amount >= 0)');
        }

        if ($driver === 'sqlite') {
            DB::unprepared("CREATE TRIGGER chk_tenant_balance_nonneg_insert BEFORE INSERT ON tenant_balances WHEN NEW.available_amount < 0 OR NEW.held_amount < 0 BEGIN SELECT RAISE(ABORT, 'tenant balance cannot be negative'); END");
            DB::unprepared("CREATE TRIGGER chk_tenant_balance_nonneg_update BEFORE UPDATE OF available_amount, held_amount ON tenant_balances WHEN NEW.available_amount < 0 OR NEW.held_amount < 0 BEGIN SELECT RAISE(ABORT, 'tenant balance cannot be negative'); END");
        }
    }

    private function dropBalanceChecks(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::unprepared('DROP TRIGGER IF EXISTS chk_tenant_balance_nonneg_insert');
            DB::unprepared('DROP TRIGGER IF EXISTS chk_tenant_balance_nonneg_update');
        }
    }
};

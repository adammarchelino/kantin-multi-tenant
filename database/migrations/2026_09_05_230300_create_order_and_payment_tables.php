<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Orders Induk (Tanpa tenant_id)
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('canteen_id')->constrained()->restrictOnDelete();
            $table->foreignId('table_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number', 50)->unique();
            $table->unsignedBigInteger('total_amount'); // Rupiah BIGINT
            $table->enum('status', ['pending', 'paid', 'cancelled', 'completed'])->default('pending');
            $table->timestamps(6);
        });

        // 2. Tabel Tenant Orders (Disertai tenant_id dan snapshot komisi)
        Schema::create('tenant_orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('subtotal_amount');
            $table->decimal('commission_rate', 5, 2); // Snapshot komisi saat order dibuat
            $table->unsignedBigInteger('commission_amount');
            $table->enum('status', ['received', 'processing', 'ready', 'completed', 'cancelled'])->default('received');
            $table->timestamps(6);

            $table->unique(['tenant_id', 'id']); // Untuk Composite FK ke order items
        });

        // 3. Tabel Order Items
        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('menu_id');
            $table->string('menu_name_snapshot', 120); // Snapshot nama menu
            $table->unsignedBigInteger('price_snapshot'); // Snapshot harga menu
            $table->integer('quantity');
            $table->unsignedBigInteger('subtotal');
            $table->timestamps(6);

            // Composite Foreign Key ke Menus
            $table->foreign(['tenant_id', 'menu_id'])
                ->references(['tenant_id', 'id'])
                ->on('menus')
                ->restrictOnDelete();
        });

        // 4. Tabel Payments (Dengan Idempotency Key)
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->string('payment_gateway', 50);
            $table->string('transaction_id', 100)->nullable();
            $table->string('idempotency_key', 100)->unique(); // Mencegah double charge
            $table->unsignedBigInteger('amount');
            $table->enum('status', ['pending', 'success', 'failed', 'expired'])->default('pending');
            $table->timestamps(6);
        });

        // 5. Tabel Ledgers (Append-Only untuk pembukuan keuangan)
        Schema::create('ledgers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->enum('type', ['credit', 'debit']);
            $table->unsignedBigInteger('amount');
            $table->string('description', 255);
            $table->timestamps(6);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledgers');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('tenant_orders');
        Schema::dropIfExists('orders');
    }
};

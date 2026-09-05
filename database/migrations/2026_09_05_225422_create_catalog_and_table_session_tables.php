<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Tables (Meja)
        Schema::create('tables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('canteen_id')->constrained()->restrictOnDelete();
            $table->string('number', 20);
            $table->binary('token_hash'); // Hash token meja BINARY(32)
            $table->timestamps(6);

            $table->unique(['canteen_id', 'number']);
        });

        // 2. Tabel Categories (Kategori Menu)
        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->string('name', 100);
            $table->timestamps(6);

            $table->unique(['tenant_id', 'id']);
        });

        // 3. Tabel Menus (Katalog Menu)
        Schema::create('menus', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 120);
            $table->unsignedBigInteger('price_amount');
            $table->boolean('is_available')->default(true);
            $table->timestamps(6);

            // Indeks Komposit Unik (Wajib untuk Composite Foreign Key)
            $table->unique(['tenant_id', 'id']);
        });

        // 4. Tabel Modifiers (Toping / Opsi Tambahan)
        Schema::create('modifiers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('menu_id');
            $table->string('name', 100);
            $table->unsignedBigInteger('price_amount')->default(0);
            $table->timestamps(6);

            // Composite Foreign Key (Mencegah modifier menunjuk menu milik tenant lain)
            $table->foreign(['tenant_id', 'menu_id'])
                ->references(['tenant_id', 'id'])
                ->on('menus')
                ->restrictOnDelete();
        });

        // 5. Tabel Commissions (Tarif Komisi Effective-Dated)
        Schema::create('commissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->decimal('rate_percentage', 5, 2);
            $table->timestamp('effective_from');
            $table->timestamp('effective_until')->nullable();
            $table->timestamps(6);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('modifiers');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('tables');
    }
};

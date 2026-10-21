<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dining_tables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('canteen_id')->constrained()->restrictOnDelete();
            $table->string('code', 30);
            $table->string('label', 60);
            $table->string('zone', 60)->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps(6);

            $table->unique(['canteen_id', 'code']);
            $table->unique(['id', 'canteen_id']);
        });

        Schema::create('table_qr_tokens', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('dining_table_id')->constrained()->cascadeOnDelete();
            $table->binary('token_hash', 32, fixed: true);
            $table->string('status', 20)->default('active');
            $table->timestampTz('issued_at', 6)->nullable();
            $table->timestampTz('expires_at', 6)->nullable();
            $table->timestampTz('revoked_at', 6)->nullable();
            $table->timestamps(6);

            $table->unique('token_hash');
            $table->index(['dining_table_id', 'status']);
        });

        Schema::create('customer_sessions', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignId('canteen_id')->constrained()->restrictOnDelete();
            $table->foreignId('dining_table_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('qr_token_id')->nullable()->constrained('table_qr_tokens')->nullOnDelete();
            $table->binary('session_token_hash', 32, fixed: true);
            $table->string('status', 20)->default('active');
            $table->timestampTz('expires_at', 6)->nullable();
            $table->timestamps(6);

            $table->unique('session_token_hash');
            $table->index(['canteen_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_sessions');
        Schema::dropIfExists('table_qr_tokens');
        Schema::dropIfExists('dining_tables');
    }
};

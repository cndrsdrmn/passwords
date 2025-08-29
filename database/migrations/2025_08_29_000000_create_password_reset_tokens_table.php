<?php

declare(strict_types=1);

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
        Schema::create($this->table(), function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->boolean('is_verified')->default(false);
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->table());
    }

    /**
     * Get the table name.
     */
    private function table(): string
    {
        $provider = config()->string('auth.defaults.passwords', 'users');

        return config()->string("auth.passwords.{$provider}.table", 'password_reset_tokens');
    }
};

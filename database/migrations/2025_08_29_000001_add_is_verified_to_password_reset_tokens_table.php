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
        if (Schema::hasTable($this->table()) && ! Schema::hasColumn($this->table(), 'is_verified')) {
            Schema::table($this->table(), function (Blueprint $table): void {
                $table->boolean('is_verified')->default(false)->after('token');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable($this->table()) && Schema::hasColumn($this->table(), 'is_verified')) {
            Schema::table($this->table(), function (Blueprint $table): void {
                $table->dropColumn('is_verified');
            });
        }
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

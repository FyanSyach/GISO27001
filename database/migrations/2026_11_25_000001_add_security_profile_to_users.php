<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('username')->nullable()->unique();
            $table->string('division')->nullable();
            $table->string('job_title')->nullable();
            $table->string('avatar')->nullable();
            $table->string('role')->default('user')->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('points')->default(0);
            $table->unsignedTinyInteger('level')->default(1);
            $table->unsignedInteger('current_streak')->default(0);
            $table->date('last_active_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['username', 'division', 'job_title', 'avatar', 'role', 'is_active', 'points', 'level', 'current_streak', 'last_active_date']));
    }
};

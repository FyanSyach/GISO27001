<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Skema tabel user_progress; rollback menghapus data tabel ini. */
return new class extends Migration
{
    /** Membuat tabel beserta constraint integritasnya. */
    public function up(): void
    {
        Schema::create('user_progress', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_completed')->default(false);
            $table->boolean('points_awarded')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->unique(['user_id', 'module_id']);
            $table->timestamps();
        });
    }

    /** Menghapus tabel saat rollback; gunakan forward fix untuk produksi. */
    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};

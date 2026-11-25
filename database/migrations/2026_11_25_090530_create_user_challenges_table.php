<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Skema tabel user_challenges; rollback menghapus data tabel ini. */
return new class extends Migration
{
    /** Membuat tabel beserta constraint integritasnya. */
    public function up(): void
    {
        Schema::create('user_challenges', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('progress_count')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->unique(['user_id', 'challenge_id']);
            $table->timestamps();
        });
    }

    /** Menghapus tabel saat rollback; gunakan forward fix untuk produksi. */
    public function down(): void
    {
        Schema::dropIfExists('user_challenges');
    }
};

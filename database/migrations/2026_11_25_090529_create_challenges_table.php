<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Skema tabel challenges; rollback menghapus data tabel ini. */
return new class extends Migration
{
    /** Membuat tabel beserta constraint integritasnya. */
    public function up(): void
    {
        Schema::create('challenges', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('frequency', ['daily', 'weekly']);
            $table->string('target_type');
            $table->unsignedInteger('target_count');
            $table->unsignedInteger('points_reward');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamps();
        });
    }

    /** Menghapus tabel saat rollback; gunakan forward fix untuk produksi. */
    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};

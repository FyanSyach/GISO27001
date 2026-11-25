<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Skema tabel modules; rollback menghapus data tabel ini. */
return new class extends Migration
{
    /** Membuat tabel beserta constraint integritasnya. */
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('iso_control_ref')->nullable();
            $table->unsignedInteger('order_level')->unique();
            $table->unsignedTinyInteger('passing_score')->default(70);
            $table->unsignedInteger('points_reward')->default(50);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /** Menghapus tabel saat rollback; gunakan forward fix untuk produksi. */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Skema tabel module_contents; rollback menghapus data tabel ini. */
return new class extends Migration
{
    /** Membuat tabel beserta constraint integritasnya. */
    public function up(): void
    {
        Schema::create('module_contents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('content_type', ['video', 'infographic', 'text', 'case_study']);
            $table->text('body')->nullable();
            $table->string('media_url')->nullable();
            $table->string('subtitle_url')->nullable();
            $table->unsignedInteger('duration_minutes');
            $table->unsignedInteger('sort_order');
            $table->timestamps();
        });
    }

    /** Menghapus tabel saat rollback; gunakan forward fix untuk produksi. */
    public function down(): void
    {
        Schema::dropIfExists('module_contents');
    }
};

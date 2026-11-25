<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Skema tabel certificates; rollback menghapus data tabel ini. */
return new class extends Migration
{
    /** Membuat tabel beserta constraint integritasnya. */
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_number')->unique();
            $table->string('file_path');
            $table->timestamp('issued_at');
            $table->timestamps();
        });
    }

    /** Menghapus tabel saat rollback; gunakan forward fix untuk produksi. */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};

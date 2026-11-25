<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Menandai modul terbit yang menjadi syarat sertifikat; default menjaga perilaku katalog awal. */
return new class extends Migration
{
    /** Menambahkan flag wajib berindeks untuk seleksi kelayakan sertifikat. */
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table): void {
            $table->boolean('is_required')->default(true)->after('is_published')->index();
        });
    }

    /** Menghapus flag; rollback akan kembali memperlakukan modul terbit sebagai wajib pada kode lama. */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table): void {
            $table->dropIndex(['is_required']);
            $table->dropColumn('is_required');
        });
    }
};

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Entitas Certificate yang memetakan tabel certificates. */
class Certificate extends Model
{
    use HasFactory;

    protected $table = 'certificates';

    protected $fillable = [
        0 => 'user_id',
        1 => 'certificate_number',
        2 => 'file_path',
        3 => 'issued_at',
    ];

    /** Mengonversi atribut database ke tipe domain. */
    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    /** Relasi user untuk entitas Certificate. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

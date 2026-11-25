<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Entitas Badge yang memetakan tabel badges. */
class Badge extends Model
{
    use HasFactory;

    protected $table = 'badges';

    protected $fillable = [
        0 => 'code',
        1 => 'name',
        2 => 'description',
        3 => 'icon_path',
        4 => 'category',
        5 => 'min_points',
        6 => 'required_module_id',
    ];

    /** Mengonversi atribut database ke tipe domain. */
    protected function casts(): array
    {
        return [
            'category' => '\\App\\Enums\\BadgeCategory',
        ];
    }

    /** Relasi requiredModule untuk entitas Badge. */
    public function requiredModule(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'required_module_id');
    }
}

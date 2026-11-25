<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Entitas ModuleContent yang memetakan tabel module_contents. */
class ModuleContent extends Model
{
    use HasFactory;

    protected $table = 'module_contents';

    protected $fillable = [
        0 => 'module_id',
        1 => 'title',
        2 => 'content_type',
        3 => 'body',
        4 => 'media_url',
        5 => 'subtitle_url',
        6 => 'duration_minutes',
        7 => 'sort_order',
    ];

    /** Mengonversi atribut database ke tipe domain. */
    protected function casts(): array
    {
        return [
            'content_type' => '\\App\\Enums\\ContentType',
            'duration_minutes' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /** Relasi module untuk entitas ModuleContent. */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}

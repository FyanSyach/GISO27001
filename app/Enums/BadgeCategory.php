<?php

namespace App\Enums;

/** Kategori kriteria badge; PRD belum menetapkan nilai enum secara eksplisit. */
enum BadgeCategory: string
{
    case POINTS = 'points';
    case MODULE = 'module';
    case ACHIEVEMENT = 'achievement';
}

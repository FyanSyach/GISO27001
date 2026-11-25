<?php

namespace App\Enums;

/** Jenis konten mikro yang didukung platform. */
enum ContentType: string
{
    case VIDEO = 'video';
    case INFOGRAPHIC = 'infographic';
    case TEXT = 'text';
    case CASE_STUDY = 'case_study';
}

<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Test Pest menggunakan lifecycle Laravel yang sama dengan test PHPUnit terdahulu.
pest()->extend(TestCase::class)->use(RefreshDatabase::class)->in('Feature/Pest');

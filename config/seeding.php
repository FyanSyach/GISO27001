<?php

return [
    // Password wajib disediakan operator; tidak ada password admin bawaan.
    'admin_email' => env('SEED_ADMIN_EMAIL', 'admin@giso27001.test'),
    'admin_password' => env('SEED_ADMIN_PASSWORD'),
    'demo_password' => env('SEED_DEMO_PASSWORD'),
];

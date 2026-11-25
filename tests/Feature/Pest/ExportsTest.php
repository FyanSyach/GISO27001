<?php

use App\Models\User;

/** Memastikan ekspor menghasilkan format asli tanpa memperlakukan nama sebagai formula. */
it('exports a real xlsx with safe text cells', function (): void {
    $admin = User::factory()->create(['role' => 'admin', 'name' => '=1+1']);
    $response = $this->actingAs($admin)->get(route('admin.reports.excel'))->assertOk();
    $bytes = $response->streamedContent();
    expect(substr($bytes, 0, 2))->toBe('PK');
    $file = tempnam(sys_get_temp_dir(), 'giso-xlsx');
    try {
        file_put_contents($file, $bytes);
        $sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        expect($sheet->getActiveSheet()->getCell('A2')->getDataType())->toBe('s');
        expect($sheet->getActiveSheet()->getCell('A2')->getValue())->toBe('=1+1');
    } finally {
        unlink($file);
    }
});

it('exports an actual pdf for admins', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $response = $this->actingAs($admin)->get(route('admin.reports.pdf'))->assertOk();
    expect(substr($response->getContent(), 0, 5))->toBe('%PDF-');
});

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Ekspor laporan akun dan penyelesaian terbatas hanya untuk administrator. */
class ReportController extends Controller
{
    /** Menghasilkan XLSX asli dengan sel teks eksplisit untuk mencegah formula injection. */
    public function excel(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $sheet = new Spreadsheet;
            $sheet->getActiveSheet()->fromArray(['Nama', 'Email', 'Divisi', 'Poin', 'Modul selesai'], null, 'A1');
            $row = 2;
            foreach (User::withCount(['progress as completions' => fn ($query) => $query->where('is_completed', true)])->orderBy('id')->cursor() as $user) {
                foreach ([$user->name, $user->email, $user->division, $user->points, $user->completions] as $column => $value) {
                    $sheet->getActiveSheet()->setCellValueExplicit([$column + 1, $row], (string) $value, DataType::TYPE_STRING);
                }
                $row++;
            }
            (new Xlsx($sheet))->save('php://output');
            $sheet->disconnectWorksheets();
        }, 'giso-report.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    /** Menghasilkan PDF ringkasan maksimal 500 akun untuk membatasi penggunaan memori. */
    public function pdf(): Response
    {
        return Pdf::loadView('reports.users', ['users' => User::withCount(['progress as completions' => fn ($query) => $query->where('is_completed', true)])->orderBy('id')->limit(500)->get()])->download('giso-report.pdf');
    }
}

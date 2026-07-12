<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\KategoriPenilaian;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;

class TemplateExcelController extends Controller
{
    public function balitaTemplate(): Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Balita');

        $sheet->fromArray([
            ['Nama', 'NIK', 'Jenis Kelamin', 'Tanggal Lahir', 'Nama Orang Tua'],
            ['Contoh Nama Balita', '1234567890123456', 'Perempuan', '2024-01-15', 'Contoh Nama Orang Tua'],
        ], null, 'A1');

        $filename = 'template_balita.xlsx';

        $tempFile = tempnam(sys_get_temp_dir(), 'template-balita');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    public function penilaianTemplate(): Response
    {
        $kriterias = Kriteria::orderByRaw('CAST(SUBSTRING(kode_kriteria, 2) AS UNSIGNED)')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Penilaian');

        $headers = ['Nama Balita', 'Tanggal Penilaian'];
        foreach ($kriterias as $kriteria) {
            $headers[] = $kriteria->nama_kriteria;
        }

        $sheet->fromArray([$headers], null, 'A1');

        $row = ['Contoh Nama Balita', '2025-04-20'];
        foreach ($kriterias as $kriteria) {
            $kategori = KategoriPenilaian::where('kriteria_id', $kriteria->id)->orderBy('nilai')->first();
            $row[] = $kategori ? $kategori->nama_kategori : 'Contoh Nilai';
        }
        $sheet->fromArray([$row], null, 'A2');

        $filename = 'template_penilaian_balita.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'template-penilaian');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}

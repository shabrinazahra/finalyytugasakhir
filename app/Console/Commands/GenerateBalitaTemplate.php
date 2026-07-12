<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class GenerateBalitaTemplate extends Command
{
    protected $signature = 'template:generate-balita';
    protected $description = 'Generate template Excel balita dengan dropdown Jenis Kelamin ke storage/app/public/templates/excel/';

    public function handle(): int
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Balita');

        // Build headers
        $headers = ['Nama', 'NIK', 'Jenis Kelamin', 'Tanggal Lahir', 'Nama Orang Tua'];
        $sheet->fromArray([$headers], null, 'A1');

        // Build contoh baris data
        $row = ['Contoh Nama Balita', '1234567890123456', 'Perempuan', '2024-01-15', 'Contoh Nama Orang Tua'];
        $sheet->fromArray([$row], null, 'A2');

        // Tambahkan dropdown validation untuk Jenis Kelamin (Kolom C)
        $columnLetter = 'C';
        $options = ['Laki-laki', 'Perempuan'];
        $formulaList = '"' . implode(',', $options) . '"';

        // Terapkan dropdown dari baris 2 sampai baris 100
        for ($rowNum = 2; $rowNum <= 100; $rowNum++) {
            $cellCoordinate = $columnLetter . $rowNum;
            $validation = $sheet->getCell($cellCoordinate)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(true);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Input Error');
            $validation->setError('Jenis kelamin tidak valid. Harus Laki-laki atau Perempuan.');
            $validation->setPromptTitle('Jenis Kelamin');
            $validation->setPrompt('Pilih Laki-laki atau Perempuan.');
            $validation->setFormula1($formulaList);
        }

        // Auto-size kolom
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Simpan ke storage/app/public/templates/excel/
        $directory = storage_path('app/public/templates/excel');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fullPath = $directory . '/template_balita.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($fullPath);

        $this->info("Template Balita berhasil dibuat di: {$fullPath}");

        return 0;
    }
}

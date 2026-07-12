<?php

namespace Tests\Feature;

use App\Models\Balita;
use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class BalitaImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_kader_can_import_balita_from_excel(): void
    {
        $posyandu = Posyandu::factory()->create();
        $user = User::factory()->create([
            'posyandu_id' => $posyandu->id,
        ]);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['Nama', 'NIK', 'Jenis Kelamin', 'Tanggal Lahir', 'Nama Orang Tua'],
            ['Alya', '1234567890123456', 'Perempuan', '2024-01-15', 'Budi'],
        ], null, 'A1');

        $tmpFile = tempnam(sys_get_temp_dir(), 'balita-import');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tmpFile);

        $uploadedFile = new UploadedFile(
            $tmpFile,
            'balita.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $this->actingAs($user)
            ->post(route('balita.import'), [
                'file' => $uploadedFile,
            ])
            ->assertRedirect(route('balita.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('balitas', [
            'posyandu_id' => $posyandu->id,
            'nik' => '1234567890123456',
            'nama' => 'Alya',
        ]);

        unlink($tmpFile);
    }
}

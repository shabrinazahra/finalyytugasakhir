<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

class TemplateExcelController extends Controller
{
    public function balitaTemplate(): Response
    {
        $filePath = storage_path('app/public/templates/excel/template_balita.xlsx');

        if (!file_exists($filePath)) {
            try {
                \Illuminate\Support\Facades\Artisan::call('template:generate-balita');
            } catch (\Exception $e) {
                // Fallback to error if it fails
            }
        }

        if (!file_exists($filePath)) {
            abort(404, 'Template balita tidak ditemukan.');
        }

        return response()->download($filePath, 'template_balita.xlsx');
    }
}


<?php

namespace App\Services;

use Mpdf\Mpdf;

class PdfService
{
    /**
     * Create optimized Mpdf instance for faster PDF generation
     *
     * @param array $customParams
     * @return Mpdf
     */
    public static function createOptimizedPdf(array $customParams = []): Mpdf
    {
        $defaultParams = [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_header' => '3',
            'margin_top' => '30',
            'margin_bottom' => '20',
            'margin_footer' => '2',
            'margin_right' => 5,
            'margin_left' => 5,
            'tempDir' => storage_path('framework/cache'),

            // Performance optimizations
            'fontDir' => storage_path('fonts'), // Cache fonts
            'fontdata' => [], // Use only needed fonts
            'default_font' => 'dejavusanscondensed', // Faster than full DejaVu
            'simpleTables' => true, // Faster table rendering
            'packTableData' => true, // Compress table data
            'useSubstitutions' => false, // Disable font substitutions
            'autoScriptToLang' => false, // Disable auto script detection
            'autoLangToFont' => false, // Disable auto language font mapping
            'useKashida' => false, // Disable kashida justification
            'debug' => false,
        ];

        $params = array_merge($defaultParams, $customParams);

        return new Mpdf($params);
    }
}


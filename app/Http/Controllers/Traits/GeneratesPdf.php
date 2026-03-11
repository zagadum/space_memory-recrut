<?php

namespace App\Http\Controllers\Traits;

use App\Services\PdfService;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;

/**
 * Трейт для генерации PDF документов
 *
 * Предоставляет стандартизированные методы для создания PDF
 * с правильными настройками времени выполнения и памяти
 */
trait GeneratesPdf
{
    /**
     * Инициализация настроек для генерации PDF
     *
     * @param int $timeLimit Лимит времени выполнения в секундах (по умолчанию 300 = 5 минут)
     * @param string $memoryLimit Лимит памяти (по умолчанию 512M)
     * @return void
     */
    protected function initPdfGeneration(int $timeLimit = 300, string $memoryLimit = '512M'): void
    {
        // Логируем изменение лимитов
        Log::warning('PDF Generation: Modifying PHP execution limits', [
            'time_limit' => $timeLimit,
            'memory_limit' => $memoryLimit,
            'previous_time_limit' => ini_get('max_execution_time'),
            'previous_memory_limit' => ini_get('memory_limit'),
            'caller_class' => get_class($this) ?? 'unknown'
        ]);

        set_time_limit($timeLimit);
        ini_set("max_execution_time", (string)$timeLimit);
        ini_set("pcre.backtrack_limit", "5000000");
        ini_set("memory_limit", $memoryLimit);
    }

    /**
     * Создать оптимизированный PDF документ
     *
     * @param array $params Параметры для mPDF
     * @param int $timeLimit Лимит времени выполнения
     * @param string $memoryLimit Лимит памяти
     * @return Mpdf
     */
    protected function createOptimizedPdf(array $params = [], int $timeLimit = 300, string $memoryLimit = '512M'): Mpdf
    {
        $this->initPdfGeneration($timeLimit, $memoryLimit);

        // Проверяем, существует ли PdfService
        if (class_exists(PdfService::class) && method_exists(PdfService::class, 'createOptimizedPdf')) {
            Log::info('PDF Generation: Using PdfService', [
                'caller_class' => get_class($this) ?? 'unknown'
            ]);
            return PdfService::createOptimizedPdf($params);
        }

        // Fallback: создаем обычный Mpdf с оптимизированными параметрами
        Log::warning('PDF Generation: PdfService not available, using fallback Mpdf', [
            'caller_class' => get_class($this) ?? 'unknown',
            'params' => $params
        ]);

        $defaultParams = [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_header' => '3',
            'margin_top' => '30',
            'margin_bottom' => '20',
            'margin_footer' => '2',
            'margin_right' => 5,
            'margin_left' => 5,
            'tempDir' => storage_path('framework/cache')
        ];

        $mergedParams = array_merge($defaultParams, $params);

        return new Mpdf($mergedParams);
    }

    /**
     * Получить стандартные параметры для mPDF
     *
     * @param string $format Формат страницы (A4, A4-L и т.д.)
     * @return array
     */
    protected function getDefaultPdfParams(string $format = 'A4'): array
    {
        return [
            'mode' => 'utf-8',
            'format' => $format,
            'margin_header' => '3',
            'margin_top' => '30',
            'margin_bottom' => '20',
            'margin_footer' => '2',
            'margin_right' => 5,
            'margin_left' => 5,
            'tempDir' => storage_path('framework/cache')
        ];
    }

    /**
     * Вывести PDF в браузер
     *
     * @param Mpdf $pdf Объект PDF
     * @param string $filename Имя файла
     * @param string $mode Режим вывода (I - inline, D - download, S - string)
     * @return void
     */
    protected function outputPdf(Mpdf $pdf, string $filename, string $mode = 'I'): void
    {
        if ($mode === 'I') {
            header("Content-type:application/pdf");
            header("Content-Disposition:inline;filename={$filename}");
        }

        print $pdf->Output($filename, $mode);
    }
}


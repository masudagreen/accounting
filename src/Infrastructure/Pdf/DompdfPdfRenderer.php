<?php

declare(strict_types=1);

namespace App\Infrastructure\Pdf;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * dompdf を使った HTML → PDF 変換実装.
 */
final class DompdfPdfRenderer implements PdfRenderer
{
    public function render(string $html): string
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $options->set('defaultFont', 'serif');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        if ($output === null) {
            throw new \RuntimeException('dompdf failed to generate PDF output');
        }

        return $output;
    }
}

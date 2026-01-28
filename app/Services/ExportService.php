<?php
namespace App\Services;

use App\Core\Config;
use App\Models\BrandKit;
use App\Models\BrandAsset;
use App\Models\Color;
use App\Models\FontSelection;
use App\Models\TemplateAsset;

require_once __DIR__ . '/../../vendor/fpdf.php';

class ExportService
{
    public function pdf(int $kitId, array $plan, array $workspace): string
    {
        $kit = BrandKit::findById($kitId);
        $assets = BrandAsset::forKit($kitId);
        $colors = Color::forKit($kitId);
        $fonts = FontSelection::forKit($kitId);
        $templates = TemplateAsset::forKit($kitId);

        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $title = $workspace['white_label_name'] ?: ($workspace['name'] ?? 'StudioKit');
        $pdf->Cell(0, 10, $title . ' Brand Guide', 0, 1);

        if (!empty($plan['watermark_pdf'])) {
            $pdf->SetFont('Arial', 'B', 50);
            $pdf->SetTextColor(230, 230, 230);
            $pdf->Text(30, 150, 'StudioKit');
            $pdf->SetTextColor(0, 0, 0);
        }

        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 6, 'Kit: ' . ($kit['name'] ?? ''));
        $pdf->MultiCell(0, 6, 'Tagline: ' . ($kit['tagline'] ?? ''));
        $pdf->MultiCell(0, 6, 'Description: ' . ($kit['description'] ?? ''));
        $pdf->Ln(4);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'Colors', 0, 1);
        $pdf->SetFont('Arial', '', 11);
        foreach ($colors as $color) {
            $pdf->Cell(0, 6, $color['name'] . ' - ' . $color['hex'], 0, 1);
        }

        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'Fonts', 0, 1);
        if ($fonts) {
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 6, 'Heading: ' . $fonts['heading_font'], 0, 1);
            $pdf->Cell(0, 6, 'Body: ' . $fonts['body_font'], 0, 1);
        }

        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'Logos', 0, 1);
        $pdf->SetFont('Arial', '', 11);
        foreach ($assets as $asset) {
            $pdf->Cell(0, 6, $asset['original_name'] . ' (' . $asset['type'] . ')', 0, 1);
        }

        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'Templates', 0, 1);
        $pdf->SetFont('Arial', '', 11);
        foreach ($templates as $template) {
            $pdf->Cell(0, 6, $template['type'], 0, 1);
        }

        $path = $this->storagePath('exports/kit-' . $kitId . '-' . time() . '.pdf');
        $pdf->Output('F', $path);
        return $path;
    }

    public function zip(int $kitId): string
    {
        $kit = BrandKit::findById($kitId);
        $assets = BrandAsset::forKit($kitId);
        $colors = Color::forKit($kitId);
        $fonts = FontSelection::forKit($kitId);
        $templates = TemplateAsset::forKit($kitId);

        $path = $this->storagePath('exports/kit-' . $kitId . '-' . time() . '.zip');
        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE);
        $zip->addFromString('README.txt', "Brand kit export for {$kit['name']}\n");

        $colorsText = '';
        $colorsJson = [];
        foreach ($colors as $color) {
            $colorsText .= $color['name'] . ': ' . $color['hex'] . "\n";
            $colorsJson[] = ['name' => $color['name'], 'hex' => $color['hex']];
        }
        $zip->addFromString('colors.txt', $colorsText);
        $zip->addFromString('colors.json', json_encode($colorsJson, JSON_PRETTY_PRINT));

        if ($fonts) {
            $zip->addFromString('fonts.txt', "Heading: {$fonts['heading_font']}\nBody: {$fonts['body_font']}\n");
        }

        foreach ($assets as $asset) {
            $source = $this->storagePath($asset['path']);
            if (file_exists($source)) {
                $zip->addFile($source, 'logos/' . basename($asset['path']));
            }
        }

        foreach ($templates as $template) {
            $source = $this->storagePath($template['path']);
            if (file_exists($source)) {
                $zip->addFile($source, 'templates/' . basename($template['path']));
            }
        }

        $zip->close();
        return $path;
    }

    private function storagePath(string $relative): string
    {
        $base = rtrim(Config::get('storage.path', __DIR__ . '/../../storage'), '/');
        $path = $base . '/' . ltrim($relative, '/');
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        return $path;
    }
}

<?php
// Minimal FPDF subset for simple PDF exports.
class FPDF
{
    protected array $pages = [];
    protected int $page = 0;
    protected float $x = 10;
    protected float $y = 10;
    protected string $font = 'Arial';
    protected int $fontSize = 12;
    protected array $textColor = [0, 0, 0];

    public function AddPage(): void
    {
        $this->page++;
        $this->pages[$this->page] = '';
        $this->x = 10;
        $this->y = 10;
    }

    public function SetFont(string $family, string $style = '', int $size = 12): void
    {
        $this->font = $family;
        $this->fontSize = $size;
    }

    public function SetTextColor(int $r, int $g, int $b): void
    {
        $this->textColor = [$r, $g, $b];
    }

    public function Cell(float $w, float $h, string $txt, int $border = 0, int $ln = 0): void
    {
        $this->Text($this->x, $this->y, $txt);
        $this->y += $h;
        if ($ln > 0) {
            $this->x = 10;
        }
    }

    public function MultiCell(float $w, float $h, string $txt): void
    {
        $lines = explode("\n", wordwrap($txt, 80));
        foreach ($lines as $line) {
            $this->Text($this->x, $this->y, $line);
            $this->y += $h;
        }
    }

    public function Ln(float $h = 0): void
    {
        $this->y += $h;
    }

    public function Text(float $x, float $y, string $txt): void
    {
        $this->pages[$this->page] .= sprintf("BT /F1 %d Tf %d %d %d rg %.2f %.2f Td (%s) Tj ET\n", $this->fontSize, $this->textColor[0], $this->textColor[1], $this->textColor[2], $x, 842 - $y, $this->escape($txt));
    }

    public function Output(string $dest, string $name): void
    {
        $pdf = $this->buildPdf();
        if ($dest === 'F') {
            file_put_contents($name, $pdf);
            return;
        }
        header('Content-Type: application/pdf');
        echo $pdf;
    }

    protected function buildPdf(): string
    {
        $objects = [];
        $pages = '';
        $kids = '';
        $offsets = [0];
        $n = 1;

        $objects[$n++] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
        foreach ($this->pages as $content) {
            $contentObj = $n++;
            $objects[$contentObj] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "endstream";
            $pageObj = $n++;
            $objects[$pageObj] = "<< /Type /Page /Parent 1 0 R /Resources << /Font << /F1 1 0 R >> >> /Contents $contentObj 0 R /MediaBox [0 0 595 842] >>";
            $kids .= "$pageObj 0 R ";
        }
        $pagesObj = $n++;
        $objects[$pagesObj] = "<< /Type /Pages /Kids [$kids] /Count " . count($this->pages) . " >>";
        $catalogObj = $n++;
        $objects[$catalogObj] = "<< /Type /Catalog /Pages $pagesObj 0 R >>";

        $pdf = "%PDF-1.4\n";
        foreach ($objects as $i => $obj) {
            $offsets[$i] = strlen($pdf);
            $pdf .= "$i 0 obj\n$obj\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . ($n) . "\n0000000000 65535 f \n";
        for ($i = 1; $i < $n; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size $n /Root $catalogObj 0 R >>\nstartxref\n$xref\n%%EOF";
        return $pdf;
    }

    protected function escape(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}

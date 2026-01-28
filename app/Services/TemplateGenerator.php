<?php
namespace App\Services;

use App\Core\Config;

class TemplateGenerator
{
    public function socialProfile(string $text, string $hex, string $filename): string
    {
        $img = imagecreatetruecolor(800, 800);
        $bg = $this->allocateColor($img, $hex);
        imagefill($img, 0, 0, $bg);
        $white = imagecolorallocate($img, 255, 255, 255);
        $this->centerText($img, $text, $white, 5, 800, 800);
        $path = $this->savePng($img, $filename);
        imagedestroy($img);
        return $path;
    }

    public function socialBanner(string $text, string $hex, string $filename): string
    {
        $img = imagecreatetruecolor(1500, 500);
        $bg = $this->allocateColor($img, $hex);
        imagefill($img, 0, 0, $bg);
        $white = imagecolorallocate($img, 255, 255, 255);
        $this->centerText($img, $text, $white, 5, 1500, 500);
        $path = $this->savePng($img, $filename);
        imagedestroy($img);
        return $path;
    }

    public function faviconPack(string $text, string $hex, string $filenameBase): array
    {
        $sizes = [16, 32, 64, 128];
        $paths = [];
        foreach ($sizes as $size) {
            $img = imagecreatetruecolor($size, $size);
            $bg = $this->allocateColor($img, $hex);
            imagefill($img, 0, 0, $bg);
            $white = imagecolorallocate($img, 255, 255, 255);
            $this->centerText($img, $text, $white, 1, $size, $size);
            $paths[] = $this->savePng($img, $filenameBase . '-' . $size . '.png');
            imagedestroy($img);
        }
        return $paths;
    }

    public function emailSignature(string $name, string $title, string $company, string $hex): string
    {
        $color = $hex;
        $html = '<div style="font-family: Arial, sans-serif; font-size:14px; line-height:1.4;">';
        $html .= '<strong style="color:' . $color . ';">' . htmlspecialchars($name) . '</strong><br>';
        $html .= '<span>' . htmlspecialchars($title) . '</span><br>';
        $html .= '<span>' . htmlspecialchars($company) . '</span>';
        $html .= '</div>';
        return $html;
    }

    private function allocateColor($img, string $hex)
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $rgb = [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
        return imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);
    }

    private function centerText($img, string $text, $color, int $size, int $width, int $height): void
    {
        $font = 5;
        $text = substr($text, 0, 10);
        $textWidth = imagefontwidth($font) * strlen($text);
        $textHeight = imagefontheight($font);
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2;
        imagestring($img, $font, (int) $x, (int) $y, $text, $color);
    }

    private function savePng($img, string $filename): string
    {
        $base = rtrim(Config::get('storage.path', __DIR__ . '/../../storage'), '/');
        $path = $base . '/uploads/' . $filename;
        imagepng($img, $path);
        return 'uploads/' . $filename;
    }
}

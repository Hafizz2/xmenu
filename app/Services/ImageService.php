<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Turns an uploaded photo into WebP files at several widths.
 * Stored as {base}-{width}.webp; the base path is what goes in the database.
 */
class ImageService
{
    public const ITEM_WIDTHS = [320, 640, 1024];
    public const COVER_WIDTHS = [640, 1024];
    public const LOGO_WIDTHS = [128, 256, 512];

    /**
     * @param  float|null  $ratio  width/height to centre-crop to, or null to keep the shape
     * @param  bool  $png  also write a PNG at the largest width (used in print PDFs)
     */
    public function store(UploadedFile $file, string $dir, array $widths, ?float $ratio = null, bool $png = false): string
    {
        $src = $this->load($file->getRealPath());

        if ($ratio) {
            $src = $this->cropTo($src, $ratio);
        }

        $base = trim($dir, '/').'/'.Str::lower(Str::random(16));
        $disk = Storage::disk('public');
        $srcW = imagesx($src);

        foreach ($widths as $w) {
            $img = $this->resize($src, min($w, $srcW));
            ob_start();
            imagewebp($img, null, 78);
            $disk->put("{$base}-{$w}.webp", ob_get_clean());
            if ($png && $w === max($widths)) {
                ob_start();
                imagepng($img, null, 6);
                $disk->put("{$base}-{$w}.png", ob_get_clean());
            }
            if ($img !== $src) {
                imagedestroy($img);
            }
        }
        imagedestroy($src);

        return $base;
    }

    public function delete(?string $base): void
    {
        if (! $base) {
            return;
        }
        $disk = Storage::disk('public');
        $dir = dirname($base);
        foreach ($disk->files($dir) as $f) {
            if (str_starts_with($f, $base.'-')) {
                $disk->delete($f);
            }
        }
    }

    private function load(string $path): \GdImage
    {
        $data = @file_get_contents($path);
        $img = $data ? @imagecreatefromstring($data) : false;
        if (! $img) {
            throw new RuntimeException('This image could not be read. Please upload a JPG, PNG or WebP.');
        }

        // Phones save photos sideways and rely on EXIF to rotate them.
        if (function_exists('exif_read_data') && ($exif = @exif_read_data($path)) && ! empty($exif['Orientation'])) {
            $img = match ((int) $exif['Orientation']) {
                3 => imagerotate($img, 180, 0),
                6 => imagerotate($img, -90, 0),
                8 => imagerotate($img, 90, 0),
                default => $img,
            };
        }

        imagepalettetotruecolor($img);
        imagealphablending($img, false);
        imagesavealpha($img, true);

        return $img;
    }

    private function cropTo(\GdImage $src, float $ratio): \GdImage
    {
        $w = imagesx($src);
        $h = imagesy($src);
        if (abs($w / $h - $ratio) < 0.01) {
            return $src;
        }
        if ($w / $h > $ratio) {
            $cw = (int) round($h * $ratio);
            $ch = $h;
        } else {
            $cw = $w;
            $ch = (int) round($w / $ratio);
        }
        $out = imagecrop($src, ['x' => (int) (($w - $cw) / 2), 'y' => (int) (($h - $ch) / 2), 'width' => $cw, 'height' => $ch]);
        imagedestroy($src);

        return $out;
    }

    private function resize(\GdImage $src, int $w): \GdImage
    {
        $sw = imagesx($src);
        if ($w >= $sw) {
            return $src;
        }
        $h = (int) round(imagesy($src) * $w / $sw);
        $dst = imagecreatetruecolor($w, $h);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $sw, imagesy($src));

        return $dst;
    }
}

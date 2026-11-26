<?php

namespace App\Services;

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * Encodes with chillerlan/php-qrcode and draws the modules ourselves with GD,
 * so we control the style (square or dots) and colour.
 */
class QrService
{
    /** @return bool[][] [y][x] true = dark */
    public function matrix(string $data): array
    {
        $options = new QROptions([
            'eccLevel' => EccLevel::Q,
            'addQuietzone' => false,
        ]);
        $m = (new QRCode($options))->addByteSegment($data)->getQRMatrix();
        $size = $m->getSize();
        $out = [];
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $out[$y][$x] = $m->check($x, $y);
            }
        }

        return $out;
    }

    /**
     * Render a PNG. $px is the final pixel size of one module; the quiet zone is $quiet modules.
     */
    public function png(string $data, string $dark = '#111111', string $light = '#FFFFFF', string $style = 'square', int $px = 18, int $quiet = 2, bool $transparent = false): string
    {
        $matrix = $this->matrix($data);
        $n = count($matrix);
        $ss = 3; // supersample for smooth dots
        $m = $px * $ss;
        $full = ($n + $quiet * 2) * $m;

        $img = imagecreatetruecolor($full, $full);
        imagealphablending($img, true);
        [$lr, $lg, $lb] = hex_rgb($light);
        [$dr, $dg, $db] = hex_rgb($dark);
        $bg = $transparent ? imagecolorallocatealpha($img, $lr, $lg, $lb, 127) : imagecolorallocate($img, $lr, $lg, $lb);
        $fg = imagecolorallocate($img, $dr, $dg, $db);
        $lightCol = imagecolorallocate($img, $lr, $lg, $lb);
        if ($transparent) {
            imagealphablending($img, false);
            imagesavealpha($img, true);
        }
        imagefilledrectangle($img, 0, 0, $full, $full, $bg);
        if ($transparent) {
            imagealphablending($img, true);
        }

        $isFinder = fn ($x, $y) => ($x < 7 && $y < 7) || ($x >= $n - 7 && $y < 7) || ($x < 7 && $y >= $n - 7);
        $o = $quiet * $m;

        for ($y = 0; $y < $n; $y++) {
            for ($x = 0; $x < $n; $x++) {
                if (! $matrix[$y][$x] || $isFinder($x, $y)) {
                    continue;
                }
                $x0 = $o + $x * $m;
                $y0 = $o + $y * $m;
                if ($style === 'dots') {
                    $d = (int) ($m * 0.86);
                    imagefilledellipse($img, $x0 + intdiv($m, 2), $y0 + intdiv($m, 2), $d, $d, $fg);
                } else {
                    imagefilledrectangle($img, $x0, $y0, $x0 + $m - 1, $y0 + $m - 1, $fg);
                }
            }
        }

        // Finder patterns: 7x7 ring, 3x3 centre.
        $r = $style === 'dots' ? (int) ($m * 1.8) : 0;
        foreach ([[0, 0], [$n - 7, 0], [0, $n - 7]] as [$fx, $fy]) {
            $x0 = $o + $fx * $m;
            $y0 = $o + $fy * $m;
            $this->roundRect($img, $x0, $y0, $x0 + 7 * $m - 1, $y0 + 7 * $m - 1, $r, $fg);
            $this->roundRect($img, $x0 + $m, $y0 + $m, $x0 + 6 * $m - 1, $y0 + 6 * $m - 1, (int) ($r * 0.6), $transparent ? $lightCol : $bg);
            $this->roundRect($img, $x0 + 2 * $m, $y0 + 2 * $m, $x0 + 5 * $m - 1, $y0 + 5 * $m - 1, (int) ($r * 0.5), $fg);
        }

        $outSize = (int) ($full / $ss);
        $out = imagecreatetruecolor($outSize, $outSize);
        if ($transparent) {
            imagealphablending($out, false);
            imagesavealpha($out, true);
        }
        imagecopyresampled($out, $img, 0, 0, 0, 0, $outSize, $outSize, $full, $full);
        imagedestroy($img);

        ob_start();
        imagepng($out, null, 6);
        imagedestroy($out);

        return ob_get_clean();
    }

    public function dataUri(string $data, string $dark = '#111111', string $style = 'square', int $px = 18): string
    {
        return 'data:image/png;base64,'.base64_encode($this->png($data, $dark, '#FFFFFF', $style, $px));
    }

    private function roundRect(\GdImage $img, int $x1, int $y1, int $x2, int $y2, int $r, int $col): void
    {
        if ($r <= 0) {
            imagefilledrectangle($img, $x1, $y1, $x2, $y2, $col);

            return;
        }
        $r = min($r, intdiv($x2 - $x1, 2), intdiv($y2 - $y1, 2));
        imagefilledrectangle($img, $x1 + $r, $y1, $x2 - $r, $y2, $col);
        imagefilledrectangle($img, $x1, $y1 + $r, $x2, $y2 - $r, $col);
        $d = $r * 2;
        imagefilledellipse($img, $x1 + $r, $y1 + $r, $d, $d, $col);
        imagefilledellipse($img, $x2 - $r, $y1 + $r, $d, $d, $col);
        imagefilledellipse($img, $x1 + $r, $y2 - $r, $d, $d, $col);
        imagefilledellipse($img, $x2 - $r, $y2 - $r, $d, $d, $col);
    }
}

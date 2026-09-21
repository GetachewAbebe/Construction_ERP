<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Pure PHP QR Code Generator (SVG).
 * Zero external dependencies, 100% offline-ready.
 * Implements ISO/IEC 18004 QR Code specification (Byte mode, Version 1-5).
 */
class QrCodeService
{
    private const GF256_EXP = [
        1, 2, 4, 8, 16, 32, 64, 128, 29, 58, 116, 232, 205, 135, 19, 38,
        76, 152, 45, 90, 180, 117, 234, 201, 143, 3, 6, 12, 24, 48, 96, 192,
        157, 39, 78, 156, 37, 74, 148, 53, 106, 212, 217, 223, 219, 211, 203, 147,
        59, 118, 236, 197, 151, 51, 102, 204, 133, 23, 46, 92, 184, 109, 218, 209,
        215, 227, 187, 107, 214, 225, 191, 99, 198, 145, 63, 126, 252, 245, 247, 243,
        235, 203, 139, 11, 22, 44, 88, 176, 125, 250, 233, 207, 131, 27, 54, 108,
        216, 221, 223, 209, 213, 215, 225, 189, 103, 206, 129, 31, 62, 124, 248, 237,
        199, 147, 61, 122, 244, 245, 241, 231, 179, 123, 246, 241, 229, 183, 115, 230,
        177, 127, 254, 249, 239, 195, 155, 43, 86, 172, 85, 170, 73, 146, 57, 114,
        228, 181, 119, 238, 193, 159, 35, 70, 140, 5, 10, 20, 40, 80, 160, 93,
        186, 105, 210, 201, 141, 7, 14, 28, 56, 112, 224, 189, 101, 202, 137, 15,
        30, 60, 120, 240, 237, 199, 145, 61, 122, 244, 243, 233, 205, 133, 21, 42,
        84, 168, 77, 154, 41, 82, 164, 85, 170, 71, 142, 1, 2, 4, 8, 16,
        32, 64, 128, 29, 58, 116, 232, 205, 135, 19, 38, 76, 152, 45, 90, 180,
        117, 234, 201, 143, 3, 6, 12, 24, 48, 96, 192, 157, 39, 78, 156, 37,
        74, 148, 53, 106, 212, 217, 223, 219, 211, 203, 147, 59, 118, 236, 197, 151
    ];

    private static ?array $gf256Log = null;

    /**
     * Generate an inline SVG for the given text/URL.
     *
     * @param string $text Content to encode
     * @param int $size Width/Height of rendered SVG in pixels
     * @param string $fgColor Foreground hex color (default: #0f172a navy-black)
     * @param string $bgColor Background hex color (default: transparent or #ffffff)
     */
    public function svg(string $text, int $size = 180, string $fgColor = '#0f172a', string $bgColor = '#ffffff'): string
    {
        $matrix = $this->generateMatrix($text);
        $modules = count($matrix);
        $quietZone = 4;
        $totalModules = $modules + ($quietZone * 2);
        $scale = $size / $totalModules;

        $rects = '';
        for ($r = 0; $r < $modules; $r++) {
            for ($c = 0; $c < $modules; $c++) {
                if ($matrix[$r][$c]) {
                    $x = round(($c + $quietZone) * $scale, 2);
                    $y = round(($r + $quietZone) * $scale, 2);
                    $w = round($scale, 2);
                    $rects .= sprintf('<rect x="%s" y="%s" width="%s" height="%s" fill="%s"/>', $x, $y, $w, $w, $fgColor);
                }
            }
        }

        $bg = $bgColor !== 'transparent'
            ? sprintf('<rect width="%d" height="%d" fill="%s"/>', $size, $size, $bgColor)
            : '';

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d" width="%d" height="%d" shape-rendering="crispEdges">%s%s</svg>',
            $size, $size, $size, $size, $bg, $rects
        );
    }

    /**
     * Return base64 encoded SVG data URI.
     */
    public function dataUri(string $text, int $size = 180): string
    {
        return 'data:image/svg+xml;base64,' . base64_encode($this->svg($text, $size));
    }

    /**
     * Build QR matrix for given text (Version 1-4 Level L or M).
     */
    public function generateMatrix(string $text): array
    {
        self::initLogTable();
        $len = strlen($text);

        // Version capacity table for Byte Mode (Level L: 19, 34, 55, 80, 108)
        $version = 1;
        $capacities = [1 => 19, 2 => 34, 3 => 55, 4 => 80, 5 => 108];
        foreach ($capacities as $v => $cap) {
            if ($len <= $cap) {
                $version = $v;
                break;
            }
        }
        if ($len > 108) {
            $version = 5;
            $text = substr($text, 0, 108);
            $len = 108;
        }

        // Total data codewords & EC codewords per version (Level L)
        $spec = [
            1 => ['total' => 26, 'data' => 19, 'ec' => 7, 'blocks' => 1, 'align' => []],
            2 => ['total' => 44, 'data' => 34, 'ec' => 10, 'blocks' => 1, 'align' => [6, 18]],
            3 => ['total' => 70, 'data' => 55, 'ec' => 15, 'blocks' => 1, 'align' => [6, 22]],
            4 => ['total' => 100, 'data' => 80, 'ec' => 20, 'blocks' => 1, 'align' => [6, 26]],
            5 => ['total' => 134, 'data' => 108, 'ec' => 26, 'blocks' => 1, 'align' => [6, 30]],
        ][$version];

        // 1. Bit Stream
        $bits = '0100'; // Byte mode
        $bits .= str_pad(decbin($len), 8, '0', STR_PAD_LEFT);
        for ($i = 0; $i < $len; $i++) {
            $bits .= str_pad(decbin(ord($text[$i])), 8, '0', STR_PAD_LEFT);
        }

        // Terminator
        $maxBits = $spec['data'] * 8;
        $bits .= substr('0000', 0, max(0, min(4, $maxBits - strlen($bits))));

        // Padding to byte boundary
        if (strlen($bits) % 8 !== 0) {
            $bits .= str_repeat('0', 8 - (strlen($bits) % 8));
        }

        // Pad bytes (0xEC, 0x11)
        $padBytes = ['11101100', '00010001'];
        $padIdx = 0;
        while (strlen($bits) < $maxBits) {
            $bits .= $padBytes[$padIdx % 2];
            $padIdx++;
        }

        // 2. Data codewords
        $dataCodewords = [];
        for ($i = 0; $i < $spec['data']; $i++) {
            $dataCodewords[] = bindec(substr($bits, $i * 8, 8));
        }

        // 3. Reed-Solomon EC Codewords
        $ecCodewords = $this->calculateReedSolomon($dataCodewords, $spec['ec']);

        // 4. Combined codewords
        $allCodewords = array_merge($dataCodewords, $ecCodewords);

        // 5. Initialize Matrix
        $dim = 17 + ($version * 4);
        $matrix = array_fill(0, $dim, array_fill(0, $dim, null));
        $reserved = array_fill(0, $dim, array_fill(0, $dim, false));

        // Place Finder Patterns
        $this->placeFinder($matrix, $reserved, 0, 0);
        $this->placeFinder($matrix, $reserved, $dim - 7, 0);
        $this->placeFinder($matrix, $reserved, 0, $dim - 7);

        // Place Alignment Pattern (v2+)
        if (!empty($spec['align'])) {
            $x = $spec['align'][1];
            $y = $spec['align'][1];
            $this->placeAlignment($matrix, $reserved, $x - 2, $y - 2);
        }

        // Timing Patterns
        for ($i = 8; $i < $dim - 8; $i++) {
            $val = ($i % 2 === 0) ? 1 : 0;
            if ($matrix[6][$i] === null) {
                $matrix[6][$i] = $val;
                $reserved[6][$i] = true;
            }
            if ($matrix[$i][6] === null) {
                $matrix[$i][6] = $val;
                $reserved[$i][6] = true;
            }
        }

        // Dark module
        $matrix[4 * $version + 9][8] = 1;
        $reserved[4 * $version + 9][8] = true;

        // Reserve Format Information Area
        for ($i = 0; $i < 9; $i++) {
            $reserved[8][$i] = true;
            $reserved[$i][8] = true;
        }
        for ($i = 0; $i < 8; $i++) {
            $reserved[8][$dim - 1 - $i] = true;
            $reserved[$dim - 1 - $i][8] = true;
        }

        // 6. Place Data Bits
        $dataBits = '';
        foreach ($allCodewords as $cw) {
            $dataBits .= str_pad(decbin($cw), 8, '0', STR_PAD_LEFT);
        }
        // Remainder bits (if any for version)
        $remainderBits = [1 => 0, 2 => 7, 3 => 7, 4 => 7, 5 => 7][$version];
        $dataBits .= str_repeat('0', $remainderBits);

        $bitIdx = 0;
        $up = true;
        for ($col = $dim - 1; $col > 0; $col -= 2) {
            if ($col === 6) $col--; // Skip vertical timing line
            $rowRange = $up ? range($dim - 1, 0) : range(0, $dim - 1);
            foreach ($rowRange as $row) {
                foreach ([$col, $col - 1] as $c) {
                    if (!$reserved[$row][$c]) {
                        $bit = ($bitIdx < strlen($dataBits)) ? (int)$dataBits[$bitIdx] : 0;
                        // Apply standard mask pattern 0: (row + col) % 2 == 0
                        $mask = (($row + $c) % 2 === 0) ? 1 : 0;
                        $matrix[$row][$c] = $bit ^ $mask;
                        $bitIdx++;
                    }
                }
            }
            $up = !$up;
        }

        // 7. Apply Format Info: Mask 0, Level L -> Format bits: 111011111000100
        $formatBits = '111011111000100';
        $this->placeFormatInfo($matrix, $dim, $formatBits);

        return $matrix;
    }

    private function placeFinder(array &$matrix, array &$reserved, int $startX, int $startY): void
    {
        for ($r = -1; $r <= 7; $r++) {
            for ($c = -1; $c <= 7; $c++) {
                $x = $startX + $c;
                $y = $startY + $r;
                if ($x >= 0 && $x < count($matrix) && $y >= 0 && $y < count($matrix)) {
                    $reserved[$y][$x] = true;
                    if ($r === -1 || $r === 7 || $c === -1 || $c === 7) {
                        $matrix[$y][$x] = 0;
                    } elseif ($r === 0 || $r === 6 || $c === 0 || $c === 6) {
                        $matrix[$y][$x] = 1;
                    } elseif ($r >= 2 && $r <= 4 && $c >= 2 && $c <= 4) {
                        $matrix[$y][$x] = 1;
                    } else {
                        $matrix[$y][$x] = 0;
                    }
                }
            }
        }
    }

    private function placeAlignment(array &$matrix, array &$reserved, int $startX, int $startY): void
    {
        for ($r = 0; $r < 5; $r++) {
            for ($c = 0; $c < 5; $c++) {
                $y = $startY + $r;
                $x = $startX + $c;
                if (!$reserved[$y][$x]) {
                    $reserved[$y][$x] = true;
                    if ($r === 0 || $r === 4 || $c === 0 || $c === 4 || ($r === 2 && $c === 2)) {
                        $matrix[$y][$x] = 1;
                    } else {
                        $matrix[$y][$x] = 0;
                    }
                }
            }
        }
    }

    private function placeFormatInfo(array &$matrix, int $dim, string $format): void
    {
        // Around top-left
        $coordsTopLeft = [
            [8, 0], [8, 1], [8, 2], [8, 3], [8, 4], [8, 5], [8, 7], [8, 8],
            [7, 8], [5, 8], [4, 8], [3, 8], [2, 8], [1, 8], [0, 8]
        ];
        for ($i = 0; $i < 15; $i++) {
            [$r, $c] = $coordsTopLeft[$i];
            $matrix[$r][$c] = (int)$format[$i];
        }

        // Around bottom-left and top-right
        for ($i = 0; $i < 7; $i++) {
            $matrix[$dim - 1 - $i][8] = (int)$format[$i];
        }
        for ($i = 7; $i < 15; $i++) {
            $matrix[8][$dim - 15 + $i] = (int)$format[$i];
        }
    }

    private function calculateReedSolomon(array $data, int $ecCount): array
    {
        $gen = $this->generatorPolynomial($ecCount);
        $poly = array_merge($data, array_fill(0, $ecCount, 0));

        for ($i = 0; $i < count($data); $i++) {
            $lead = $poly[$i];
            if ($lead !== 0) {
                $logLead = self::$gf256Log[$lead];
                for ($j = 0; $j < count($gen); $j++) {
                    $poly[$i + $j] ^= self::GF256_EXP[($gen[$j] + $logLead) % 255];
                }
            }
        }

        return array_slice($poly, count($data));
    }

    private function generatorPolynomial(int $degree): array
    {
        $g = [0]; // alpha^0
        for ($i = 0; $i < $degree; $i++) {
            $next = [];
            $next[0] = ($g[0] + $i) % 255;
            for ($j = 1; $j < count($g); $j++) {
                $term1 = self::GF256_EXP[($g[$j] + $i) % 255];
                $term2 = self::GF256_EXP[$g[$j - 1]];
                $next[$j] = self::$gf256Log[$term1 ^ $term2];
            }
            $next[] = $g[count($g) - 1];
            $g = $next;
        }
        return $g;
    }

    private static function initLogTable(): void
    {
        if (self::$gf256Log === null) {
            self::$gf256Log = array_fill(0, 256, 0);
            for ($i = 0; $i < 255; $i++) {
                self::$gf256Log[self::GF256_EXP[$i]] = $i;
            }
        }
    }
}

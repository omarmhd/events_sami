<?php

namespace App\Services;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;

class QrCodeService
{
    /**
     * Generate QR code as Base64 PNG (no file storage)
     */
    public function generateBase64(string $data, int $size = 200): string
    {
        $matrix = Encoder::encode($data, ErrorCorrectionLevel::L());

        $qrBinary = $this->renderPngFromMatrix($matrix->getMatrix()->getArray()->toArray(), $size);

        return 'data:image/png;base64,' . base64_encode($qrBinary);
    }

    /**
     * Render a QR matrix into a PNG binary string without GD/Imagick.
     *
     * @param array<int, array<int, int>> $matrixRows
     */
    private function renderPngFromMatrix(array $matrixRows, int $size): string
    {
        $matrixSize = count($matrixRows);
        $margin = 4;
        $pointsOnSide = $matrixSize + ($margin * 2);
        $moduleSize = max(1, intdiv($size, $pointsOnSide));
        $contentSize = $pointsOnSide * $moduleSize;
        $canvasSize = $contentSize;
        $offset = 0;

        $rawImage = '';

        for ($y = 0; $y < $canvasSize; $y++) {
            $rawImage .= "\x00";

            $moduleY = intdiv($y - $offset, $moduleSize);
            $insideY = $y >= $offset && $y < ($offset + $contentSize);

            for ($x = 0; $x < $canvasSize; $x++) {
                $isBlack = false;

                if ($insideY) {
                    $moduleX = intdiv($x - $offset, $moduleSize);
                    $insideX = $x >= $offset && $x < ($offset + $contentSize);

                    if ($insideX && isset($matrixRows[$moduleY][$moduleX])) {
                        $isBlack = (bool) $matrixRows[$moduleY][$moduleX];
                    }
                }

                $rawImage .= $isBlack ? "\x00\x00\x00" : "\xFF\xFF\xFF";
            }
        }

        $png = "\x89PNG\r\n\x1a\n";
        $png .= $this->pngChunk('IHDR', pack('NNCCCCC', $canvasSize, $canvasSize, 8, 2, 0, 0, 0));
        $png .= $this->pngChunk('IDAT', gzcompress($rawImage, 9));
        $png .= $this->pngChunk('IEND', '');

        return $png;
    }

    private function pngChunk(string $type, string $data): string
    {
        return pack('N', strlen($data))
            . $type
            . $data
            . pack('N', crc32($type . $data));
    }
}

<?php

namespace App\Services;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeService
{
    /**
     * Generate QR code as Base64 PNG (no file storage)
     */
    public function generateBase64(string $data, int $size = 200): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        // Binary SVG output
        $qrBinary = $writer->writeString($data);

        return 'data:image/svg+xml;base64,' . base64_encode($qrBinary);
    }
}

<?php

namespace App\Modules\Admin\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

final class QrCodeSvg
{
    public function render(string $url, int $size = 480): string
    {
        return (new Writer(new ImageRenderer(new RendererStyle($size, 4), new SvgImageBackEnd)))->writeString($url);
    }
}

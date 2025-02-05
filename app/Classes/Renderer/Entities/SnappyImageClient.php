<?php

namespace App\Classes\Renderer\Entities;

use App\Classes\Renderer\Contracts\ImageClientInterface;
use Knp\Snappy\Image;

class SnappyImageClient implements ImageClientInterface
{

    protected $image;

    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    public function generate(string $markup, string $path): string
    {
        $this->image->generateFromHtml($markup, $path, ['enable-local-file-access' => true,
        ]);

        return $path;
    }
}

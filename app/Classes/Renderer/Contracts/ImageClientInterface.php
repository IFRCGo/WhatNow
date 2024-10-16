<?php

namespace App\Classes\Renderer\Contracts;

interface ImageClientInterface
{
    public function generate(string $markup, string $path): string;
}

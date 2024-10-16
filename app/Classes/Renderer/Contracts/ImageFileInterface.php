<?php

namespace App\Classes\Renderer\Contracts;

interface ImageFileInterface
{
    public function getPath(): string;
    public function exists(): bool;
    public function delete(): bool;
}

<?php

namespace App\Classes\Renderer\Entities;

use App\Classes\Renderer\Contracts\ImageFileInterface;

class ImageFile implements ImageFileInterface
{
    
    protected $directory;
    
    protected $filename;

    public function __construct(string $directory, string $filename)
    {
        $this->directory = $directory;
        $this->filename = $filename;
    }

    public function getPath(): string
    {
        return $this->directory . '/' . $this->filename;
    }

    public function exists(): bool
    {
        return file_exists($this->getPath());
    }

    public function delete(): bool
    {
        return @unlink($this->getPath());
    }
}

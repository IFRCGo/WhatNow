<?php

namespace Tests\Unit\Renderer;

use App\Classes\Renderer\Entities\ImageFile;
use PHPUnit\Framework\TestCase;

class ImageFileTest extends TestCase
{
    public function test_it_returns_correct_path()
    {
        $imageFile = $this->getTestImageFile();
        $this->assertEquals($this->getTestFileDirectory() . '/' . $this->getTestFileName(), $imageFile->getPath());
    }

    public function test_file_does_not_exist()
    {
        $imageFile = $this->getTestImageFile();
        $this->assertFalse($imageFile->exists());
    }

    public function test_it_deletes_existing_file()
    {
        $this->createTestFile();
        $imageFile = $this->getTestImageFile();

        $this->assertTrue($imageFile->exists());
        $this->assertTrue($imageFile->delete());
        $this->assertFalse($imageFile->exists());
    }

    public function test_it_does_not_delete_non_existent_file()
    {
        $imageFile = $this->getTestImageFile();

        $this->assertFalse($imageFile->exists());
        $this->assertFalse($imageFile->delete());
        $this->assertFalse($imageFile->exists());
    }

    protected function createTestFile()
    {
        touch($this->getTestFileDirectory() . '/' . $this->getTestFileName());
    }

    protected function getTestImageFile()
    {
        return new ImageFile($this->getTestFileDirectory(), $this->getTestFileName());
    }

    protected function getTestFileDirectory()
    {
        return getcwd();
    }

    protected function getTestFileName()
    {
        return 'test.file';
    }
}

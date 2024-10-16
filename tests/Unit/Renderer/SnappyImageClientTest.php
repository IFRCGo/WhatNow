<?php

namespace Tests\Unit\Renderer;

use App\Classes\Renderer\Entities\SnappyImageClient;
use Knp\Snappy\Image;
use PHPUnit\Framework\TestCase;

class SnappyImageClientTest extends TestCase
{
    public function test_it_generates_image()
    {
        $image = \Mockery::mock(Image::class)->makePartial();
        $image->shouldReceive('generateFromHtml')
            ->once()
            ->withArgs(['image_markup', 'image_path'])
            ->andReturn('image_path')
            ->getMock();

        $client = new SnappyImageClient($image);
        $path = $client->generate('image_markup', 'image_path');

        $this->assertEquals('image_path', $path);
    }
}

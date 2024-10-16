<?php

namespace Tests\Unit\Renderer;

use App\Classes\Renderer\Entities\EventType;
use App\Classes\Renderer\Entities\Image;
use App\Classes\Renderer\Entities\Language;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function test_it_returns_correct_property_values()
    {
        $societyName = 'my_society';
        $title = 'my_title';
        $stageRef = 'my_stageRef';
        $language = \Mockery::mock(Language::class)
            ->shouldReceive('isRtl')
            ->once()
            ->withNoArgs()
            ->andReturn(true)
            ->getMock();
        $eventType = \Mockery::mock(EventType::class)
            ->shouldReceive('getIconName')
            ->once()
            ->withNoArgs()
            ->andReturn('my_icon_name')
            ->getMock();
        $items = [
            'item1',
            'item2',
            'item3'
        ];

        $asArray = [
            'society' => $societyName,
            'title' => $title,
            'stageRef' => $stageRef,
            'items' => $items,
            'rtl' => true,
            'eventIcon' => 'my_icon_name'
        ];

        $image = new Image(
            $societyName,
            $title,
            $stageRef,
            $language,
            $eventType,
            $items
        );

        $this->assertEquals($societyName, $image->getSocietyName());
        $this->assertEquals($title, $image->getTitle());
        $this->assertEquals($stageRef, $image->getStageRef());
        $this->assertSame($language, $image->getLanguage());
        $this->assertSame($eventType, $image->getEventType());
        $this->assertSame($items, $image->getItems());

        $this->assertSame($asArray, $image->toArray());
    }
}

<?php

namespace Tests\Unit\Renderer;

use App\Classes\Renderer\Entities\EventType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;


class EventTypeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_returns_correct_event_type_name()
    {
        $eventType = new EventType('My Event Type Name');
        $this->assertEquals('My Event Type Name', $eventType->getName());
    }

    public function test_it_returns_correct_icon_name()
    {
        $event =  factory(\App\Models\EventType::class)->create();
        $eventType = new EventType($event->name);
        $this->assertEquals($event->icon, $eventType->getIconName());
    }

    public function test_it_returns_fallback_icon()
    {
        $eventType = new EventType('DO NOT EXIST');
        $this->assertEquals('general@3x.png', $eventType->getIconName());
    }
}

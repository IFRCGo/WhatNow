<?php

namespace App\Classes\Renderer\Entities;

class EventType
{
    
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    
    public function getName(): string
    {
        return $this->name;
    }

    
    public function getIconName(): string
    {
        $eventType = \App\Models\EventType::where('name', $this->name)->first();

        if($eventType === null)
        {
            $eventType = \App\Models\EventType::where('code', 'other')->first();
        }

        return $eventType->icon;
    }
}

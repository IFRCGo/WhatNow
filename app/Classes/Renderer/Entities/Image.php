<?php

namespace App\Classes\Renderer\Entities;

use App\Classes\Renderer\Contracts\ImageInterface;
use Illuminate\Support\Facades\Storage;

class Image implements ImageInterface
{
    
    protected $societyName;
    
    protected $title;
    
    protected $stageRef;
    
    protected $language;
    
    protected $eventType;
    
    protected $items;

    
    public function __construct(
        string $societyName,
        string $title,
        string $stageRef,
        Language $language,
        EventType $eventType,
        array $items
    ) {
        $this->societyName = $societyName;
        $this->title = $title;
        $this->stageRef = $stageRef;
        $this->language = $language;
        $this->eventType = $eventType;
        $this->items = $items;
    }

    
    public function getSocietyName(): string
    {
        return $this->societyName;
    }

    
    public function getTitle(): string
    {
        return $this->title;
    }

    
    public function getStageRef(): string
    {
        return $this->stageRef;
    }

    
    public function getLanguage(): Language
    {
        return $this->language;
    }

    
    public function getEventType(): EventType
    {
        return $this->eventType;
    }

    
    public function getItems(): array
    {
        return $this->items;
    }

    
    public function getMarkup(): string
    {
        $payload = $this->toArray();
        $payload['eventIcon'] = $this->getIconUrl($payload['eventIcon']) . '?' . $this->getCachebust();

        return view('whatnowImage', $payload);
    }

    
    public function toArray()
    {
        return [
            'society' => $this->getSocietyName(),
            'title' => $this->getTitle(),
            'stageRef' => $this->getStageRef(),
            'items' => $this->getItems(),
            'rtl' => $this->getLanguage()->isRtl(),
            'eventIcon' => $this->getEventType()->getIconName()
        ];
    }

    
    protected function getIconUrl(string $filename): string
    {
        return Storage::disk('public')->path($filename);
    }

    
    protected function getCachebust(): string
    {
        $source = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

        $cachebust = '';
        for ($i = 0; $i < 8; $i++) {
            $cachebust .= substr($source, rand(0, strlen($source) - 1), 1);
        }

        return $cachebust;
    }
}

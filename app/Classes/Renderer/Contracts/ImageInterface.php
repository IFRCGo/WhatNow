<?php

namespace App\Classes\Renderer\Contracts;

use App\Classes\Renderer\Entities\EventType;
use App\Classes\Renderer\Entities\Language;
use Illuminate\Contracts\Support\Arrayable;

interface ImageInterface extends Arrayable
{
    public function getSocietyName(): string;
    public function getTitle(): string;
    public function getStageRef(): string;
    public function getLanguage(): Language;
    public function getEventType(): EventType;
    public function getItems(): array;
    public function getMarkup(): string;
}

<?php

namespace App\Classes\RcnApi\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class InstructionTranslation implements \JsonSerializable, Arrayable
{
    const EVENT_STAGES = [
        'immediate',
        'warning',
        'anticipated',
        'assess_and_plan',
        'mitigate_risks',
        'prepare_to_respond',
        'recover'
    ];


    protected $id;


    protected $lang;


    protected $webUrl;


    protected $title;


    protected $description;




    protected $stages;


    protected $createdAt = null;


    protected $published;


    public static function createFromRequest(array $array)
    {
        $translation = new self();
        $translation->lang          = $array['lang'];
        $translation->webUrl        = $array['webUrl'];
        $translation->title         = $array['title'];
        $translation->description   = $array['description'];
        $translation->stages        = new Collection($array['stages']);
        return $translation;
    }


    public static function createFromResponse(array $array)
    {
        $translation = new self();

        $translation->id            = $array['id'];
        $translation->lang          = $array['lang'];
        $translation->webUrl        = $array['webUrl'];
        $translation->title         = $array['title'];
        $translation->description   = $array['description'];
        $translation->stages        = new Collection($array['stages']);
        $translation->createdAt = new \DateTimeImmutable($array['createdAt']);
        $translation->published = $array['published'];
        return $translation;
    }


    public function getId(): int
    {
        return $this->id;
    }


    public function getLang(): string
    {
        return $this->lang;
    }


    public function getWebUrl(): ?string
    {
        return $this->webUrl;
    }


    public function getTitle(): ?string
    {
        return $this->title;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }


    public function getStages(): Collection
    {
        return $this->stages;
    }


    public function getCreatedAt()
    {
        return $this->createdAt;
    }


    public function getPublished()
    {
        return $this->published;
    }


    public function isPublished(): bool
    {
        return $this->published;
    }


    public function toArray()
    {
        return [
            'id' => $this->id,
            'lang' => $this->lang,
            'webUrl' => $this->webUrl,
            'title' => $this->title,
            'description' => $this->description,
            'stages' => $this->stages->toArray(),
            'created_at' => $this->createdAt->format('c'),
            'published' => $this->published
        ];
    }


    public function jsonSerialize()
    {
        return [
            'lang' => $this->lang,
            'webUrl' => $this->webUrl,
            'title' => $this->title,
            'description' => $this->description,
            'stages' => $this->stages
        ];
    }
}

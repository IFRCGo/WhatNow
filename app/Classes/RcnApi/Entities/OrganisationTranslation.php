<?php

namespace App\Classes\RcnApi\Entities;

use Illuminate\Contracts\Support\Arrayable;

class OrganisationTranslation implements \JsonSerializable, Arrayable
{
    
    protected $languageCode;

    
    protected $name;

    
    protected $attributionMessage;

    
    protected $isPublished;

    protected $contributors = [];

    
    public static function createFromResponse(array $array)
    {
        $translation = new self();
        $translation->name = $array['name'];
        $translation->attributionMessage = $array['attributionMessage'];
        $translation->languageCode = $array['languageCode'];
        $translation->isPublished = $array['published'];
        $translation->contributors = $array['contributors'];
        return $translation;
    }

    
    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    
    public function getName(): string
    {
        return $this->name;
    }

    
    public function getAttributionMessage(): ?string
    {
        return $this->attributionMessage;
    }

    
    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function getContributors(): array
    {
        return $this->contributors;
    }

    
    public function toArray()
    {
        return [
            'languageCode' => $this->languageCode,
            'name' => $this->name,
            'attributionMessage' => $this->attributionMessage,
            'published' => $this->isPublished,
            'contributors' => $this->contributors
        ];
    }

    
    public function jsonSerialize(): array
    {
        return [
            'languageCode' => $this->languageCode,
            'name' => $this->name,
            'attributionMessage' => $this->attributionMessage,
            'published' => $this->isPublished,
            'contributors' => $this->contributors
        ];
    }
}

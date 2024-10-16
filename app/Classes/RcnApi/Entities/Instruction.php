<?php

namespace App\Classes\RcnApi\Entities;

use Illuminate\Contracts\Support\Arrayable;

class Instruction implements \JsonSerializable, Arrayable
{
    
    protected $id;

    
    protected $countryCode;

    
    protected $eventType;

    
    protected $regionName;

    
    protected $attribution;

    
    protected $translations = [];

    
    public static function createFromResponse(array $array)
    {
        $instruction = new self();
        $instruction->id = $array['id'];
        $instruction->countryCode = $array['countryCode'];
        $instruction->eventType = $array['eventType'];
        $instruction->regionName = $array['regionName'];

        $instruction->attribution = Organisation::createFromArray($array['attribution']);

        $instruction->translations = array_map(function (array $translation) {
            return InstructionTranslation::createFromResponse($translation);
        }, $array['translations']);

        return $instruction;
    }

    
    public static function createFromRequest(array $array)
    {
        $instruction = new self();

        if (isset($array['id'])) {
            $instruction->id = $array['id'];
        }

        $instruction->countryCode = $array['countryCode'];
        $instruction->eventType = $array['eventType'];
        $instruction->regionName = isset($array['regionName']) ? $array['regionName'] : null;

        $instruction->translations = array_map(function (array $translation) {
            return InstructionTranslation::createFromRequest($translation);
        }, $array['translations']);

        return $instruction;
    }

    
    public function getId(): ?int
    {
        return $this->id;
    }

    
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    
    public function getEventType(): string
    {
        return $this->eventType;
    }

    
    public function getTranslations(): array
    {
        return $this->translations;
    }

    
    public function getAttribution(): Organisation
    {
        return $this->attribution;
    }

    
    public function getRegionName(): string
    {
        return empty($this->regionName) ? 'National' : $this->regionName ;
    }

    
    public function getTranslationsByLanguage(string $languageCode): ?InstructionTranslation
    {
        foreach ($this->translations as $translation) {
            if ($translation->getLang() === $languageCode) {
                return $translation;
            }
        }

        return null;
    }

    
    public function setTranslation(InstructionTranslation $translation)
    {
        $this->translations[$translation->getLang()] = $translation;
    }

    
    public function setRegionName($regionName)
    {
        $this->regionName = $regionName;
    }

    
    public function getAvailableLanguages()
    {
        return array_map(function (InstructionTranslation $translation) {
            return $translation->getLang();
        }, $this->translations);
    }

    
    public function toArray(): array
    {
        $response = [
            'countryCode' => $this->countryCode,
            'eventType' => $this->eventType,
            'regionName' => $this->regionName,
            'attribution' => $this->attribution,
        ];

        if ($this->id) {
            $response['id'] = $this->id;
        }

        if (! empty($this->translations)) {
            $response['translations'] = array_map(function (InstructionTranslation $translation) {
                return $translation->toArray();
            }, $this->translations);
        }

        return $response;
    }

    
    public function jsonSerialize(): array
    {
        $response = [
            'countryCode' => $this->countryCode,
            'eventType' => $this->eventType,
            'regionName' => $this->regionName,
            'translations' => $this->translations,
        ];

        if ($this->id) {
            $response['id'] = $this->id;
        }

        return $response;
    }
}

<?php

namespace App\Classes\RcnApi\Entities;

use Illuminate\Contracts\Support\Arrayable;

class Organisation implements \JsonSerializable, Arrayable
{
    
    protected $countryCode;

    
    protected $name;

    
    protected $url;

    
    protected $translations = [];

    
    public static function createFromArray(array $array)
    {
        $attribution = new self();
        $attribution->name = $array['name'];
        $attribution->countryCode = $array['countryCode'];
        $attribution->url = $array['url'];

        if (is_array($array['translations'])) {
            foreach ($array['translations'] as $translationArray) {
                $attribution->translations[$translationArray['languageCode']] = OrganisationTranslation::createFromResponse($translationArray);
            }
        }

        return $attribution;
    }

    
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    
    public function getName(): string
    {
        return $this->name;
    }

    
    public function getUrl(): ?string
    {
        return $this->url;
    }

    
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    
    public function getTranslations(): array
    {
        return $this->translations;
    }

    
    public function getTranslationsByLanguage(string $languageCode): ?OrganisationTranslation
    {
        
        foreach ($this->translations as $translation) {
            if ($translation->getLanguageCode() === $languageCode) {
                return $translation;
            }
        }

        return null;
    }

    
    public function setTranslation(OrganisationTranslation $organisationTranslation)
    {
        
        foreach ($this->translations as $key => $translation) {
            if ($translation->getLanguageCode() === $organisationTranslation->getLanguageCode()) {
                $this->translations[$key] = $organisationTranslation;
                return;
            }
        }

        $this->translations[$organisationTranslation->getLanguageCode()] = $organisationTranslation;
    }

    
    public function toArray(): array
    {
        return [
            'countryCode' => $this->countryCode,
            'name' => $this->name,
            'url' => $this->url,
            'translations' => array_values(array_map(function (OrganisationTranslation $translation) {
                return $translation->toArray();
            }, $this->translations))
        ];
    }

    
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'url' => $this->url,
            'translations' => $this->translations
        ];
    }
}

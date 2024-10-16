<?php

namespace App\Classes\RcnApi\Entities;

use Illuminate\Contracts\Support\Arrayable;

class Application implements \JsonSerializable, Arrayable
{
    
    protected $id;

    
    protected $name;

    
    protected $description;

    
    protected $key;

    
    protected $estimatedUsers;

    
    public static function createFromArray(array $array)
    {
        $application = new self();
        $application->id = $array['id'];
        $application->name = $array['name'];
        $application->description = $array['description'];
        $application->key = $array['key'];
        $application->estimatedUsers = $array['estimatedUsers'] ?? null;

        return $application;
    }

    
    public function getId(): int
    {
        return $this->id;
    }

    
    public function getName(): string
    {
        return $this->name;
    }

    
    public function getDescription(): string
    {
        return $this->description;
    }

    
    public function getKey(): string
    {
        return $this->key;
    }

    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'key' => $this->key,
            'estimatedUsers' => $this->estimatedUsers,
        ];
    }

    
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

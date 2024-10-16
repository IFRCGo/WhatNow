<?php

namespace App\Classes\RcnApi\Entities;

use Illuminate\Contracts\Support\Arrayable;

class Alert implements \JsonSerializable, Arrayable
{
    
    protected $data;

    
    public static function createFromArray(array $array)
    {
        $alert = new self();
        $alert->data = $array;

        return $alert;
    }

    
    public function toArray(): array
    {
        return $this->data;
    }

    
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    
    public function __get($key) {
        return $this->data[$key];
    }
}

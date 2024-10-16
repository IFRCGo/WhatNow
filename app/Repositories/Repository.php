<?php

namespace App\Repositories;


abstract class Repository extends BaseRepository
{
    
    public function getAll()
    {
        return $this->query()->get();
    }

    
    public function getCount()
    {
        return $this->query()->count();
    }

    
    public function find($id)
    {
        return $this->query()->find($id);
    }

    
    public function findOrFail($id, array $with = [])
    {
        return $this->query()->with($with)->findOrFail($id);
    }
}

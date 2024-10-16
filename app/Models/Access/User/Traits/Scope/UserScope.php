<?php

namespace App\Models\Access\User\Traits\Scope;


trait UserScope
{
    
    public function scopeConfirmed($query, $confirmed = true)
    {
        return $query->where('confirmed', $confirmed);
    }

    
    public function scopeActive($query, $status = true)
    {
        return $query->where('status', $status);
    }
}

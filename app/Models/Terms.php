<?php

namespace App\Models;

use App\Models\Access\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Terms extends Model
{
    protected $table = 'terms';

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected $fillable = ['version', 'content', 'created_at', 'user_id'];

    


    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}

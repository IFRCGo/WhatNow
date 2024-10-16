<?php

namespace App\Models;

use App\Models\Access\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Terms extends Model
{
    protected $table = 'terms';

    protected $fillable = ['version', 'content', 'created_at', 'user_id'];

    
    protected $dates = [
        'created_at'
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

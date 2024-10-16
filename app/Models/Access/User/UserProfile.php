<?php

namespace App\Models\Access\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class UserProfile extends Model
{
    use SoftDeletes;

    
    protected $fillable = [
        'first_name',
        'last_name',
        'country_code',
        'organisation',
        'industry_type',
        'api_used_in',
        'terms_version',
        'notifications_enabled'
    ];

    
    protected $casts = [
        'accepted_agreement' => 'boolean',
        'notifications_enabled' => 'boolean'
    ];

    
    public function user()
    {
        return $this->belongsTo('App\Models\Access\User\User');
    }
}

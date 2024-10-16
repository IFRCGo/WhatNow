<?php

namespace App\Models\Access\User;

use Illuminate\Database\Eloquent\Model;


class UserOrganisation extends Model
{
    
    protected $fillable = ['organisation_code'];

    
    public function user()
    {
        return $this->belongsTo('App\Models\Access\User\User');
    }
}

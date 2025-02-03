<?php

namespace App\Models\Access\User;

use App\Models\Access\User\Traits\Relationship\UserRelationship;
use App\Models\Access\User\Traits\UserAccess;
use App\Models\OAuthProvider;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable,
        SoftDeletes,
        UserAccess,
        UserRelationship;
    
    
    protected $fillable = [
        'email', 'password', 'confirmation_code', 'confirmed_role'
    ];

    
    protected $casts = [
        'activated' => 'boolean',
        'confirmed' => 'boolean',
        'confirmed_role' => 'boolean',
    ];

    
    protected $attributes = [
        'activated' => true
    ];

    
    protected $dates = ['deleted_at', 'password_updated_at', 'created_at', 'updated_at', 'last_logged_in_at'];

    
    protected $hidden = [
        'password', 'remember_token',
    ];

    
    protected $appends = [
        'photo_url',
    ];

    
    protected $with = [
        'userProfile'
    ];

    
    public function getPhotoUrlAttribute()
    {
        return 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'.jpg?s=200&d=mm';
    }

    
    public function oauthProviders()
    {
        return $this->hasMany(OAuthProvider::class);
    }

    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    
    public function getJWTCustomClaims()
    {
        return [
            'permissions' => $this->getPermissions()
        ];
    }

    
    public function hasSetOwnPassword()
    {
        return !is_null($this->password_updated_at);
    }

    public function setPasswordUpdatedAt()
    {
        $this->password_updated_at = new \DateTimeImmutable('now');
        $this->save();
    }

    public function confirm()
    {
        $this->confirmed = true;
        $this->save();
    }

    
    public function isConfirmed()
    {
        return $this->confirmed === true;
    }

    
    public function isActive()
    {
        return $this->activated === true;
    }

    public function setLastLoggedIn()
    {
        $this->last_logged_in_at = new Carbon('now');
        $this->save();
    }
}

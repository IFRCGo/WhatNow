<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    protected $fillable = ['icon', 'name', 'code'];

    public function getIconPath()
	{
		return '/' . $this->icon;
	}

    public function getIconUrl()
    {
        $filepath = $this->getIconPath();

        return asset(config('app.bucket_url') .  $filepath);
    }
}

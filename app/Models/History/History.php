<?php namespace App\Models\History;

use App\Models\Access\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class History extends Model
{
    
    protected $table = 'history';

    
    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected $fillable = ['action', 'user_id', 'entity_id', 'content', 'country_code', 'language_code'];

    


    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = Carbon::now();
        });
    }

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}

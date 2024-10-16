<?php namespace App\Models\History;

use App\Models\Access\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class History extends Model
{
    
    protected $table = 'history';

    
    protected $fillable = ['action', 'user_id', 'entity_id', 'content', 'country_code', 'language_code'];

    
    protected $dates = [
        'created_at'
    ];

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
}

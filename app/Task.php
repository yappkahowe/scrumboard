<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = ['description', 'arrangement', 'stage_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function scopeOfUser($query, $user)
    {
        return $query->where('user_id', '=', $user->id);
    }

    public function scopeOfStage($query, $stage)
    {
        return $query->where('stage_id', '=', $stage->id);
    }
}

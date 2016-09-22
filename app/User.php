<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'designation', 'avatar_url', 'email', 'google_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'google_id', 'api_token', 'remember_token',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['last_reported_at', 'created_at', 'updated_at', 'deleted_at'];

    public function tasks()
    {
        return $this->hasMany('App\Task');
    }

    public function scopeOfEmail($query, $email)
    {
        return $query->where('email', '=', $email);
    }

    public function withoutGoogle()
    {
        return !$this->google_id;
    }

    public function withoutApiToken()
    {
        return !$this->api_token;
    }

    public function generateApiToken()
    {
        $this->api_token = str_random(64);

        return $this->save();
    }

    public function generateApiTokenIfNone()
    {
        return $this->withoutApiToken() ? $this->generateApiToken() : $this;
    }

    public function revokeApiToken()
    {
        $this->api_token = null;

        return $this->save();
    }

    public function report($task)
    {
        $this->last_reported_at = $task->trashed() ? $task->deleted_at : $task->updated_at;

        return $this->save();
    }
}

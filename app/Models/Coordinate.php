<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Coordinate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lat', 'lon', 'accuracy', 'tracker_sessions_id'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function device()
    {
        return $this->hasOne(Device::class,'id','device_id');
    }
}

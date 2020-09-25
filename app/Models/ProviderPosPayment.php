<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderPosPayment extends Model
{
    //
    use SoftDeletes;

    public $sendNotifications = true;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'amount', 'description', 'user_id', 'operation_id','target_id','provider_pos_id'
    ];

    public function providerPos()
    {
        return $this->belongsTo('App\Models\ProviderPos', 'provider_pos_id');
    }
}

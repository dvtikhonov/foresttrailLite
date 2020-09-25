<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class ProviderPos extends Model
{
    use SoftDeletes;

    public $sendNotifications = true;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'contacts', 'address', 'provider_id', 'name', 'phone', 'lat', 'lon', 'minimum_balance'
    ];

    /**
     * Получить устройства точки продаж.
     */
    public function devices()
    {
        return $this->hasMany('App\Models\Device', 'provider_pos_id','id');
    }
    /**
     * Получить владельца точки продаж .
     */
    public function provider()
    {
        return $this->belongsTo('App\Models\Provider', 'provider_id');
    }
    /**
     * Получить журнал перемещения устройств.
     */
    public function transferDevice()
    {
        return $this->hasMany(TransferDevice::class);
    }

//    public function clearDevice($devices_id)
//    {
//        dd (__METHOD__, '777' , $devices_id);
//
//        if (!empty($devices_id)) {
//            $result = Device::query()
//                ->whereIn('id', $devices_id)
//                ->update(['provider_pos_id' => null]);
//dump($result);
//            TransferDevice::query()
//                ->whereIn('device_id', $devices_id)
//                ->whereNull('returned_at')
//                ->update(['returned_at' => Carbon::now()]);
//        }
//    }


}

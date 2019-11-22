<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Device extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'imei', 'alias', 'options', 'provider_pos_id','device_tariff_id'
    ];


    /**
     * Получить точку продаж  данного устройства.
     */
    public function providerPos()
    {
        return $this->belongsTo('App\Models\ProviderPos');
    }

    /**
     * Get the device_tariff record associated with the device .
     */
    public function deviceTariff()
    {
        return $this->belongsTo('App\Models\DeviceTariff');
    }

    /**
     * Get the device_tariff record associated with the device .
     */
    public function coordinates()
    {
        return $this->hasMany('App\Models\Coordinate', 'device_id');
    }


    /**
     * Объедингить столбцы.
     */
    public function getTenantFullNameAttribute()
    {
        return $this->attributes['name'] .' '. $this->attributes['alias'];
    }
}

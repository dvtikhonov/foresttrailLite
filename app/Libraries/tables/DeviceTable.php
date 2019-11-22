<?php
/**
 * Created by PhpStorm.
 * User: ddd
 * Date: 01.10.2019
 * Time: 0:01
 */

namespace App\Libraries\tables;


use App\Models\Device;
use Illuminate\Support\Carbon;
use LaravelEnso\VueDatatable\app\Classes\Table;

class DeviceTable extends Table
{
    protected $templatePath = __DIR__.'/templates/device.json';

    public function query()
    {

        $coordinates_in_date_range = $this->request->params->coordinates_in_date_range;

        $query = Device::query()
            ->leftJoin('device_tariffs','devices.device_tariff_id','=', 'device_tariffs.id')
            ->leftJoin('provider_pos','devices.provider_pos_id','=', 'provider_pos.id')
            ->select('devices.id as dtRowId', 'devices.name as name', 'devices.alias as alias',
               'devices.imei as imei','devices.options as options',
               'device_tariffs.name as tariff_name',
               'provider_pos.name as provider_pos_name');

        if($coordinates_in_date_range && $coordinates_in_date_range[0] && $coordinates_in_date_range[1]){
            $begin_date = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z',
                $coordinates_in_date_range[0]);
            $end_date = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z',
                $coordinates_in_date_range[1]);

            $query->has('coordinates','>','0','and', function ($subQuery) use ($begin_date, $end_date){
                $subQuery->whereBetween('created_at', [$begin_date, $end_date]);
            });
        }

        return $query;
    }
}





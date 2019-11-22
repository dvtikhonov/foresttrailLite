<?php

namespace App\Libraries\Repositories;

use App\Interfaces\Repositories\ProviderPosRepositoryInterface;
use App\Models\Device;
use App\Models\ProviderPos;
use App\Models\TransferDevice;
use Carbon\Carbon;
//use Illuminate\Database\Query\Builder ;
use Illuminate\Database\Eloquent\Model;
use Tests\Unit\ProviderPosRepositoryTest;

class ProviderPosRepository implements ProviderPosRepositoryInterface
{
    private $providerPos ;
    private $device ;
    private $transferDevice ;

    public function __construct(ProviderPos $providerPos = null, Device $device = null, TransferDevice $transferDevice = null)
    {
        $this->providerPos = $providerPos ; // ?: new ProviderPos
        $this->device = $device;
        $this->transferDevice = $transferDevice; //? : new  TransferDevice;
    }

    /**
     * Очистка данных в таблице devices и transfer_devices
     * в исходное состояние для указанных устройств.
     *
     * @param  array  $devices_id
     * @return bool
     */

    public  function clearDevice($devices_id):bool
    {
//        dd(__METHOD__,
//            ['newQuery' =>    method_exists($this->providerPos,'newQuery'),
//            'whereIn' =>    method_exists(Builder::class,'whereIn'),
//            'update' =>    method_exists($this->providerPos,'update'),
//            'whereNull' =>    method_exists(Builder::class,'whereNull')],
//            $devices_id,$this->providerPos,$this->device,$this->transferDevice,'777_clear');

        if (!empty($devices_id)) {
            $dev = $this->device->newQuery()
                ->whereIn('id', $devices_id)
                ->update(['provider_pos_id' => null]);
            $trans = $this->transferDevice->newQuery()
                ->whereIn('device_id', $devices_id)
                ->whereNull('returned_at')
                ->update(['returned_at' => Carbon::now()]);
            if ($dev && $trans){
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * Получить все id устройств точки продаж провайдера
     *
     *
     * @param  int  $id
     * @return array
     */

    public  function getDevicePos($id): array
    {
        $model = $this->providerPos->newQuery()
            ->with('devices')
            ->where(['id'=>$id])
            ->first();
        $devices_id = $model->devices->pluck('id')->toArray();
//        dd(__METHOD__, $devices_id,  $id );

        return $devices_id;
    }

    /**
     * Подговить данные для TreeSelect,
     * устройства только принадлежащие точке продаж.
     *
     *
     * @param  Model  $id
     * @return Model
     */

    public  function initArrayTreeSelect($pos)
    {
//        dd(__METHOD__,$pos,   (new ProviderPosRepositoryTest)->ModelProviderPosWithDevices());
        $devices = $pos->devices;
        $result = $this->initArrayTreeSelectDevices($devices);
        $pos->devices_tmp = $result;
        $pos->devices_id = $devices->pluck('id')->toArray();
        unset($pos['devices']);

        return $pos;
    }

    /**
     * Подговить данные для TreeSelect,
     * по устройствам
     *
     * @param  Model  $id
     * @return Model
     */
    public  function initArrayTreeSelectDevices ($devices)
    {
        $result = collect($devices)->map(function ($item)  { // подготовить данные для  treeSelect
            $returnResult1 = [
                'key' => $item['id'],
                'name' => $item['alias'].'  '. $item['name'] ,
            ];
            return $returnResult1;
        });
        return $result;
    }
    /**
     * Подговить данные для TreeSelect,
     * Точки продаж
     *
     * @param  Model  $id
     * @return Model
     */

    public  function initArrayTreeSelectPos($pos)
    {
        $result1 = collect($pos)->map(function ($item) { // преобразовать в плоский  массив для treeSelect
            $returnResult1 = [
                'key' => $item['id'],
                'name' => $item['name'],
            ];
            return $returnResult1;
        });
        $pos1 = $pos->toArray();

        $pos1['pos_id'] = $pos->pluck('id')->toArray();
        $pos1['pos_tmp'] = $result1;

        return $pos1;
    }

}
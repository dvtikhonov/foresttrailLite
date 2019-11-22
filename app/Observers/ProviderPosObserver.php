<?php

namespace App\Observers;

use App\Interfaces\Repositories\ProviderPosRepositoryInterface;
use App\Models\Device;
use App\Models\ProviderPos;
use App\Models\ProviderPosBlockDate;
use App\Models\TransferDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProviderPosObserver
{
    private $repository;
    private $device ;
    private $transferDevice ;
    private $providerPosBlockDate ;

    public function __construct(
        ProviderPosRepositoryInterface $providerPosRepository = null,
        Device $device = null,
        TransferDevice $transferDevice = null,
        ProviderPosBlockDate $providerPosBlockDate = null
    )
    {
        $this->repository = $providerPosRepository;
        $this->device = $device;
        $this->transferDevice = $transferDevice;
        $this->providerPosBlockDate = $providerPosBlockDate;
    }

    /**
     * Handle the User "updated" event.
     *
     * @param ProviderPos $providerPos
     * @return void
     */
    public function saved(ProviderPos $providerPos)
    {
        $request = request();
        $this->saveDevices($request, $providerPos);
        $this->saveBlockedDates($providerPos);
    }

    public function saving(ProviderPos $providerPos)
    {
        $this->checkBalance($providerPos);
    }

    private function checkBalance(ProviderPos $providerPos)
    {
        $providerPos->is_blocked = !($providerPos->balance >= $providerPos->minimum_balance);
    }

    private function saveBlockedDates(ProviderPos $providerPos)
    {
        $blockDate = $this->providerPosBlockDate->newQuery()
            ->where(['provider_pos_id' => $providerPos->id])
            ->whereNull('restored_at')
            ->first();

        if($providerPos->is_blocked){
            if($blockDate){return;} // запись уже создана
            $this->providerPosBlockDate->insert([
                'provider_pos_id' => $providerPos->id,
                'blocked_at' => Carbon::now()
            ]);
        }else{
            if( ! $blockDate){return;}
            $blockDate->restored_at = Carbon::now();
            $blockDate->save();
        }
    }

    private function saveDevices(Request $request, ProviderPos $providerPos)
    {
        $devicesModels = [];
        $transDevicesModels = [];
        $devices = $providerPos->devices->pluck('id'); // список устройств из базы
        $dell_devices_id = collect($devices)->diff($request->devices_id)->toArray();
        $add_devices_id = collect($request->devices_id)->diff($devices);

        // очистить   список устройств
        $this->repository->clearDevice($dell_devices_id);
//        ProviderPos::clearDevice($dell_devices_id);

        // Дополнить список устройств
        foreach ($add_devices_id as $device_id) {
            $loc_dev = $this->device->find($device_id);
            $loc_dev['provider_pos_id'] = $providerPos->id;
            $devicesModels[] =  $loc_dev;
            $transDevice = [
                'device_id' => $device_id,
                'provider_pos_id' => $providerPos->id,
                'transferred_at' => Carbon::now(),
            ];
            $transDevicesModels[] = $transDevice;
        };

        $providerPos->devices()->saveMany($devicesModels);

        if( empty($transDevicesModels) ){return;}
        $this->transferDevice->insert($transDevicesModels);
    }
}

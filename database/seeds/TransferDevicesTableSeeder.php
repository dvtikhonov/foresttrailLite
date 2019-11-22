<?php

use Illuminate\Database\Seeder;

class TransferDevicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $deviceIds = \App\Models\Device::pluck('id')->toArray();
        foreach ($deviceIds as $deviceId) {
            $devices = factory(\App\Models\TransferDevice::class,1)
                ->make()
                ->each(function ($device) use ($deviceId) {
                $device->device_id = $deviceId;
                $device->provider_pos_id = \App\Models\Device::find($deviceId)->toArray()['provider_pos_id'];
//                // $session->save();
            })
                ->toArray();

            \App\Models\TransferDevice::insert($devices);

        }
//        dd ( $deviceIds);

        //
    }
}

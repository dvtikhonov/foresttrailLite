<?php

use Illuminate\Database\Seeder;

class DevicesTableSeeder extends Seeder
{
/**
 * Run the database seeds.
 *
 * @return void
 */
    public function run()
        {
            // устройства привязаны к точкам продаж
            $providerPosIds = \App\Models\ProviderPos::pluck('id')
                ->toArray();
            foreach ($providerPosIds as $providerPosId) {
                $devices = factory(\App\Models\Device::class, random_int(1,5))
                    ->make()
                    ->each(function ($device) use ($providerPosId) {
                        $device->provider_pos_id = $providerPosId;
                    })->toArray();

                \App\Models\Device::insert($devices);
            }
            // устройства не привязаны к точкам продаж
                $devices = factory(\App\Models\Device::class, random_int(10,20))
                    ->make()->each(function ($device) use ($providerPosId) {
                        $device->provider_pos_id = null;
                    })->toArray();

                \App\Models\Device::insert($devices);
        }
}

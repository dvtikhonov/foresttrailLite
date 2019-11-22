<?php

use Illuminate\Database\Seeder;

class ProviderPosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $providerIds = \App\Models\Provider::pluck('id')->toArray();
        foreach ($providerIds as $providerId) {
            $providerPos = factory(\App\Models\ProviderPos::class, 4)
                ->make()
                ->each(function ($providerPos) use ($providerId) {
                $providerPos->provider_id = $providerId;
            })->toArray();


            \App\Models\ProviderPos::insert($providerPos);
        }

//        dd(__METHOD__, $providerIds);

    }
}

<?php
//namespace database\seeds;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        dump(__METHOD__,  date("Y-m-d H:i:s:u"));

        \Illuminate\Database\Eloquent\Model::unguard();

        $this->call([
            PassportTableSeeder::class,
            UsersTableSeeder::class,
            TrackerSessionsTableSeeder::class,
            ProvidersTableSeeder::class,
            ProviderPosTableSeeder::class,
            DevicesTableSeeder::class,
            TransferDevicesTableSeeder::class,
        ]);

        \Illuminate\Database\Eloquent\Model::reguard();
//        dump(__METHOD__,  date("Y-m-d H:i:s:u"));

    }
}

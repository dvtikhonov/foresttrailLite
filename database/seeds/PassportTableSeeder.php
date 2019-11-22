<?php
//namespace database\seeds;

class PassportTableSeeder extends DatabaseSeeder
{
    public function run() {
//        dd (__METHOD__);

        \Illuminate\Support\Facades\DB::table('oauth_clients')->insert([
            'name' => 'Laravel Personal Access Client',
            'secret' => 'ZIvMLfl3rzcI9F6PS8VAJuslaQbIqyj9nOTDAFYi',
            'redirect' => 'http://localhost',
            'personal_access_client' => 1,
            'password_client' => 0,
            'revoked'=>0,
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s'),
        ]);
        \Illuminate\Support\Facades\DB::table('oauth_clients')->insert([
            'name' => 'Laravel Password Grant Client',
            'secret' => 'R0U36E6KaYJGfMHN4aGdKqOqQJcXoytGK8yMVGOE',
            'redirect' => 'http://localhost',
            'personal_access_client' => 0,
            'password_client' => 1,
            'revoked'=>0,
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s'),
        ]);
        \Illuminate\Support\Facades\DB::table('oauth_personal_access_clients')->insert([
            'client_id' => 1,
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s'),
        ]);

    }

}
<?php
//namespace database\seeds;

/**
 * Created by PhpStorm.
 * User: ddd
 * Date: 25.09.2019
 * Time: 16:08
 */

class TrackerSessionsTableSeeder extends DatabaseSeeder
{
    public function run() {

//        $userIds = factory(\App\User::class, 3)->create()->pluck('id')->toArray();
//        $userIds = DB::table('users')->select()->pluck('id')->toArray();
        $userIds = \App\User::pluck('id')->toArray();

//        dd (__METHOD__, $userIds );
        $sessions = factory(\App\Models\TrackerSession::class, 25)->make()->each(function($session) use ($userIds ) {
            $session->user_id = array_random($userIds);
            // $session->save();
        })->toArray();

//        dd (__METHOD__, $sessions);
        \App\Models\TrackerSession::insert($sessions);

    }
}
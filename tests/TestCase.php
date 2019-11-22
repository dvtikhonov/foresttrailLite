<?php

namespace Tests;

use Illuminate\Database\SQLiteConnection;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\Feature\DatabaseMigrationsAlt;
use Tests\Feature\ProviderPosControllerTest;


abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigrations, DatabaseMigrationsAlt {
        DatabaseMigrationsAlt::runDatabaseMigrations insteadof  DatabaseMigrations ;
    }


    public function setUp()
    {
        parent::setUp(); // 10 секунд на выполнение

        if (ProviderPosControllerTest::$upBefore){
//            dump (ProviderPosControllerTest::$upBefore, '$upBefore is setUp' );
            ProviderPosControllerTest::$upBefore = false;
                Artisan::call('db:seed');
        };

    }
}

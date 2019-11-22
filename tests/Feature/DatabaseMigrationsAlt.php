<?php

namespace Tests\Feature;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabaseState;

trait DatabaseMigrationsAlt
{
    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function runDatabaseMigrations()
    {
//        dump (ProviderPosControllerTest::$upBefore, '$upBefore' );
        if (ProviderPosControllerTest::$upBefore) {

            $this->artisan('migrate:fresh');
//            $this->artisan('migrate:fresh');

            $this->app[Kernel::class]->setArtisan(null);

            $this->beforeApplicationDestroyed(function () {
//                $this->artisan('migrate:rollback');

                RefreshDatabaseState::$migrated = false;
            });
        }
    }
}

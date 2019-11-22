<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginForettrailTest extends DuskTestCase
{
    /**
     * A Dusk test вывод формы авторизации.
     *
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */

    public function testLoginForettrail()
    {

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->visit('app#/')
//                ->screenshot('Exampl_77')
                ->assertSchemeIs('http')
                ->assertPathIs('/app')
//                ->assertUrlIs("http://foresttrail.loc/app#/")
                ->assertHostIs('foresttrail.loc')
//               ->assertVue('email', 'lll@ll')
                ->assertSee('Вход')
                ->assertSee('Авторизация');
        });
    }


    /**
     * A Dusk test не авторизации пользователя.
     *
     * @depends testLoginForettrail
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */

    public function testUnLoginInForettrail()
    {

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->visit('app#/')
                ->type('#inputEmail', '9133310802')
                ->type('#inputPassword', '12345')
                ->press('Вход')
                ->waitForText('Unauthorised')
//                ->screenshot('Exampl_Login_in')
                ->assertSee('Unauthorised')
            ;
        });
    }

    /**
     * A Dusk test авторизации пользователя.
     *
     * @depends testUnLoginInForettrail
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */

    public function testLoginInForettrail()
    {

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->visit('app#/')
                ->type('#inputEmail', '9133310802')
                ->type('#inputPassword', '12345678')
                ->press('Вход')
                ->waitForText('Admin')
//                ->screenshot('Exampl_Login_in')
                ->assertSee('Admin')
            ;
        });
    }


}

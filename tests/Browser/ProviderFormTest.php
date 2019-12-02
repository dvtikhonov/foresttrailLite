<?php

namespace Tests\Browser;

use App\Models\Device;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\Browser\Pages\Provider\Form;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProviderFormTest extends DuskTestCase
{
    /**
     * A Dusk test формы  Провайдера.
     *
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testEditFormProvider()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/')
                ->visit('app#/')
                ->type('#inputEmail', '9133310802')
                ->type('#inputPassword', '12345678')
                ->press('Вход')
//                ->pause(5000)
                ->waitForText('Admin',10)
                ->visit(new Form(2))
                ->checkUpdateProviderInfo()
                ->closeWinSaveText()
                ->buttonMenu()
                ->screenshot('Exampl_Login_in18')
            ;
        });
    }
}



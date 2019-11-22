<?php

namespace Tests\Browser;

use App\Models\Device;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\Browser\Pages\Provider\Form;
use Tests\Browser\Pages\Provider\ProviderPosList;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProviderPosListTest extends DuskTestCase
{
    /**
     * A Dusk test списка Точек продаж.
     *
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testListProviderPos()
    {

        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/')
                ->visit('app#/')
                ->type('#inputEmail', '9133310802')
                ->type('#inputPassword', '12345678')
                ->press('Вход')
//                ->pause(5000)
                ->waitForText('Admin')
                ->visit(new ProviderPosList($browser, 2))
                ->deletTestDevice('3568680000414112010')
                ->deletTestDevice('3568680000414112011')
                ->initDeviceProviderPos()
                ->createPos()

//                ->clickLink('Отмена')
//                ->waitForText('Провайдер id: 2')
                ->checkCreateProviderPos()

                ->findProviderPos('ИП Рогожкин точка продаж №1')
                ->checkUpdateProviderPos()

                ->checkDeviceProviderPos('TESTST910', 'GSM gps трекер ST-910')
//                ->pause(500)
                ->checkDeviceProviderPos('TESTST911', 'GSM gps трекер ST-911')
                ->findProviderPos('ИП Рогожкин точка продаж №1')

                ->checkDeleteProviderPos('ИП Рогожкин точка продаж №1')
                ->deletTestDevice('3568680000414112010')
                ->deletTestDevice('3568680000414112011')
                ->screenshot('Exampl_Login_in181')
            ;
        });
    }
}



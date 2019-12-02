<?php

namespace Tests\Browser;

use App\Models\Device;
use App\User;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\Browser\Pages\Provider\Form;
use Tests\Browser\Components\FindVueTable;
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
                ->deletPos()
                ->deletTestDevice('3568680000414112010')
                ->deletTestDevice('3568680000414112011')
                ->initDeviceProviderPos()
                ->createPos()

                ->checkCreateProviderPos()
                ->screenshot('Exampl_Login_in187_0')

                ->clickLink('Точки продаж')
                ->waitForText('записей',10)
                ->within(new FindVueTable('@point-of-sales-component'), function ($browser) {
                    $browser->findVueTable('ИП Рогожкин точка продаж №1');
                })
//                ->findVueTable1('ИП Рогожкин точка продаж №1','Точки продаж')
                ->waitUntilMissing('@LoaderRed') // есть еще div.overlay-loader
                ->waitForText('записей',10)
                ->assertSee('881212312491')
                ->assertSee('Иван Кузьмич')
                ->assertSee('308434, Мурманская область, город Сергиев Посад, наб. Ломоносова, 28а')


                ->checkUpdateProviderPos()

                ->checkDeviceProviderPos('TESTST910', 'GSM gps трекер ST-910')
//                ->pause(500)
                ->checkDeviceProviderPos('TESTST911', 'GSM gps трекер ST-911')


                ->clickLink('Точки продаж')
                ->waitForText('записей',10)
                ->within(new FindVueTable('@point-of-sales-component'), function ($browser) {
                    $browser->findVueTable('ИП Рогожкин точка продаж №1','Точки продаж');
                })
                ->waitUntilMissing('@LoaderRed') // есть еще div.overlay-loader
                ->waitForText('записей',10)
//                ->findVueTable1('ИП Рогожкин точка продаж №1','Точки продаж')
//
                ->checkDeleteProviderPos('ИП Рогожкин точка продаж №1')
                ->deletTestDevice('3568680000414112010')
                ->deletTestDevice('3568680000414112011')
                ->screenshot('Exampl_Login_in181')
            ;
        });
    }
}



<?php

namespace Tests\Browser;

use Tests\Browser\Components\FindVueTable;
use Tests\Browser\Pages\Provider\ProviderPosListPayment;
use Tests\Browser\Pages\Provider\ProviderPosList;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ProviderPosPaymentTest extends DuskTestCase
{
    /**
     * A Dusk test списка начислений и платежей Точек продаж.
     *
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testListProviderPosPayment()
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
                ->visit(new ProviderPosListPayment($browser, 2))
                ->on(new ProviderPosList($browser, 2))
                ->deletPos()
                ->createPos()
                ->checkCreateProviderPos()
                ->on( new ProviderPosListPayment($browser, 2))
                ->checkCreateProviderPosPayment()
                ->createProviderPosPayment("ИП Рогожкин точка продаж №1",'Поступление','120')
                ->createProviderPosPayment("ИП Рогожкин точка продаж №1",'Списание','15')
                ->clickLink('Редактирование')
                ->clickLink('Взаиморасчеты')

                ->waitForText('записей',10)
                ->within(new FindVueTable('@provider-pos-payment-component'), function ($browser) {
                    $browser->selectVueTable('ИП Рогожкин точка продаж №1');
                })
                ->waitUntilMissing('@LoaderRed') // есть еще div.overlay-loader
                ->waitForText('записей',10)
                ->assertSee('105 руб')
                ->assertSee('Test for summa=15, Списание')
                ->assertSee('Test for summa=120, Поступление')

                ->deleteProviderPosPayment('Списание', 'ИП Рогожкин точка продаж №1')
                ->screenshot('Exampl_Login_in195')
                ->waitForText('Test for summa=120, Поступление',10)
                ->deleteProviderPosPayment('Поступление', 'ИП Рогожкин точка продаж №1')
                ->screenshot('Exampl_Login_in197')
                ->waitForText('Нет данных',10)

                ->on(new ProviderPosList($browser, 2))
                ->within(new FindVueTable('@point-of-sales-component'), function ($browser) {
                    $browser->findVueTable('ИП Рогожкин точка продаж №1','Точки продаж');
                })
                ->waitUntilMissing('@LoaderRed') // есть еще div.overlay-loader
                ->waitForText('записей',10)
                ->checkDeleteProviderPos('ИП Рогожкин точка продаж №1')
                ->screenshot('Exampl_Login_in200')
            ;
        });
    }
}



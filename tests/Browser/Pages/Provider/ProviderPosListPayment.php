<?php

namespace Tests\Browser\Pages\Provider;

use Tests\Browser\Components\FindVueTable;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class ProviderPosListPayment extends Page
{
    protected $user_id;
    protected $browser;

    public function __construct(Browser $browser, $user_id){
        $this->browser = $browser;
        $this->user_id = $user_id;
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/app#/manager/providers/edit/'.$this->user_id;
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser $browser
     * @return void
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathBeginsWith('/app')
            ->waitForText('Отмена')
            ->clickLink('Взаиморасчеты')
            ->waitForText('Создать',10)
            ->pause(1250)
            ->assertDontSee('Запись сохраненна')
            ->assertDontSee('Ошибка')
            ->assertSee('Создать')
            ->assertSeeIn('@tablistActive', 'Редактирование')
            ->assertSeeIn('@tabs-general', 'Основное')
            ->assertSeeIn('@tabs-point-of-sales', 'Точки продаж')
            ->assertSeeIn('@tabs-provider-pos-payment', 'Взаиморасчеты')
            ->assertVue('fields.id', $this->user_id, '@general-component')
            ->assertVue('breadcrumbList[0].text', 'Провайдеры', '@breadcrumb-component')
            ->assertVue('breadcrumbList[1].text', 'Редактирование', '@breadcrumb-component')
        ;
    }
    /**
     *  проверка создания  начислений/ платежей Точки продаж.
     *
     * @param  Browser $browser
     * @return void
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function checkCreateProviderPosPayment(Browser $browser)
    {
        $browser
//            ->pause(1250)
            ->waitForText('Создать',10)
            ->press('Создать')

            ->waitForText('Создать операцию')
            ->pause(700)
            ->press('Сохранить')
            ->waitForText('Ошибка - Request failed with status code 400')
            ->assertSee('Ошибка - Request failed with status code 400')

            ->click('@SelectOperation')
            ->type('@SelectInputOperation', 'Поступление')
            ->waitForText('Поступление')
            ->keys('@SelectInputOperation', '{arrow_down}', '{arrow_down}' ,'{enter}')//'{enter}',  '{arrow_down}','{enter}'
            ->click('@SelectOperation')
            ->press('Сохранить')
            ->waitForText('Ошибка - Request failed with status code 400')

            ->clickLink('Редактирование')
            ->clickLink('Взаиморасчеты')
            ->waitForText('Создать',10)
            ->press('Создать')
            ->waitForText('Создать операцию')

            ->click('@Select')
            ->pause(300)
//            ->screenshot('Exampl_Login_in199')
            ->type('@SelectInputPOS', 'ИП Рог')
//            ->pause(2000)
//            ->screenshot('Exampl_Login_in199_1')

            ->waitForText('ИП Рогожкин точка продаж №1',10)
//            ->keys('@SelectInputPOS', '{arrow_down}', '{arrow_down}')//'{enter}',  '{arrow_down}','{enter}'
            ->keys('@SelectInputPOS', '{enter}')//'{enter}',  '{arrow_down}','{enter}'
            ->click('@Select')
            ->press('Сохранить')
            ->waitForText('Ошибка - Request failed with status code 400')
            ->assertSee('Ошибка - Request failed with status code 400')
        ;
    }
    /**
     *  создания  начислений/ платежей Точки продаж.
     *
     * @param  Browser $browser
     * @return void
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function createProviderPosPayment(Browser $browser, $pos=null, $operation=null , $summa=null)
    {
        $browser
            ->clickLink('Редактирование')
            ->clickLink('Взаиморасчеты')
            ->waitForText('Создать',10)
            ->press('Создать')
            ->waitForText('Создать операцию')

            ->click('@Select')
            ->pause(700)
            ->screenshot('Exampl_Login_in199')
            ->type('@SelectInputPOS', mb_substr($pos, 0, 6))
            ->waitForText($pos,15)


//            ->keys('@SelectInputPOS', '{arrow_down}', '{arrow_down}')//'{enter}',  '{arrow_down}','{enter}'
            ->keys('@SelectInputPOS', '{enter}')//'{enter}',  '{arrow_down}','{enter}'
            ->click('@Select')
            ->click('@SelectOperation')
            ->type('@SelectInputOperation', mb_substr($operation, 0, 6) )
//            ->pause(2000)
//            ->screenshot('Exampl_Login_in199_7')
            ->waitForText($operation)
//            ->keys('@SelectInputOperation', '{arrow_down}', '{arrow_down}' ,'{enter}')//'{enter}',  '{arrow_down}','{enter}'
            ->keys('@SelectInputOperation' ,'{enter}')
            ->click('@SelectOperation')
            ->type('@Summa',  $summa)
            ->type('@PaymentOption', 'Test for summa='. $summa.', '.$operation)
            ->press('Сохранить')
            ->waitForText('Запись сохраненна')
            ->assertSee('Запись сохраненна')
        ;
    }
    /**
     *  проверка удаления плптежей/начислений  Точки продаж.
     *
     * @param  Browser $browser
     * @param  $pos
     * @return void
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */

    public function checkDeleteProviderPosPayment(Browser $browser, $pos)

    {
        $browser
            ->clickLink('', '@Trash')// пиктограмма мусорка, текста ссылки нет
            ->waitForText('Подтвердите')
            ->assertSee('Удалить запись?')
            ->press('Отмена')
            ->waitForText($pos)
            ->assertDontSee('Удалить запись')
            ->clickLink('', '@Trash')// пиктограмма мусорка, текста ссылки нет
            ->waitForText('Подтвердите')
            ->assertSee('Удалить запись?')
            ->press('Да')
            ->pause(1700)
            ->waitUntilMissing('@LoaderRed') // есть еще div.overlay-loader
            ->waitForText('№')
            ->assertSee('Нет данных')
        ;
    }

    /**
     * @param Browser $browser
     * @param $pos
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function deleteProviderPosPayment(Browser $browser,$operation = null, $title=null)

    {
        $browser
        ->within(new FindVueTable('@provider-pos-payment-component'), function ($browser) use($operation) {
        $browser->findVueTable($operation);
    })
        ->waitUntilMissing('@LoaderRed') // есть еще div.overlay-loader
        ->waitForText('записей',10)

        ->checkDeleteProviderPosPayment($title)
//        ->screenshot('Exampl_Login_in198')

        ->within(new FindVueTable('@provider-pos-payment-component'), function ($browser) {
            $browser->findVueTable(' ');
        })
//            ->screenshot('Exampl_Login_in199')
        ->waitUntilMissing('@LoaderRed') // есть еще div.overlay-loader
        ;
    }


    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@Pensil' => '.button:has(.fa-pencil-alt)', // пиктограмма карандаш, текста ссылки нет
            '@Trash' => '.button:has(.fa-trash-alt)', // пиктограмма мусорка, текста ссылки нет
            '@VueTableTrash' => '.fa-trash-alt', // пиктограмма мусорка, текста ссылки нет
            '@Loader' => 'div.loading-background',
            '@LoaderRed' => 'div.overlay-loader',
            '@DeviceSelect' => 'form > div:nth-child(1) > div:nth-child(2) > div > div.vue-treeselect__menu-container > div label',// можно в консоли выполнить temp1.openMenu(), где temp1 глобально сохраненный контекст области openMenu
            '@SelrctST910' => 'form .select-device .vue-treeselect__list .vue-treeselect__option:not(.vue-treeselect__option--hide):last',// можно в консоли выполнить temp1.openMenu(), где temp1 глобально сохраненный контекст области openMenu
            '@SelrctST911' => 'form .select-device .vue-treeselect__list .vue-treeselect__option:not(.vue-treeselect__option--hide):first',
            '@SelectInput' => 'form > div:nth-child(1) > div:nth-child(2) > div > div.vue-treeselect__control > div.vue-treeselect__value-container > div > div.vue-treeselect__input-container > input ', // input.vue-treeselect__input
            '@SelectInputPOS' => 'form > div:nth-child(1) > div:nth-child(2) > div > div.vue-treeselect__control > div.vue-treeselect__value-container > div.vue-treeselect__input-container > input',
            '@SelectInputOperation' => 'form > div:nth-child(1) > div:nth-child(1) > div > div.vue-treeselect__control > div.vue-treeselect__value-container > div.vue-treeselect__input-container > input',
            '@Select' => 'form > div:nth-child(1) > div:nth-child(2) > div > div.vue-treeselect__control > div.vue-treeselect__control-arrow-container',
            '@VueTableSelectInput' => 'div.vue-treeselect__control  div.vue-treeselect__value-container  div.vue-treeselect__input-container > input ',
            '@VueTableSelectX' => 'div.vue-treeselect__control  div.vue-treeselect__x-container',
            '@VueTableSelect' => 'div.vue-treeselect__control > div.vue-treeselect__control-arrow-container',
            '@SelectOperation' => 'form > div:nth-child(1) > div:nth-child(1) > div > div.vue-treeselect__control > div.vue-treeselect__control-arrow-container',
            '@MinBalans' => 'form > div:nth-child(4) > div:nth-child(1) > div > input',
            '@Phone' => 'form > div:nth-child(3) > div:nth-child(2) > div > input',
            '@Contacts' => 'form > div:nth-child(3) > div:nth-child(1) > div > input',
            '@Address' => 'form > div.form-group > div > input',
            '@Name' => 'form > div:nth-child(1) > div:nth-child(1) > div> input',
            '@Summa' => 'form > div:nth-child(2) > div:nth-child(1) > div input',
            '@PaymentOption' => 'form > div:nth-child(2) > div:nth-child(2) > div input',
            '@tablistActive' => '#app > div > ol > li.breadcrumb-item.active > span',
            '@tabs-general' => 'div.dusk-tabs>div>div>ul> li:nth-child(1) >a',
            '@tabs-point-of-sales' => 'div.dusk-tabs>div>div>ul> li:nth-child(2) >a',
            '@tabs-provider-pos-payment' => 'div.dusk-tabs>div>div>ul> li:nth-child(3) >a',
            '@provider-id' => 'div.dusk-tabs>div>div>div>div>div>div> h3',
        ];
    }

}

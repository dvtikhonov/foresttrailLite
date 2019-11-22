<?php

namespace Tests\Browser\Pages\Provider;

use App\Models\ProviderPos;
use Tests\Browser\DeviceListTest;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class ProviderPosList extends Page

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
            ->clickLink('Точки продаж')
            ->waitForText('записей',10)
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
     *  проверка открытия формы  Точки продаж.
     *
     * @param  Browser $browser
     * @return void
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function createPos(Browser $browser)
    {
        $browser
            ->visit('/app#/manager/providers/edit/'.$this->user_id)
            ->waitUntilMissing('@Loader') // есть еще div.overlay-loader
            ->clickLink('Точки продаж')
            ->waitForText('записей')

            ->press('Создать') //
            ->waitForText('точка POS',10)
            ->assertDontSee('Ошибка')
            ->assertDontSee('Запись сохраненна')
        ;
    }

    /**
     *  проверка создания  Точки продаж.
     *
     * @param  Browser $browser
     * @return void
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function checkCreateProviderPos(Browser $browser)
    {
        $browser
//            ->pause(250)
            ->press('Сохранить')
            ->waitForText('Отмена')
            ->pause(700)
            ->assertSee('The name field is required')
            ->assertSee('The address field is required')
            ->assertSee('The contacts field is required')
            ->assertSee('The phone field is required')
            ->type('@Name', 'ИП Рогожкин точка продаж №1')
            ->type('@Address', '308434, Мурманская область, город Сергиев Посад, наб. Ломоносова, 28а')
            ->type('@Contacts', 'Иван Кузьмич')
            ->type('@Phone', '881212312491')
            ->type('@MinBalans', '25')
            ->press('Сохранить')
            ->waitForText('Запись сохраненна')
            ->clickLink('Отмена')
            ->waitForText('Провайдер id: '.$this->user_id)
            ->assertDontSee('Ошибка')
            ->assertDontSee('Запись сохраненна')
        ;
    }

    /**
     *  проверка редактирования  Точки продаж.
     *
     * @param  Browser $browser
     * @return void
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */

    public function checkUpdateProviderPos(Browser $browser)

    {
        $browser
            ->clickLink('', '.button:has(.fa-pencil-alt)') // пиктограмма карандаш, текста ссылки нет
            ->waitForText('точка POS')
            ->pause(500)
            ->waitUntilMissing('@Loader') // есть еще div.overlay-loader
            ->assertDontSee('Ошибка')
            ->assertDontSee('Запись сохраненна')
            ->assertValue('@Name',  'ИП Рогожкин точка продаж №1')
            ->assertValue( '@Address','308434, Мурманская область, город Сергиев Посад, наб. Ломоносова, 28а')
            ->assertValue( '@Contacts','Иван Кузьмич')
            ->assertValue( '@Phone','881212312491')
            ->assertValue( '@MinBalans','25')

            ->type('@Name', ' ')
            ->type('@Address', ' ')
            ->type('@Contacts', ' ')
            ->type('@Phone', ' ')
            ->press('Сохранить')
            ->waitUntilMissing('@Loader') // есть еще div.overlay-loader
//            ->pause(250)
            ->waitForText('The name field is required')
            ->assertSee('The name field is required')
            ->assertSee('The address field is required')
            ->assertSee('The contacts field is required')
            ->assertSee('The phone field is required')
            ->type('@Name', 'ИП Рогожкин точка продаж №1')
            ->type('@Address', '308434, Мурманская область, город Сергиев Посад, наб. Ломоносова, 28а')
            ->type('@Contacts', 'Иван Кузьмич')
            ->type('@Phone', '881212312491')
            ->type('@MinBalans', '25')
            ->press('Сохранить')
            ->waitForText('Запись сохраненна')
            ->assertDontSee('TESTST911')
            ->click('@Select')
            ->type('@SelectInput', 'TEST91')
            ->waitForText('TESTST91')
            ->keys('@SelectInput', '{enter}',  '{arrow_down}','{enter}')
//            ->clickLink('', '@SelrctST911')
//            ->click('@SelrctST910')
            ->click('@Select')
            ->press('Сохранить')
            ->waitForText('Запись сохраненна')
//            ->clickLink('Отмена')
            ->clickLink('Редактирование')
            ->waitForText('Провайдер id: '.$this->user_id)
            ->assertDontSee('Ошибка')
            ->assertDontSee('Запись сохраненна')
        ;
    }

    /**
     *  Инициалтзация устройств для тестов  Точки продаж.
     *
     * @param  Browser $browser
     * @param  $device
     * @param  $deviceName
     * @return void
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function initDeviceProviderPos(Browser $browser)
    {
        $browser
            ->waitForText('Меню')
            ->waitUntilMissing('@Loader') // есть еще div.overlay-loader

            // div class="loading-background    loading-icon    body > div > div.loading-icon
            ->press('Меню')
            ->waitForLink('Устройства')
            ->clickLink('Устройства')
            ->waitUntilMissing('@LoaderRed') // есть еще div.overlay-loader
            ->waitForText('IMEI')
            ->type('div.search-input input.input', 'TESTST91')
            ->pause(250)
//            ->waitFor('#id > tbody > tr:nth-child(1) > td:nth-child(3) > span > span')
//            ->pause(1500)
            ->waitUntilMissing('@LoaderRed') // есть еще div.overlay-loader
            ->assertSee('Нет данных')
            ->press('Создать') //

            ->waitForText('Код IMEI')
            ->assertVue('breadcrumbList[0].text', 'Устройства', '@breadcrumb-component')
            ->assertVue('breadcrumbList[1].text', 'Редактирование', '@breadcrumb-component')
            ->assertDontSee('Запись сохраненна')
            ->type('form > div:nth-child(1) > div > div > input', 'GSM gps трекер ST-910')
            ->type('form > div:nth-child(2) > div:nth-child(1) > div > input', 'TESTST910S')
            ->type('form > div:nth-child(2) > div:nth-child(2) > div > input', 'ТЕСТ.Подлежит удалению. нет Опций')
            ->type('form > div:nth-child(3) > div:nth-child(1) > div > input', '3568680000414112010')
            ->press('Сохранить')
            ->waitForText('Запись сохраненна')
            ->clickLink('Устройства')
            ->waitForText('IMEI')
            ->pause(500)
            ->waitUntilMissing('@LoaderRed') // есть еще div.overlay-loader
            ->press('Создать') //
            ->waitForText('Код IMEI')

            ->type('form > div:nth-child(1) > div > div > input', 'GSM gps трекер ST-911')
            ->type('form > div:nth-child(2) > div:nth-child(1) > div > input', 'TESTST911S')
            ->type('form > div:nth-child(2) > div:nth-child(2) > div > input', 'ТЕСТ.Подлежит удалению. нет Опций')
            ->type('form > div:nth-child(3) > div:nth-child(1) > div > input', '3568680000414112011')
            ->press('Сохранить')
            ->waitForText('Запись сохраненна')

        ;
    }

    /**
     *  Удаление устройств из базы.
     *
     * @param  Browser $browser
     * @param  $imei
     * @return void
     */
    public function deletTestDevice(Browser $browser,$imei=null )
    {
        $pos = ProviderPos::query()
            ->where('provider_id', 2)
            ->where('name', 'ИП Рогожкин точка продаж №1')
            ->where('contacts', 'Иван Кузьмич')
            ->where('address', '308434, Мурманская область, город Сергиев Посад, наб. Ломоносова, 28а')
            ->where('phone', '881212312491')
            ->where('minimum_balance', '25')
            ->first();
        if (!empty($pos)) {
            $pos = ProviderPos::query()
                ->where('provider_id', 2)
                ->where('name', 'ИП Рогожкин точка продаж №1')
                ->where('contacts', 'Иван Кузьмич')
                ->where('address', '308434, Мурманская область, город Сергиев Посад, наб. Ломоносова, 28а')
                ->where('phone', '881212312491')
                ->where('minimum_balance', '25')
                ->update(['deleted_at' => date('Y-m-d H:i:s')]);
            dump('Pos - '.$pos.'rows updated' );
        }
        return DeviceListTest::trashedDeleteDevice($imei);

}
    /**
     *  проверка кнопки Точки продаж на странице Устройства.
     *
     * @param  Browser $browser
     * @param  $device
     * @param  $deviceName
     * @return void
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function checkDeviceProviderPos(Browser $browser, $device=null, $deviceName=null)
    {
        $browser
            ->waitForText('Меню')
            ->waitUntilMissing('@Loader') // есть еще div.overlay-loader
            // div class="loading-background    loading-icon    body > div > div.loading-icon
            ->press('Меню')
            ->waitForLink('Устройства')
            ->clickLink('Устройства')
            ->waitForText('IMEI')
            ->type('div.search-input input.input', $device)
//            ->pause(250)
//            ->waitFor('#id > tbody > tr:nth-child(1) > td:nth-child(3) > span > span')
//            ->pause(1500)
            ->waitForText($deviceName)
//            ->assertSeeIn('#id > tbody > tr:nth-child(1) > td:nth-child(3) > span > span',$device)
            ->clickLink('', '.button:has(.fa-pencil-alt)') // пиктограмма карандаш, текста ссылки нет
            ->waitForText('Перейти на точку продаж')
            ->press('Перейти на точку продаж id')
            ->waitForText($device)
            ->assertSee($device)
            ->clickLink('Редактирование')
            ->waitForText('Провайдер id: '.$this->user_id)
        ;
}

    /**
     *  проверка удаления Точки продаж.
     *
     * @param  Browser $browser
     * @param  $pos
     * @return void
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */

    public function checkDeleteProviderPos(Browser $browser, $pos)

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
            ->pause(1000)
            ->waitUntilMissing('@LoaderRed') // есть еще div.overlay-loader
            ->waitForText('ID')
            ->assertSee('Нет данных')
        ;
}

    /**
     *  поиск  Точки продаж.
     *
     * @param  Browser $browser
     * @param $find
     * @return void
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function findProviderPos(Browser $browser, $find =null)

    {
        $browser
            ->clickLink('Точки продаж')
            ->waitForText('ID')
            ->type('div.search-input input.input', $find)
             ->pause(250)
            ->waitForText('Телефон')
            ->assertSee('881212312491')
            ->assertSee('Иван Кузьмич')
            ->assertSee('308434, Мурманская область, город Сергиев Посад, наб. Ломоносова, 28а')
        ;
    }

//   Неудачный эксперемент по TreeSelect
//->click('@Select')
//->type('@SelectInput', 'TEST910')
//->waitForText('TESTST910')
////            ->clickLink('', '@SelrctST911')
//->findElement('@SelrctST910')
//->screenshot('Exampl_Login_in180')
//->type('@SelectInput', 'TEST911')
//->waitForText('TESTST911')
//->findElement('@SelrctST911')
//->pause(3000)


    public function findElement(Browser $browser, $alias)
    {
        $selector = $this->elements()[$alias];
        $browser->ensurejQueryIsAvailable();
        $this->ensurejQueryGetSelector($browser->driver);
        $selector = $browser->driver->executeScript("return jQuery('".$selector."').getSelector();");
        $browser->click($selector);
        return $browser ;
    }

    public function ensurejQueryGetSelector($driver)
    {
        if ($driver->executeScript('return window.jQuery !== null && !window.jQuery().getSelector')) {
            $driver->executeScript(file_get_contents(__DIR__.'/../../js/getSelector.js'));
        }
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
            '@Loader' => 'div.loading-background',
            '@LoaderRed' => 'div.overlay-loader',
            '@DeviceSelect' => 'form > div:nth-child(1) > div:nth-child(2) > div > div.vue-treeselect__menu-container > div label',// можно в консоли выполнить temp1.openMenu(), где temp1 глобально сохраненный контекст области openMenu
            '@SelrctST910' => 'form .select-device .vue-treeselect__list .vue-treeselect__option:not(.vue-treeselect__option--hide):last',// можно в консоли выполнить temp1.openMenu(), где temp1 глобально сохраненный контекст области openMenu
            '@SelrctST911' => 'form .select-device .vue-treeselect__list .vue-treeselect__option:not(.vue-treeselect__option--hide):first',
//            '@SelrctST910' => 'form > div:nth-child(1) > div:nth-child(2) > div > div.vue-treeselect__menu-container > div > div > div:nth-child(1) > div > div > label',
            '@SelectInput' => 'form > div:nth-child(1) > div:nth-child(2) > div > div.vue-treeselect__control > div.vue-treeselect__value-container > div > div.vue-treeselect__input-container > input ', // input.vue-treeselect__input
            '@Select' => 'form > div:nth-child(1) > div:nth-child(2) > div > div.vue-treeselect__control > div.vue-treeselect__control-arrow-container',
            '@MinBalans' => 'form > div:nth-child(4) > div:nth-child(1) > div > input',
            '@Phone' => 'form > div:nth-child(3) > div:nth-child(2) > div > input',
            '@Contacts' => 'form > div:nth-child(3) > div:nth-child(1) > div > input',
            '@Address' => 'form > div.form-group > div > input',
            '@Name' => 'form > div:nth-child(1) > div:nth-child(1) > div> input',
            '@tablistActive' => '#app > div > ol > li.breadcrumb-item.active > span',
            '@tabs-general' => 'div.dusk-tabs>div>div>ul> li:nth-child(1) >a',
            '@tabs-point-of-sales' => 'div.dusk-tabs>div>div>ul> li:nth-child(2) >a',
            '@tabs-provider-pos-payment' => 'div.dusk-tabs>div>div>ul> li:nth-child(3) >a',
            '@provider-id' => 'div.dusk-tabs>div>div>div>div>div>div> h3',
        ];

    }
}

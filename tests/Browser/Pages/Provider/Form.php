<?php

namespace Tests\Browser\Pages\Provider;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class Form extends Page
{
    protected $user_id;

     public function __construct($user_id){
         $this->user_id = $user_id;
     }

    /**
     * Get the URL for the page.
     * Form Provider
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
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathBeginsWith('/app')
            ->waitForText('Отмена')
            ->pause(1250)
            ->assertDontSee('Запись сохраненна')
            ->assertDontSee('Ошибка')
            ->assertSeeIn('@tablistActive', 'Редактирование')
            ->assertSeeIn('@tabs-general', 'Основное')
            ->assertSeeIn('@tabs-point-of-sales', 'Точки продаж')
            ->assertSeeIn('@tabs-provider-pos-payment', 'Взаиморасчеты')
            ->assertSeeIn('@provider-id', 'Провайдер id: '.$this->user_id)
            ->assertVue('fields.id', $this->user_id, '@general-component')
            ->assertVue('breadcrumbList[0].text', 'Провайдеры', '@breadcrumb-component')
            ->assertVue('breadcrumbList[1].text', 'Редактирование', '@breadcrumb-component')
            ;
    }
    public function fillProviderInfo(Browser $browser)
    {
        $browser
            ->type('@Name', 'ИП Рогожкин')
//            ->clear('@Address')
            ->type('@Address', '308434, Мурманская область, город Сергиев Посад, наб. Ломоносова, 69')
            ->type('@Contacts', 'Василий Кузьмич')
            ->type('@Phone', '881212312490')
            ->type('@Login', '9133310802LL')
            ->type('@Password', '12345678');
    }

    public function closeWinSaveText(Browser $browser)
    {
        $browser
            ->press('×')
            ->waitForText('Admin')
            ->assertDontSee('Запись сохраненна')
        ;
    }
    public function buttonMenu(Browser $browser)
    {
        $browser
            ->press('Меню')
            ->waitForLink('Провайдеры')
            ->waitForLink('Устройства')
            ->waitForLink('Тарифы')
            ->press('Меню')
//            ->waitForText('Admin')
            ->assertDontSee('Устройства')
            ->assertDontSee('Тарифы')
        ;
    }

    /**
     * @param Browser $browser
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function checkUpdateProviderInfo(Browser $browser)

    {
        $browser
            ->waitUntilMissing('@Loader')
            ->type('@Password', '')
            ->assertDontSee('Запись сохраненна')
            ->press('Сохранить')
            ->waitForText('Ошибка - Request failed with status code 400')
            ->assertDontSee('The name field is required')
            ->assertDontSee('The address field is required')
            ->assertDontSee('The contacts field is required')
            ->assertDontSee('The phone field is required')
            ->screenshot('Exampl_Login_in181')
            ->type('@Password', '12345678')
            ->press('Сохранить')
            ->waitForText('Ошибка - Request failed with status code 400',10)
            ->type('@Login', '913331080277')
            ->press('Сохранить')
            ->waitForText('Запись сохраненна',10)
            ->type('@Name', ' ')
            ->press('Сохранить')
            ->waitForText('The name field is required')
            ->assertDontSee('The address field is required')
            ->assertDontSee('The contacts field is required')
            ->assertDontSee('The phone field is required')
            ->type('@Name', 'ИП Рогожкин')
            ->press('Сохранить')
            ->waitForText('Запись сохраненна')

            ->type('@Address', ' ')
//            ->assertDontSee('Запись сохраненна')
            ->press('Сохранить')
            ->waitForText('The address field is required.')
            ->assertDontSee('The name field is required')
            ->assertDontSee('The contacts field is required')
            ->assertDontSee('The phone field is required')
            ->type('@Address', '308434, Мурманская область, город Сергиев Посад, наб. Ломоносова, 69')
            ->press('Сохранить')
            ->waitForText('Запись сохраненна')

            ->type('@Contacts', ' ')
            ->press('Сохранить')
            ->waitForText('The contacts field is required.')
            ->assertDontSee('The name field is required')
            ->assertDontSee('The address field is required')
            ->assertDontSee('The phone field is required')
//            ->screenshot('Exampl_Login_in181')
            ->type('@Contacts', 'Василий Кузьмич')
            ->press('Сохранить')
            ->waitForText('Запись сохраненна')

            ->type('@Phone', ' ')
//            ->assertDontSee('Запись сохраненна')
            ->press('Сохранить')
            ->waitForText('The phone field is required.')
            ->assertDontSee('The name field is required')
            ->assertDontSee('The address field is required')
            ->assertDontSee('The contacts field is required')
            ->type('@Phone', '881212312490')
            ->press('Сохранить')
            ->waitForText('Запись сохраненна')
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
            '@Loader'  => 'div.loading-background',
            '@Password' => 'form > div:nth-child(4) > div:nth-child(2) > div > input',
            '@Login' => 'form > div:nth-child(4) > div:nth-child(1) > div > input',
            '@Phone' => 'form > div:nth-child(3) > div:nth-child(2) > div > input',
            '@Contacts' => 'form > div:nth-child(3) > div:nth-child(1) > div > input',
            '@Address' => 'form > div.form-group > div > input',
            '@Name' => 'form > div:nth-child(1) > div > div > input',
            '@tablistActive' => '#app > div > ol > li.breadcrumb-item.active > span',
//            '@hh' => '#app> div>div>div>div>div>ul> li:nth-child(2)',
            '@tabs-general' => 'div.dusk-tabs>div>div>ul> li:nth-child(1) >a',
            '@tabs-point-of-sales' => 'div.dusk-tabs>div>div>ul> li:nth-child(2) >a',
            '@tabs-provider-pos-payment' => 'div.dusk-tabs>div>div>ul> li:nth-child(3) >a',
            '@provider-id' => 'div.dusk-tabs>div>div>div>div>div>div> h3',
        ];
    }
}

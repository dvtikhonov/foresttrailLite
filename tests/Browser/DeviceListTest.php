<?php

namespace Tests\Browser;

use App\Models\Device;
use App\Models\TransferDevice;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DeviceListTest extends DuskTestCase
{
    /**
     * A Dusk test формы листа всех устройств.
     *
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testDeviceList()
    {

        $this->browse(function (Browser $browser) {
            $browser
//                ->loginAs(User::find(1))  // Роман, Id=1, role=admin
//                ->pause(5000)
//                ->screenshot('Exampl_Login_in8')
//                ->assertAuthenticatedAs(User::find(1))
                ->visit('/')
                ->visit('app#/')
                ->type('#inputEmail', '9133310802')
                ->type('#inputPassword', '12345678')
                ->press('Вход')
                ->visit('app#/manager/devices/')
                ->waitForText('Admin')
                ->waitUntilMissing('div.loading-background')
                ->waitForText('IMEI')
                ->waitFor('li.breadcrumb-item.active')
                ->assertVue('breadcrumbList[0].text', 'Устройства', '@breadcrumb-component')
//                ->screenshot('Exampl_Login_in8')
                ->assertSee('IMEI')
            ;
        });
    }

    /**
     * A Dusk test формы листа, открыть создание  устройства.
     *
     * @depends testDeviceList
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testFindDeviceList()
    {
        $this->browse(function (Browser $browser) {
            $browser

//                ->visit('/')
//                ->visit('app#/')
//                ->type('#inputEmail', '9133310802')
//                ->type('#inputPassword', '12345678')
//                ->press('Вход')
//                ->visit('app#/manager/devices/')
//                ->waitForText('IMEI')
//                ->assertVue('breadcrumbList[0].text', 'Устройства', '@breadcrumb-component')
                ->type('div.search-input input.input', 'TESTST901')
                ->waitForText('IMEI')
//                ->pause(500)
                ->assertDontSee('ST-901')
            ;
        });
    }

    /**
     * A Dusk test формы  создания  устройства.
     *
     * @depends testFindDeviceList
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testCreateDevice()
    {
        $device =  Device::withTrashed()->where('imei','35686800004141120')->first();
        if ($device) {
            $device->forceDelete();
            dump ('Dedleted imei,35686800004141120 ');
        }


        $this->browse(function (Browser $browser) {
            $browser
//                ->visit('/')
//                ->visit('app#/')
//                ->type('#inputEmail', '9133310802')
//                ->type('#inputPassword', '12345678')
//                ->press('Вход')
//                ->visit('app#/manager/devices/')
//                ->waitForText('IMEI')


                ->press('Создать')
                ->waitForText('Код IMEI')
                ->assertSeeIn('#app > div > ol > li.breadcrumb-item.active > span', 'Редактирование')
                ->assertVue('breadcrumbList[0].text', 'Устройства', '@breadcrumb-component')
                ->assertVue('breadcrumbList[1].text', 'Редактирование', '@breadcrumb-component')
                ->assertDontSee('Запись сохраненна')
                ->press('Сохранить')
                ->pause(750)
                ->screenshot('Exampl_Login_in19')
                ->waitUntilMissing('div.loading-background') // @Loader
                ->assertSee('The name field is required')
                ->assertSee('The alias field is required')
                ->assertSee('The imei field is required')
                ->assertDontSee('Ошибка - Request')
                ->type('form > div:nth-child(1) > div > div > input', 'GSM gps трекер ST-901')
                ->type('form > div:nth-child(2) > div:nth-child(1) > div > input', 'TESTST901')
                ->type('form > div:nth-child(2) > div:nth-child(2) > div > input', 'ТЕСТ.Подлежит удалению.Мини водонепроницаемый Встроенный аккумулятор GSM gps трекер ST-901 для автомобиля мотоцикла 3g WCDMA устройство с программное обеспечение для онлайн отслеживания')
                ->click('form > div:nth-child(3) > div:nth-child(2) > div > div.vue-treeselect__control > div.vue-treeselect__control-arrow-container')
                ->waitForText('Fish1')
                ->click('form > div:nth-child(3) > div:nth-child(2) > div > div.vue-treeselect__menu-container > div > div > div:nth-child(2) > div > div > label')
                ->type('form > div:nth-child(3) > div:nth-child(1) > div > input', '35686800004141120')
                ->press('Сохранить')
                ->waitForText('Запись сохраненна')
                ->assertSeeIn('#app > div > ol > li.breadcrumb-item.active > span', 'Редактирование')
                ->assertVue('breadcrumbList[0].text', 'Устройства', '@breadcrumb-component')
                ->assertVue('breadcrumbList[1].text', 'Редактирование', '@breadcrumb-component')
            ;
        });

    }

    /**
     * A Dusk test формы  создания  устройства.
     *
     * @depends testCreateDevice
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testUpdateDevice()
    {
        $this->browse(function (Browser $browser) {
            $browser
//                ->visit('/')
//                ->visit('app#/')
//                ->type('#inputEmail', '9133310802')
//                ->type('#inputPassword', '12345678')
//                ->press('Вход')
//                ->visit('app#/manager/devices/')
//                ->waitForText('IMEI',10)
//                ->press('Создать')
//                ->waitForText('IMEI')


                ->click('#app > div > ol > li:nth-child(1) > a')
                ->waitForText('IMEI')
                ->type('div.search-input input.input', 'TESTST901')
                ->waitForText('TESTST901')
                ->assertSee('ST-901')
                ->clickLink('', '.button.edit:has(.fa-pencil-alt)')// пиктограмма карандаш, текста ссылки нет

                ->waitForText('IMEI')
                ->assertSeeIn('#app > div > ol > li.breadcrumb-item.active > span', 'Редактирование')
                ->assertVue('breadcrumbList[0].text', 'Устройства', '@breadcrumb-component')
                ->assertVue('breadcrumbList[1].text', 'Редактирование', '@breadcrumb-component')
                ->assertDontSee('Запись сохраненна')
                ->pause(1000)
                ->waitUntilMissing('div.loading-background') // @Loader
                ->press('Сохранить')
                ->waitForText('Запись сохраненна')
                ->type('form > div:nth-child(1) > div > div > input', 'GSM gps трекер ST-9011')
                ->press('Сохранить')
                ->waitForText('Запись сохраненна')
                ->type('form > div:nth-child(1) > div > div > input', 'GSM gps трекер ST-901')
                ->press('Сохранить')
                ->waitForText('Запись сохраненна')
                ->type('form > div:nth-child(2) > div:nth-child(1) > div > input', 'TESTST9011')
                ->press('Сохранить')
                ->waitForText('Запись сохраненна')
                ->type('form > div:nth-child(2) > div:nth-child(1) > div > input', 'TESTST901')
                ->press('Сохранить')
                ->waitForText('Запись сохраненна')
                ->type('form > div:nth-child(2) > div:nth-child(2) > div > input', '7ТЕСТ.Подлежит удалению.Мини водонепроницаемый Встроенный аккумулятор GSM gps трекер ST-901 для автомобиля мотоцикла 3g WCDMA устройство с программное обеспечение для онлайн отслеживания')
                ->press('Сохранить')
                ->waitForText('Запись сохраненна')
                ->type('form > div:nth-child(2) > div:nth-child(2) > div > input', 'ТЕСТ.Подлежит удалению.Мини водонепроницаемый Встроенный аккумулятор GSM gps трекер ST-901 для автомобиля мотоцикла 3g WCDMA устройство с программное обеспечение для онлайн отслеживания')
                ->press('Сохранить')
                ->waitForText('Запись сохраненна')
                ->click('form > div:nth-child(3) > div:nth-child(2) > div > div.vue-treeselect__control > div.vue-treeselect__control-arrow-container')
                ->waitForText('Fish1')
                ->click('form > div:nth-child(3) > div:nth-child(2) > div > div.vue-treeselect__menu-container > div > div > div:nth-child(2) > div > div > label')
                ->press('Сохранить')
                ->waitForText('Запись сохраненна')
                ->type('form > div:nth-child(3) > div:nth-child(1) > div > input', '356868000041411201')
                ->press('Сохранить')
                ->waitForText('Запись сохраненна')
                ->type('form > div:nth-child(3) > div:nth-child(1) > div > input', '35686800004141120')
                ->press('Сохранить')
                ->waitForText('Запись сохраненна')
                ->assertSeeIn('#app > div > ol > li.breadcrumb-item.active > span', 'Редактирование')
                ->assertVue('breadcrumbList[0].text', 'Устройства', '@breadcrumb-component')
                ->assertVue('breadcrumbList[1].text', 'Редактирование', '@breadcrumb-component');
        });
    }

    /**
     * A Dusk test формы  создания  устройства.
     *
     * @depends testCreateDevice
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testDeleteDevice()
    {
        $this->browse(function (Browser $browser) {
            $browser
//                ->visit('/')
//                ->visit('app#/')
//                ->type('#inputEmail', '9133310802')
//                ->type('#inputPassword', '12345678')
//                ->press('Вход')
//                ->visit('app#/manager/devices/')
//                ->waitForText('IMEI',10)
//                ->press('Создать')


                ->clickLink('Отмена')
                ->waitForText('IMEI')

                ->type('div.search-input input.input', 'TESTST901')
                ->waitForText('ST-901')
                ->waitUntilMissing('div.overlay-loader') // есть еще div.overlay-loader = @LoaderRed
                ->assertSee('ST-901')
                ->screenshot('Exampl_Login_in91')

                ->clickLink('', '.button.is-naked:has(.fa-trash-alt)') // пиктограмма корзина, текста ссылки нет
                ->pause(500)
                ->assertSee('Подтвердите')
                ->press('Отмена')
                ->assertDontSee('Подтвердите')
                ->clickLink('', '.button.is-naked:has(.fa-trash-alt)') // пиктограмма корзина, текста ссылки нет
                ->pause(500)
                ->assertSee('Удалить запись?')
                ->press('Да')
                ->assertDontSee('Удалить запись?')
                ->waitForText('IMEI')
                ->pause(1250)
                ->assertDontSee('ST-901')
                ->assertSeeIn('#app > div > ol > li.breadcrumb-item.active > span', 'Устройства')
                ->assertVue('breadcrumbList[0].text', 'Устройства', '@breadcrumb-component')
            ;
        });

        $device =  Device::withTrashed()->where('imei','35686800004141120')->first();
        if ($device) {
            $device->forceDelete();
            dump ('Dedleted imei,35686800004141120 ');
        }

    }
    public static function trashedDeleteDevice($imei)
    {
        $device = Device::withTrashed()->where('imei', $imei)->first();
        if ($device) {
            TransferDevice::withTrashed()->where('device_id', $device->id)->forceDelete();
            $device->forceDelete();
            dump('Dedleted imei,'.$imei);
        }

    }
}



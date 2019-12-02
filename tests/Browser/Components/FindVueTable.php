<?php

namespace Tests\Browser\Components;

use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class FindVueTable extends BaseComponent
{
    public $resourceName;

    /**
     * Create a new component instance.
     *
     * @param  string  $resourceName
     * @param  string  $lens
     * @return void
     */
    public function __construct($resourceName)
    {
        $this->resourceName = $resourceName;
    }

    /**
     * Get the root selector for the component.
     *
     * @return string
     */
    public function selector()
    {
        dump (__METHOD__, 'selector');
//        return $this->elements()['@FindInput'] ;
//        return '@provider-pos-payment-component';
        return $this->resourceName;
    }

    /**
     * Assert that the browser page contains the component.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        dump (__METHOD__, 'assert');
        $browser
//            ->click($this->selector())
//            ->clickLink($tabs)
//            ->assertVisible($this->selector().' div.search-input input.input');
            ->assertPresent($this->selector());
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array
     */
    public function elements()
    {
        dump (__METHOD__, 'elemernts');
        return [
            '@FindInput' => 'div.search-input input.input',
//            '@element' => '#selector',
        ];
    }

    /**
     * Ввести данные для поска.
     *
     * @param  \Laravel\Dusk\Browser $browser
     * @param null $find
     * @return void
     */

    public function findVueTable(Browser $browser, $find =null)

    {
        dump('findVue');
        $browser
            ->pause(250)
//            ->waitUntilMissing('@LoaderRed') // есть еще div.overlay-loader
            ->type('@FindInput', $find)
            ->pause(250)
        ;
    }

    /**
     * Выбрать по селектору позицию.
     *
     * @param  \Laravel\Dusk\Browser $browser
     * @param null $select_position
     * @return void
     * @throws TimeOutException
     */

    public function selectVueTable(Browser $browser, $select_position =null)

    {
        $browser
//            ->pause(250)
            ->click('@VueTableSelectX')
            ->type('@VueTableSelectInput',  mb_substr($select_position, 0, 6))
            ->waitForText($select_position)
//            ->screenshot('Exampl_Login_in198')
            ->keys('@VueTableSelectInput', '{enter}')
            ->waitForText('Итого')
            ->click('@VueTableSelect')
//            ->pause(250)
//            ->screenshot('Exampl_Login_in199')
        ;
    }
    /**
     * Удалить запись.
     *
     * @param  \Laravel\Dusk\Browser $browser
     * @param null $select_position
     * @return void
     * @throws TimeOutException
     */

    public function deleteVueTable(Browser $browser, $check =null)

    {
        $browser
            ->click( '@VueTableTrash')// пиктограмма мусорка, текста ссылки нет
            ->waitForText('Подтвердите')
            ->assertSee('Удалить запись?')
            ->press('Отмена')
//            ->waitForText($pos)
            ->assertDontSee('Удалить запись')
            ->click( '@VueTableTrash')// пиктограмма мусорка, текста ссылки нет
            ->waitForText('Подтвердите')
            ->assertSee('Удалить запись?')
            ->press('Да')
            ->screenshot('Exampl_Login_in199')
            ->waitUntilMissing('@LoaderRed') // есть еще div.overlay-loader
            ->waitForText($check)
            ->assertSee('Нет данных')
        ;
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: ddd
 * Date: 24.09.2020
 * Time: 19:16
 */

namespace App\Http\Controllers;


use App\DesignPatterns\Structural\Bridge\WithBridge\BridgeDemo;

class StructuralPattrnsController
{

    public function Bridge()
    {
        $name = 'Мост (Bridge)';
        $description = BridgeDemo::getDescription();

        (new BridgeDemo())->run();

        return view('welcome', compact('name', 'description'));
    }

}
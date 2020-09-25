<?php
/**
 * Created by PhpStorm.
 * User: ddd
 * Date: 24.09.2020
 * Time: 20:36
 */

namespace App\DesignPatterns\Structural\Bridge\WithBridge\Realization;


interface WidgetRealizationInterface
{
    public function getId();
    public function getTitle();
    public function getDescription();

}
<?php
/**
 * Created by PhpStorm.
 * User: ddd
 * Date: 24.09.2020
 * Time: 21:01
 */

namespace App\DesignPatterns\Structural\Bridge\WithBridge\Abstraction;


use App\DesignPatterns\Structural\Bridge\WithBridge\Realization\WidgetRealizationInterface;

// Базовая логика
class WidgetAbstract
{
    protected $realization;

    public function setRealization(WidgetRealizationInterface $realization)
    {
        $this->realization = $realization;

    }

    public function getRealization()
    {
        return $this->realization;
    }

    public function viewLogic($viewData)
    {
        $method = class_basename(static::class). '::'. __FUNCTION__;
        \Debugbar::info($method,$viewData);

    }
}

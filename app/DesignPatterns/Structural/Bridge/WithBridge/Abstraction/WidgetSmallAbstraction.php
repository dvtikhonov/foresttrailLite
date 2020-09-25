<?php
/**
 * Created by PhpStorm.
 * User: ddd
 * Date: 24.09.2020
 * Time: 20:49
 */

namespace App\DesignPatterns\Structural\Bridge\WithBridge\Abstraction;


use App\DesignPatterns\Structural\Bridge\WithBridge\Realization\WidgetRealizationInterface;
use Illuminate\Support\Str;

class WidgetSmallAbstraction extends WidgetAbstract
{
    public function run(WidgetRealizationInterface $realization)
    {
        $this->setRealization($realization);

        $viewData = $this->getViewData();
        $this->viewLogic($viewData);
    }

    public function getViewData()
    {
        $id = $this->getRealization()->getId();
        $smallTitle = $this->getSmallTitle();

        return compact('id','smallTitle' );
    }
// Дополнительная логика, модификация
    public function getSmallTitle()
    {
        return Str::limit($this->getRealization()->getTitle(), 5);
    }

}
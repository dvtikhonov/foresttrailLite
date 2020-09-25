<?php
/**
 * Created by PhpStorm.
 * User: ddd
 * Date: 24.09.2020
 * Time: 20:49
 */

namespace App\DesignPatterns\Structural\Bridge\WithBridge\Abstraction;


use App\DesignPatterns\Structural\Bridge\WithBridge\Realization\WidgetRealizationInterface;

class WidgetBigAbstraction extends WidgetAbstract
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
        $fullTitle = $this->getFullTitle();
        $description = $this->getRealization()->getDescription();

        return compact('id','fullTitle', 'description');
    }
// Дополнительная логика, модификация
    public function getFullTitle()
    {
        return $this->getRealization()->getId(). '::::'.
            $this->getRealization()->getTitle();
    }

}
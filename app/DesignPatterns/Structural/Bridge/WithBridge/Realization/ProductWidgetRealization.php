<?php
/**
 * Created by PhpStorm.
 * User: ddd
 * Date: 24.09.2020
 * Time: 20:26
 */

namespace App\DesignPatterns\Structural\Bridge\WithBridge\Realization;


use App\DesignPatterns\Structural\Bridge\Entities\Product;

class ProductWidgetRealization implements WidgetRealizationInterface
{
    private $entity;

    public function __construct( Product $product)
    {
        $this->entity = $product;
    }

    public function getId()
    {
        return $this->entity->id;

    }

    public function getTitle()
    {
        return $this->entity->name;
    }
    public function getDescription()
    {
        return $this->entity->description;
    }

}
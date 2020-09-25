<?php
/**
 * Created by PhpStorm.
 * User: ddd
 * Date: 24.09.2020
 * Time: 20:26
 */

namespace App\DesignPatterns\Structural\Bridge\WithBridge\Realization;


use App\DesignPatterns\Structural\Bridge\Entities\Category;

class CategoryWidgetRealization implements WidgetRealizationInterface
{
    private $entity;

    public function __construct( Category $category)
    {
        $this->entity = $category;
    }

    public function getId()
    {
        return $this->entity->id;

    }

    public function getTitle()
    {
        return $this->entity->title;
    }
    public function getDescription()
    {
        return $this->entity->description;
    }

}
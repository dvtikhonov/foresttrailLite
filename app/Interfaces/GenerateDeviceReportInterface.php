<?php

namespace App\Interfaces;


interface GenerateDeviceReportInterface
{
    public function start(\DateTime $date = null, $force = false);
    public function test($force = false);
}
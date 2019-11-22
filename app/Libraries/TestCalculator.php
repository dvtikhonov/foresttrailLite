<?php

namespace App\Libraries;


use App\Interfaces\UserVectorsInterface;
use Illuminate\Support\Facades\Cache;;
use Phpml\Regression\SVR;
use Phpml\SupportVectorMachine\Kernel;

class TestCalculator implements UserVectorsInterface
{
    public function test($force = false)
    {
        $start = microtime(true);
        $classifier = null;

        if( ! $force){
            $classifier = unserialize( Cache::get('mlp') );
        }

        if( ! $classifier){
            $samples = [];
            $labels = [];

            for ($x = 1; $x < 4; $x++){
                for ($y = 1; $y < 4; $y++) {
                    for ($z = 1; $z < 4; $z++) {
                        for ($b = 1; $b < 4; $b++) {
                            $samples[] = [strval($x), strval($y), strval($z), strval($b)];
                            $labels[] = strval($x * $y * $z * $b);
                        }
                    }
                }
            }

            $classifier = new SVR(Kernel::POLYNOMIAL, 4);

            $classifier->train($samples, $labels);

            Cache::forever('mlp', serialize($classifier));
        }

        $time = microtime(true) - $start;
        echo 'Обучение: ' . $time . ' / ' . PHP_EOL;

        for ($i = 2; $i < 30 ; $i++){

            $arr = [$i,$i,$i,$i];
            $ml = $classifier->predict($arr);
            $result = $arr[0]*$arr[1]*$arr[2]*$arr[3];

            echo 'Результат ML: '. $ml . ' (Реальный результат '.$result.', отклонение '.ceil(($ml/$result - 1) * 100).'%)' . PHP_EOL;
        }

    }
}
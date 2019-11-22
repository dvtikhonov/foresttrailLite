<?php

namespace App\Libraries;


use App\Interfaces\UserVectorsInterface;
use App\Models\TrackerSession;
use App\Models\TrackerVector;
use Illuminate\Support\Facades\DB;
use Phpml\Classification\KNearestNeighbors;
use Phpml\Classification\MLPClassifier;
use Phpml\Classification\NaiveBayes;
use Phpml\Math\Distance\Minkowski;
use Phpml\NeuralNetwork\ActivationFunction\Sigmoid;
use Phpml\Regression\LeastSquares;
use Phpml\Regression\SVR;
use Phpml\SupportVectorMachine\Kernel;

class SvrUserVectors implements UserVectorsInterface
{
    CONST POINTS_LAYER = 1;
    CONST POINT_OFFSET = 0;

    public $regressionX, $regressionY;

    public function init()
    {

        $regression =  new SVR(Kernel::POLYNOMIAL, 2);

        $this->regressionX = clone $regression;
        $this->regressionY = clone $regression;

    }

    public function test($force = false)
    {
        $start = microtime(true);

        $sessions = TrackerSession::query()
            ->has('trackerVectors', '>=', 1, 'and', function ($q){
                $q->orderBy('id');
            })
            ->has('sensorLinearAccelerations', '>=', 1, 'and', function ($q){
                $q->orderBy('created_at');
            })
            ->has('sensorGyroscopes', '>=', 1, 'and', function ($q){
                $q->orderBy('created_at');
            })
            ->has('sensorMagneticFields', '>=', 1, 'and', function ($q){
                $q->orderBy('created_at');
            })
            ->where(['check' => true])//, 94, 99, 98, 97
            //->whereIn('id', [135, 134, 132]) //
            ->orderBy('created_at')
            ->get();

        $this->init();

        foreach ($sessions as $session){
            $sensors = [];
            foreach ($session->sensorLinearAccelerations as $key => $sensorLinearAcceleration) {
                if($key < self::POINTS_LAYER + self::POINT_OFFSET){continue;}

                $sensors[] = $this->getPointLayer($session, $key);
            }

            $vectorsX = [];
            $vectorsY = [];

            foreach ($session->trackerVectors as $key => $trackerVector) {
                if($key < self::POINTS_LAYER + self::POINT_OFFSET){continue;}
                $vectorsX[] = $trackerVector->x === (float) 0 ? $this->uniqueNull() : $trackerVector->x;
                $vectorsY[] = $trackerVector->y === (float) 0 ? $this->uniqueNull() : $trackerVector->y;
            }

            //dd($sensors, $vectorsX, $session->id);

            echo 'Точек на обучение, '.$session->id.' сессия : ' . count($sensors) . ' / ';
            $this->train($sensors, $vectorsX, $vectorsY);
        }

        $time = microtime(true) - $start;
        echo 'Обучение: ' . $time . ' / ';

        $sessionsWithoutVectors = TrackerSession::query()
            ->has('sensorLinearAccelerations', '>=', 1, 'and', function ($q){
                $q->orderBy('created_at');
            })
            ->where(['check' => false ]);

        if( ! $force){
            $sessionsWithoutVectors->has('trackerVectors', '=', 0);
        }

        $sessionsWithoutVectors = $sessionsWithoutVectors->get();

        if(count($sessionsWithoutVectors) > 0){
            foreach ($sessionsWithoutVectors as $session){
                DB::table('tracker_vectors')->where(['tracker_sessions_id' => $session->id])->delete();
                foreach ($session->sensorLinearAccelerations as $key => $linearAcceleration) {

                    if($key >= self::POINTS_LAYER - 1){
                        $point = $this->getPointLayer($session, $key);

                        $result = [
                            'x' => $this->regressionX->predict($point),
                            'y' => $this->regressionY->predict($point)
                        ];
                        $result['x'] = $result['x'] === '-nan' ? 0 : $result['x'];
                        $result['y'] = $result['y'] === '-nan' ? 0 : $result['y'];
                    }else{
                        $result = [
                            'x' => 0,
                            'y' => 0
                        ];
                    }


                    $vector = new TrackerVector();

                    $vector->x = $result['x'];
                    $vector->y = $result['y'];
                    $vector->z = 0;
                    $vector->tracker_sessions_id = $session->id;

                    $vector->save();
                }
                echo 'Пройдена сессия: ' . $session->id . ' / ';
            }
        }else{
            echo 'Все сессии закончены';
            return false;
        }
    }

    private function train($sensors, $vectorsX, $vectorsY)
    {
        $this->regressionX->train($sensors, $vectorsX);
        $this->regressionY->train($sensors, $vectorsY);
    }

    private function getPointLayer($session, $key)
    {
        $inputLayer = [];

        for($i = self::POINTS_LAYER - 1; $i >= 0; $i--){
            $beforeSensorLinearAcceleration = $session->sensorLinearAccelerations[$key-$i];
            $beforeSensorGyroscopes = $session->sensorGyroscopes[$key-$i];
            $beforeSensorMagneticFields = $session->sensorMagneticFields[$key-$i];

            array_push($inputLayer,
                $beforeSensorLinearAcceleration->north,
                $beforeSensorLinearAcceleration->east,
                $beforeSensorLinearAcceleration->up,
                $beforeSensorLinearAcceleration->interval,
                $beforeSensorGyroscopes->x,
                $beforeSensorGyroscopes->y,
                $beforeSensorGyroscopes->z,
                $beforeSensorGyroscopes->interval,
                $beforeSensorMagneticFields->x,
                $beforeSensorMagneticFields->y,
                $beforeSensorMagneticFields->z,
                $beforeSensorMagneticFields->interval
            );
        }

        return $inputLayer;
    }

    private function uniqueNull()
    {
        return rand(1,100000)/10000000000000;
    }
}
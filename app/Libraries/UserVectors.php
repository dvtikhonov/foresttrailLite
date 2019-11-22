<?php

namespace App\Libraries;


use App\Interfaces\UserVectorsInterface;
use App\Models\TrackerSession;
use App\Models\TrackerVector;
use Illuminate\Support\Facades\DB;
use Phpml\Classification\MLPClassifier;
use Phpml\NeuralNetwork\ActivationFunction\Sigmoid;
use Phpml\Regression\LeastSquares;
use Phpml\Regression\SVR;
use Phpml\SupportVectorMachine\Kernel;

class UserVectors implements UserVectorsInterface
{
    CONST POINTS_LAYER = 10;
    CONST POINT_OFFSET = 0;

    public $regressionX, $regressionY;

    public function init()
    {

        /*
        $regression =  new LeastSquares();

        $this->regressionX = clone $regression;
        $this->regressionY = clone $regression;
        */

        $sensors = TrackerVector::query()->get(['x', 'y']);

        $this->regressionX = new MLPClassifier(self::POINTS_LAYER * 4, [[3, new Sigmoid]], $sensors->pluck('x')->unique()->toArray());
        $this->regressionY = new MLPClassifier(self::POINTS_LAYER * 4, [[3, new Sigmoid]], $sensors->pluck('y')->unique()->toArray());

        $this->regressionX->setLearningRate(0.1);
        $this->regressionY->setLearningRate(0.1);
    }

    public function test($force = false)
    {
        $start = microtime(true);

        $sessions = TrackerSession::query()
            ->has('trackerVectors', '>=', 1, 'and', function ($q){
                $q->orderBy('created_at');
            })
            ->has('sensorLinearAccelerations', '>=', 1, 'and', function ($q){
                $q->orderBy('created_at');
            })
            ->where(['check' => true])
            ->orderBy('created_at')
            ->get();

        $this->init();

        foreach ($sessions as $session){
            $sensors = [];
            foreach ($session->sensorLinearAccelerations as $key => $sensorLinearAcceleration) {
                //if($key <= self::POINTS_LAYER){continue;}
                if($key <= self::POINTS_LAYER + self::POINT_OFFSET){continue;}

                $sensors[] = $this->getPointLayer($session, $key);

                //$sensors[] = $inputLayer+[$sensorLinearAcceleration->north, $sensorLinearAcceleration->east, $sensorLinearAcceleration->up, $sensorLinearAcceleration->interval];
            }

            $vectorsX = [];
            $vectorsY = [];

            foreach ($session->trackerVectors as $key => $trackerVector) {
                //if($key <= self::POINTS_LAYER - 1){continue;}
                if($key >= count($session->trackerVectors) - self::POINT_OFFSET - self::POINTS_LAYER){continue;}
                $vectorsX[] = $trackerVector->x;
                $vectorsY[] = $trackerVector->y;
            }

            //var_dump(count($sensors), count($vectorsX), $session->id);

            echo 'Точек на обучение, '.$session->id.' сессия : ' . count($sensors) . ' / ';
            $this->train($sensors, $vectorsX, $vectorsY);
        }

        $time = microtime(true) - $start;
        echo 'Обучение: ' . $time . ' / ';

        $sessionsWithoutVectors = TrackerSession::query()
            ->has('sensorLinearAccelerations', '>=', 1, 'and', function ($q){
                $q->orderBy('created_at');
            })
            ->where(['check' => false]);

        if( ! $force){
            $sessionsWithoutVectors->has('trackerVectors', '=', 0);
        }

        $sessionsWithoutVectors = $sessionsWithoutVectors->get();

        if(count($sessionsWithoutVectors) > 0){
            foreach ($sessionsWithoutVectors as $session){
                DB::table('tracker_vectors')->where(['tracker_sessions_id' => $session->id])->delete();
                foreach ($session->sensorLinearAccelerations as $key => $linearAcceleration) {

                    if($key > self::POINTS_LAYER){
                        $point = $this->getPointLayer($session, $key);

                        $result = [
                            'x' => $this->regressionX->predict($point),
                            'y' => $this->regressionY->predict($point)
                        ];
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
        /*
        $this->regressionX->train($sensors, $vectorsX);
        $this->regressionY->train($sensors, $vectorsY);
        */
        foreach ($sensors as $index => $sensor){
            $this->regressionX->partialTrain([$sensor], [$vectorsX[$index]]);
            $this->regressionY->partialTrain([$sensor], [$vectorsY[$index]]);
            echo 'Точка: '.$index.' | ';
        }
    }

    private function getPointLayer($session, $key)
    {
        $inputLayer = [];

        for($i = self::POINTS_LAYER - 1; $i >= 0; $i--){
            $beforeSensorLinearAcceleration = $session->sensorLinearAccelerations[$key-$i];
            array_push($inputLayer,
                $beforeSensorLinearAcceleration->north,
                $beforeSensorLinearAcceleration->east,
                $beforeSensorLinearAcceleration->up,
                $beforeSensorLinearAcceleration->interval
            );
        }

        return $inputLayer;
    }
}
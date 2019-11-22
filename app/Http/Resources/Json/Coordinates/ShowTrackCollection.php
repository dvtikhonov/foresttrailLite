<?php

namespace App\Http\Resources\Json\Coordinates;


use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class ShowTrackCollection extends ResourceCollection
{
    const AVERAGE_POINT = 10;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $collection  = collect(parent::toArray($this));
        $result = $collection->map(function($value,$key){
            $value['timestamp'] = (new \DateTime($value['created_at']))->getTimestamp();
            unset($value['created_at']);
            return $value;
        });

//        $result = $this->filterTrack($result->reverse());
//        $result = $this->filterTrack($result->reverse());
//        $result->reverse();

        return $result;
    }

    public function filterTrack(Collection $track)
    {
        $removePoint = [];

        $result = $track;
        $pointCount = $track->count();
        $currentPoint = self::AVERAGE_POINT;

        $allAverage = 0;
        $allAveragePoint = 0;

        for ($currentPoint; $currentPoint < $pointCount; $currentPoint++){
            $averageDistance = 0;
            $subCurrentPoint = $currentPoint - (self::AVERAGE_POINT);
            $subEndPoint = $subCurrentPoint + self::AVERAGE_POINT;
            $averagePoint = 0;

            for ($subCurrentPoint; $subCurrentPoint < $subEndPoint; $subCurrentPoint++){
                $distance = $this->calcDistanceTwoPoint($track, $subCurrentPoint, $subCurrentPoint + 1);
                $averagePoint += $distance > 0 ? 1 : 0;
                $averageDistance += $distance;
            }

            $averageDistance = $averagePoint > 0 ? $averageDistance / $averagePoint : 0;

            if(
                $averageDistance !== 0 &&
                $this->calcDistanceTwoPoint($track,$currentPoint, $currentPoint+1) > $averageDistance*1.1
            ){
                $removePoint[] = $currentPoint;

                $result->forget($currentPoint);
            }
            $allAveragePoint += $averagePoint;
            $allAverage += $averageDistance;
        }

        return $result;
    }

    private function calcDistanceTwoPoint(Collection $track, $point, $nextPoint){
        $point = $track->get($point);
        $nextPoint = $track->get($nextPoint);

        if(!$point || !$nextPoint){ return 0;}

        $x1 = $point['lat'];
        $y1 = $point['lon'];
        $x2 = $nextPoint['lat'];
        $y2 = $nextPoint['lon'];

        $y = $y2 - $y1;
        $x = $x2 - $x1;

        return sqrt(pow($x, 2) + pow($y, 2));
    }
}

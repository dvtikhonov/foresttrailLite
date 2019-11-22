<?php

namespace App\Libraries\Repositories;


use App\Interfaces\Repositories\CoordinatesRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CoordinatesRepository implements CoordinatesRepositoryInterface
{

    public function groupByAliases(array $aliases): array
    {
        if(empty($aliases)){return [];}

        $aliases = collect($aliases)->filter(function ($alias){
            return preg_match('/^\w*\d*$/', $alias);
        });

        $nowDate = Carbon::create();

        clock()->startEvent('load-data', "Loading data");

        $available_aliases = \DB::query()->from('devices')
            ->whereIn('devices.alias', $aliases)
            ->leftJoin('provider_pos','provider_pos.id', '=','devices.provider_pos_id')
            ->where('provider_pos.is_blocked',0)
            ->select(['alias'])
            ->get()->pluck('alias');

        $result = \DB::query()->from('coordinates')
            ->select(['alias', 'coordinates.lat', 'coordinates.lon', 'coordinates.created_at as timestamp', 'accuracy'])
            ->leftJoin('devices','devices.id', '=','coordinates.device_id')
            ->whereIn('devices.alias', $available_aliases)
            ->limit(10)
            ->orderBy('coordinates.created_at')
            ->get()
            ->groupBy('alias');

        $result = $result->map(function(Collection $tracker) use ($nowDate){
            return [
                'coords' => $tracker,
                'blocked' => false,
                'time_passed' => $this->getTimePassed($tracker, $nowDate)
            ];
        });

        $aliases
            ->diff($result->keys())
            ->map(function ($alias) use (&$result, $available_aliases){
                $result->put($alias, [
                    'coords' => [],
                    'blocked' => $available_aliases->search($alias) === false,
                    'time_passed' => null
                ]);
            });

        clock()->endEvent('load-data');

        return $result->toArray();
    }

    private function getTimePassed($tracker, $nowDate)
    {
        if($tracker->count() > 0) {
            $coordDate = Carbon::parse($tracker->last()->timestamp);
            $timePassed = $nowDate->diffInMinutes($coordDate);
            if($timePassed === 0){
                return 'только что';
            }else if($timePassed > 1 && $timePassed <= 5){
                return 'минуту назад';
            }else if($timePassed > 5 && $timePassed <= 15){
                return 'более 5 минут назад';
            }else if($timePassed > 15 && $timePassed <= 30){
                return 'более 15 минут назад';
            }else if($timePassed > 30){
                return 'более 30 минут назад';
            }
        }

        return null;
    }
}
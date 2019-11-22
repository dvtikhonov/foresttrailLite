<?php

namespace App\Http\Resources\Json\Coordinates;


use Illuminate\Http\Resources\Json\ResourceCollection;

class GroupTrackCollection extends ResourceCollection
{
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
        $result = $result->groupBy(function ($item, $key) {
            return 'user_'.$item['user_id'];
        });
        $result = $result->map(function($value,$key){
            $res = [
                'name' => ''
            ];

            $value = $value->map(function($value,$key) use (&$res){
                $res['name'] = $value['user']['name'];
                unset($value['user']);
                unset($value['user_id']);
                return $value;
            });

            $res['coords'] = $value;
            return $res;
        });
        return $result;
    }

}
// {"lat":53.8224366,"lon":87.1591632,"timestamp":1548696678095}
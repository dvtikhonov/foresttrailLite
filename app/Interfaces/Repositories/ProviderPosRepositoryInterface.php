<?php

namespace App\Interfaces\Repositories;



use Illuminate\Database\Eloquent\Builder;

interface ProviderPosRepositoryInterface
{
 //   public function tableQuery($provider_pos_id = null): Builder;
    public function clearDevice($devices_id):bool;
    public function getDevicePos($id): array;
    public function initArrayTreeSelect($pos);
    public function initArrayTreeSelectDevices($pos);
    public function initArrayTreeSelectPos($pos);


}
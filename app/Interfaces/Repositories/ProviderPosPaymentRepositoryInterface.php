<?php

namespace App\Interfaces\Repositories;



use Illuminate\Database\Eloquent\Builder;

interface ProviderPosPaymentRepositoryInterface
{
    public function tableQuery($provider_pos_id = null): Builder;
    public function calculateBalance(int $provider_pos_id): float;
    public function withdraw($provider_pos_id, $amount, $description = null, $target_id = null):bool;
    public function deposit($provider_pos_id, $amount, $description = null, $target_id = null):bool;
}
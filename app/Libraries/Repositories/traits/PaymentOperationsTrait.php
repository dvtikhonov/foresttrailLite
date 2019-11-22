<?php

namespace App\Libraries\Repositories\traits;


use App\Models\ProviderPosPayment;
use Illuminate\Support\Facades\Auth;

trait PaymentOperationsTrait
{
    private function saveOperation($operation_id, $provider_pos_id, $amount, $description = null, $target_id = null){
        return (new ProviderPosPayment([
            'amount' => $amount,
            'description' => $description,
            'provider_pos_id' => $provider_pos_id,
            'user_id' => Auth::id(),
            'operation_id' => $operation_id,
            'target_id' => $target_id
        ]))->save();
    }

    public function withdraw($provider_pos_id, $amount, $description = null, $target_id = null):bool
    {
        return !! $this->saveOperation(self::OPERATION_WITHDRAW, $provider_pos_id, $amount, $description, $target_id);
    }

    public function deposit($provider_pos_id, $amount, $description = null, $target_id = null):bool
    {
        return !! $this->saveOperation(self::OPERATION_DEPOSIT, $provider_pos_id, $amount, $description, $target_id);
    }
}
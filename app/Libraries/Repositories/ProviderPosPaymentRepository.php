<?php

namespace App\Libraries\Repositories;


use App\Interfaces\Repositories\ProviderPosPaymentRepositoryInterface;
use App\Libraries\Repositories\traits\PaymentOperationsTrait;
use App\Models\ProviderPosPayment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProviderPosPaymentRepository implements ProviderPosPaymentRepositoryInterface
{
    use PaymentOperationsTrait;

    const OPERATION_WITHDRAW = 1;
    const OPERATION_DEPOSIT = 2;

    public function tableQuery($provider_pos_id = null): Builder
    {
        $query = ProviderPosPayment::from(
            DB::raw('(
             SELECT IF(operation_id != 2, -(amount), amount)  as amount,
                provider_pos_payments.id as dtRowId , provider_pos_payments.description, 
                provider_pos_payments.provider_pos_id, provider_pos_payments.created_at as date,
                users.name as user_name, operations.name as operation_name,
                provider_pos.name as pos_name, provider_pos_payments.target_id,
                provider_pos_payments.deleted_at
             FROM provider_pos_payments
             LEFT JOIN operations  ON (provider_pos_payments.operation_id = operations.id)
             LEFT JOIN users  ON (provider_pos_payments.user_id = users.id)
             LEFT JOIN provider_pos  ON (provider_pos_payments.provider_pos_id = provider_pos.id)
             ) as provider_pos_payments')
        )->orderByDesc('date')->orderByDesc('dtRowId');

        if( ! $provider_pos_id && ! is_array($provider_pos_id)) {return $query;}
        return $query->whereIn('provider_pos_payments.provider_pos_id', $provider_pos_id);
    }

    public function calculateBalance(int $provider_pos_id): float
    {
        return $this->tableQuery([$provider_pos_id])->sum('amount');
    }
}
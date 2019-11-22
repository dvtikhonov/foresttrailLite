<?php

namespace App\Observers;

use App\Interfaces\Repositories\ProviderPosPaymentRepositoryInterface;
use App\Models\ProviderPos;
use App\Models\ProviderPosPayment;

class PosPaymentObserver
{
    private $providerPosPaymentRepository;

    public function __construct(ProviderPosPaymentRepositoryInterface $providerPosPaymentRepository)
    {
        $this->providerPosPaymentRepository = $providerPosPaymentRepository;
    }

    /**
     * Handle the User "updated" event.
     *
     * @param ProviderPosPayment $providerPosPayment
     * @return void
     */
    public function saved(ProviderPosPayment $providerPosPayment)
    {
        $providerPos = $providerPosPayment->providerPos;

        $providerPos->balance = $this->providerPosPaymentRepository->calculateBalance($providerPos->id);
        $providerPos->save();
    }
}

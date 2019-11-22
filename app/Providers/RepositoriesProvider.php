<?php

namespace App\Providers;

use App\Models\ProviderPos;
use App\Models\ProviderPosPayment;
use App\Observers\PosPaymentObserver;
use App\Observers\ProviderPosObserver;
use Illuminate\Support\ServiceProvider;

class RepositoriesProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ProviderPos::observe(ProviderPosObserver::class);
        ProviderPosPayment::observe(PosPaymentObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app()->bind(
            \App\Interfaces\Repositories\CoordinatesRepositoryInterface::class,
            \App\Libraries\Repositories\CoordinatesRepository::class
        );
        app()->bind(
            \App\Interfaces\Repositories\ProviderPosPaymentRepositoryInterface::class,
            \App\Libraries\Repositories\ProviderPosPaymentRepository::class
        );
        app()->bind(
            \App\Interfaces\Repositories\ProviderPosRepositoryInterface::class,
            \App\Libraries\Repositories\ProviderPosRepository::class
        );
    }
}

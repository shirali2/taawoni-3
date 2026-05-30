<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Services\WalletService::class);
        $this->app->singleton(\App\Services\SmsVariableParser::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \App\invoice::observe(\App\Observers\InvoiceObserver::class);
        \App\invoice_pay::observe(\App\Observers\PaymentObserver::class);
        \App\user::observe(\App\Observers\UserObserver::class);
        \App\menu::observe(\App\Observers\MenuObserver::class);
    }
}

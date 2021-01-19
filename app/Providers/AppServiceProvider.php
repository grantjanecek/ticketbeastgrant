<?php

namespace App\Providers;

use App\TicketCodeGenerator;
use App\HashidsTicketCodeGenerator;
use Illuminate\Support\ServiceProvider;
use App\OrderConfirmationNumberGenerator;
use App\RandomOrderConfirmationNumberGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(HashidsTicketCodeGenerator::class, fn() => new HashidsTicketCodeGenerator(config('app.ticket_code_salt')));
        $this->app->bind(OrderConfirmationNumberGenerator::class, RandomOrderConfirmationNumberGenerator::class);
        $this->app->bind(TicketCodeGenerator::class, HashidsTicketCodeGenerator::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

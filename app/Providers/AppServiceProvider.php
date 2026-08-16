<?php

namespace App\Providers;

use App\Models\Call;
use App\Observers\CallObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Call::observe(CallObserver::class);
    }
}

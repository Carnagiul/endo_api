<?php

namespace App\Providers;

use App\Models\Group;
use App\Models\Player;
use App\Observers\GroupObserver;
use App\Observers\PlayerObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Player::observe(PlayerObserver::class);
        Group::observe(GroupObserver::class);
        //
    }
}

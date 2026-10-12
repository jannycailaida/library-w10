<?php

namespace App\Providers;

use App\Models\Member;
use App\Models\User;
use App\Policies\LoanPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
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
        Schema::defaultStringLength(191);

        Gate::define('manage-users', fn (User $user) => $user->isAdmin());

        Gate::policy(Member::class, LoanPolicy::class);
    }
}

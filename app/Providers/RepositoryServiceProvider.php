<?php

namespace App\Providers;

use App\Repository\AuditLogRepository;
use App\Repository\Contract\IAuditLogRepository;
use App\Repository\Contract\IUserRepository;
use App\Repository\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IAuditLogRepository::class, AuditLogRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

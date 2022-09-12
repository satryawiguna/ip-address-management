<?php

namespace App\Providers;

use App\Repository\AuditLogRepository;
use App\Repository\Contract\IAuditLogRepository;
use App\Repository\Contract\IIpAddressRepository;
use App\Repository\Contract\ILabelRepository;
use App\Repository\Contract\IUserRepository;
use App\Repository\IpAddressRepository;
use App\Repository\LabelRepository;
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
        $this->app->bind(IIpAddressRepository::class, IpAddressRepository::class);
        $this->app->bind(ILabelRepository::class, LabelRepository::class);
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

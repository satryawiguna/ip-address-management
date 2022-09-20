<?php

namespace App\Providers;

use App\Service\AuthService;
use App\Service\BaseService;
use App\Service\Contract\IAuthService;
use App\Service\Contract\IBaseService;
use App\Service\Contract\IManageService;
use App\Service\Contract\ILogService;
use App\Service\Contract\IUserService;
use App\Service\ManageService;
use App\Service\LogService;
use App\Service\UserService;
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
        $this->app->bind(IBaseService::class, BaseService::class);
        $this->app->bind(IUserService::class, UserService::class);
        $this->app->bind(IAuthService::class, AuthService::class);
        $this->app->bind(ILogService::class, LogService::class);
        $this->app->bind(IManageService::class, ManageService::class);
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

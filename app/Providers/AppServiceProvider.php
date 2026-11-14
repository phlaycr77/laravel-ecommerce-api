<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        // Giữ lại dòng bind UserRepositoryInterface cũ của bạn ở đây
    $this->app->bind(
        \App\Repositories\Contracts\CategoryRepositoryInterface::class,
        \App\Repositories\CategoryRepository::class
    );
    $this->app->bind(
        \App\Repositories\Contracts\ProductRepositoryInterface::class,
        \App\Repositories\ProductRepository::class
    );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
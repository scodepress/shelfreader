<?php

namespace App\Providers;

use App\Repositories\Contracts\FullShelfRepository;
use App\Repositories\Contracts\SortsRepository;
use App\Repositories\Eloquent\EloquentFullShelfRepository;
use App\Repositories\Eloquent\EloquentSortsRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(FullShelfRepository::class, EloquentFullShelfRepository::class);
        $this->app->bind(SortsRepository::class, EloquentSortsRepository::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

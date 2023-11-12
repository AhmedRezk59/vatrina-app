<?php

namespace App\Providers;

use App\Contracts\CollectionContract;
use App\Contracts\GetCollectionsInterface;
use App\Contracts\GetProductsInterface;
use App\Contracts\ProductContract;
use App\Contracts\ProductControllerContract;
use App\Repositories\FilterForCollectionContract;
use App\Repositories\FilterForProductContract;
use App\Repositories\GetCollectionsRepository;
use App\Repositories\GetProductsRepository;
use App\Repositories\ProductControllerRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CollectionContract::class , FilterForCollectionContract::class);
        $this->app->bind(ProductContract::class , FilterForProductContract::class);
        $this->app->bind(ProductControllerContract::class , ProductControllerRepository::class);
        $this->app->bind(GetProductsInterface::class , GetProductsRepository::class);
        $this->app->bind(GetCollectionsInterface::class , GetCollectionsRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Model::preventLazyLoading();
    }
}

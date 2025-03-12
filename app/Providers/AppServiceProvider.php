<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Filament\Resources\ProductResource as CustomProductResource;
use TomatoPHP\FilamentEcommerce\Filament\Resources\ProductResource as PackageProductResource;
use Filament\Facades\Filament;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    //     $this->app->alias(
    //         \App\Filament\Resources\ProductResource::class,
    //         \TomatoPHP\FilamentEcommerce\Filament\Resources\ProductResource::class
    //     );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // Filament::registerResources([
        //     \App\Filament\Resources\ProductResource::class, // Ensure Filament loads your custom resource
        // ]);
    }
}

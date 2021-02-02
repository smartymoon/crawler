<?php

namespace Smartymoon\Crawler;

use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {

    }

    public function register()
    {
//        $this->app->singleton(Package::class, function(){
//            return new Package();
//        });
    }
}

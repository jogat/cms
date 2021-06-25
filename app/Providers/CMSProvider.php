<?php

namespace App\Providers;

use App\CMS;
use Illuminate\Support\ServiceProvider;

class CMSProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('cms', function() {
            return new CMS();
        });
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

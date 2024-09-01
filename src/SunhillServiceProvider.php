<?php

namespace Sunhill;

use Illuminate\Support\ServiceProvider;
use Sunhill\Checker\Checks;
use Sunhill\Console\Check;

class SunhillServiceProvider extends ServiceProvider
{
    public function register()
    {        
        $this->app->singleton(Checks::class, function () { return new Checks(); } );
        $this->app->alias(Checks::class,'checks');
    }
    
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Check::class,
            ]);
        }
    }
}

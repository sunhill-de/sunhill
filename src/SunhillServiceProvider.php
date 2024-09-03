<?php

namespace Sunhill;

use Illuminate\Support\ServiceProvider;
use Sunhill\Checker\Checks;
use Sunhill\Console\Check;
use Sunhill\Filter\FilterManager;
use Sunhill\Managers\PropertiesManager;
use Sunhill\InfoMarket\Market;

class SunhillServiceProvider extends ServiceProvider
{
    public function register()
    {        
        $this->app->singleton(Checks::class, function () { return new Checks(); } );
        $this->app->alias(Checks::class,'checks');
    
        $this->app->singleton(FilterManager::class, function () { return new FilterManager(); } );
        $this->app->alias(FilterManager::class,'filters');

    
        $this->app->singleton(PropertiesManager::class, function () { return new PropertiesManager(); } );
        $this->app->alias(PropertiesManager::class,'properties');
        
        $this->app->singleton(Market::class, function () { return new Market(); } );
        $this->app->alias(Market::class,'infomarket');
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

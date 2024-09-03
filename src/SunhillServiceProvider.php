<?php

namespace Sunhill;

use Illuminate\Support\ServiceProvider;
use Sunhill\Checker\Checks;
use Sunhill\Console\Check;
use Sunhill\Filter\FilterManager;
use Sunhill\Managers\PropertiesManager;
use Sunhill\InfoMarket\Market;

use Sunhill\Managers\FilterManager;
use Sunhill\Managers\PluginManager;
use Sunhill\Managers\SiteManager;
use Sunhill\Components\OptionalLink;
use Illuminate\Support\Facades\Blade;
use Sunhill\Facades\Site;

class SunhillServiceProvider extends ServiceProvider
{
    public function register()
    {        
        // Checks facade
        $this->app->singleton(Checks::class, function () { return new Checks(); } );
        $this->app->alias(Checks::class,'checks');
    
        // Filter facade
        $this->app->singleton(FilterManager::class, function () { return new FilterManager(); } );
        $this->app->alias(FilterManager::class,'filters');
    
        $this->app->singleton(PropertiesManager::class, function () { return new PropertiesManager(); } );
        $this->app->alias(PropertiesManager::class,'properties');
        
        $this->app->singleton(Market::class, function () { return new Market(); } );
        $this->app->alias(Market::class,'infomarket');
    
        // Plugin manager facade
        $this->app->singleton(PluginManager::class, function () { return new PluginManager(); } );
        $this->app->alias(PluginManager::class,'plugins');
        
        // Site manager facade
        $this->app->singleton(SiteManager::class, function () { return new SiteManager(); } );
        $this->app->alias(SiteManager::class,'site');
    }
    
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Check::class,
            ]);
        }
        $this->loadViewsFrom(__DIR__.'/../resources/views','framework');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang','framework');
        Blade::component('optional_link', OptionalLink::class);
        Site::setupRoutes();
    }
}

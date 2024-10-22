<?php
/**
 * @file SunhillServiceprovider.php
 * The service provider for the sunhill framework
 * Lang en
 * Reviewstatus: 2024-10-05
 * Localization: incomplete
 * Documentation: complete
 */

namespace Sunhill;

use Illuminate\Support\ServiceProvider;
use Sunhill\Checker\Checks;
use Sunhill\Console\Check;
use Sunhill\Managers\PropertiesManager;
use Sunhill\InfoMarket\Market;
use Sunhill\Managers\PluginManager;
use Sunhill\Managers\SiteManager;
use Sunhill\Components\OptionalLink;
use Illuminate\Support\Facades\Blade;
use Sunhill\Facades\Site;
use Sunhill\Facades\Properties;
use Sunhill\Types\TypeBoolean;
use Sunhill\Types\TypeDate;
use Sunhill\Types\TypeDateTime;
use Sunhill\Types\TypeEnum;
use Sunhill\Types\TypeFloat;
use Sunhill\Types\TypeInteger;
use Sunhill\Types\TypeText;
use Sunhill\Types\TypeTime;
use Sunhill\Types\TypeVarchar;
use Sunhill\Semantics\Age;
use Sunhill\Semantics\Airpressure;
use Sunhill\Semantics\Airtemperature;
use Sunhill\Semantics\Capacity;
use Sunhill\Semantics\Count;
use Sunhill\Semantics\Creditcardnumber;
use Sunhill\Semantics\Direction;
use Sunhill\Semantics\Domain;
use Sunhill\Semantics\Duration;
use Sunhill\Semantics\EMail;
use Sunhill\Semantics\FirstName;
use Sunhill\Semantics\IDString;
use Sunhill\Semantics\Illuminance;
use Sunhill\Semantics\IPv4Address;
use Sunhill\Semantics\IPv6Address;
use Sunhill\Semantics\LastName;
use Sunhill\Semantics\MACAddress;
use Sunhill\Semantics\MD5;
use Sunhill\Semantics\Name;
use Sunhill\Semantics\PointInTime;
use Sunhill\Semantics\Pressure;
use Sunhill\Semantics\SHA1;
use Sunhill\Semantics\Speed;
use Sunhill\Semantics\Temperature;
use Sunhill\Semantics\Timestamp;
use Sunhill\Semantics\URL;
use Sunhill\Semantics\UUID4;
use Sunhill\Managers\FilterManager;
use Sunhill\Properties\ArrayProperty;

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
    
    protected function registerUnits()
    {
        Properties::registerUnit('none','','none');
        
        // *********************************** Length ******************************************
        Properties::registerUnit('meter','m','length');
        Properties::registerUnit('centimeter','cm','length','meter',
            function($input) { return $input / 100; },
            function($input) { return $input * 100; });
        Properties::registerUnit('millimeter','mm','length','meter',
            function($input) { return $input / 1000; },
            function($input) { return $input * 1000; });
        Properties::registerUnit('kilometer','km','length','meter',
            function($input) { return $input * 1000; },
            function($input) { return $input / 1000; });
        
        // ************************************ weight *****************************************
        Properties::registerUnit('kilogramm','kg','weight');
        Properties::registerUnit('gramm','g','weight','kilogramm',
            function($input) { return $input / 1000; },
            function($input) { return $input * 1000; });
        
        // ************************************ Temperature ***************************************
        Properties::registerUnit('degreecelsius','°C','temperature');
        Properties::registerUnit('degreekelvin','K','temperature','degreecelsius',
            function($input) { return $input - 273.15; },
            function($input) { return $input + 273.15; });
        Properties::registerUnit('degreefahrenheit','F','temperature','degreecelsius',
            function($input) { return ($input - 32) * 5/9; },
            function($input) { return $input * 1.8 + 32; });
        
        // ********************************** Speed ************************************************
        Properties::registerUnit('meterpersecond','m/s','speed');
        Properties::registerUnit('kilometerperhour','km/h','speed','meterpersecond',
            function($input) { return $input / 3.6; },
            function($input) { return $input * 3.6; });
        
        // ********************************* Time ****************************************
        Properties::registerUnit('second','s','duration');
        Properties::registerUnit('minute','min','duration','second',
            function($input) { return $input * 60; },
            function($input) { return $input / 60; });
        Properties::registerUnit('hour','h','duration','second',
            function($input) { return $input * 3600; },
            function($input) { return $input / 3600; });
        
        // ******************************** Angle ****************************************
        Properties::registerUnit('degree','°','angle');
        
        // ******************************** Ratio **********************************************
        Properties::registerUnit('percent','%','ratio');
        
        // ********************************* Capacity ****************************************
        Properties::registerUnit('byte','B','capacity');
        Properties::registerUnit('kilobyte','KB','capacity','byte',
            function($input) { return $input * 1000; },
            function($input) { return $input / 1000; });
        Properties::registerUnit('megabyte','MB','capacity','byte',
            function($input) { return $input * 1000000; },
            function($input) { return $input / 1000000; });
        
        // ****************************** Pressure ***************************************
        Properties::registerUnit('pascal', 'Pa', 'pressure');
        Properties::registerUnit('hectopascal', 'hPa', 'pressure', 'pascal',
            function($input) { return $input * 100; },
            function($input) { return $input / 100; }
            );
        
        Properties::registerUnit('lux', 'lx', 'light');
        
    }
    
    protected function registerTypes()
    {
        Properties::registerProperty(TypeBoolean::class);
        Properties::registerProperty(TypeBoolean::class, 'bool');
        Properties::registerProperty(TypeDate::class);
        Properties::registerProperty(TypeDateTime::class);
        Properties::registerProperty(TypeEnum::class);
        Properties::registerProperty(TypeFloat::class);
        Properties::registerProperty(TypeInteger::class);
        Properties::registerProperty(TypeInteger::class,'int');
        Properties::registerProperty(TypeText::class);
        Properties::registerProperty(TypeTime::class);
        Properties::registerProperty(TypeVarchar::class);
        Properties::registerProperty(TypeVarchar::class,'string');

        Properties::registerProperty(ArrayProperty::class);
        
    }
    
    protected function registerSemantics()
    {
        Properties::registerProperty(Age::class);
        Properties::registerProperty(Airpressure::class);
        Properties::registerProperty(Airtemperature::class);
        Properties::registerProperty(Capacity::class);
        Properties::registerProperty(Count::class);
        Properties::registerProperty(Creditcardnumber::class);
        Properties::registerProperty(Direction::class);
        Properties::registerProperty(Domain::class);
        Properties::registerProperty(Duration::class);
        Properties::registerProperty(EMail::class);
        Properties::registerProperty(FirstName::class);
        Properties::registerProperty(IDString::class);
        Properties::registerProperty(Illuminance::class);
        Properties::registerProperty(IPv4Address::class);
        Properties::registerProperty(IPv6Address::class);
        Properties::registerProperty(LastName::class);
        Properties::registerProperty(MACAddress::class);
        Properties::registerProperty(MD5::class);
        Properties::registerProperty(Name::class);
        Properties::registerProperty(PointInTime::class);
        Properties::registerProperty(Pressure::class);
        Properties::registerProperty(SHA1::class);
        Properties::registerProperty(Speed::class);
        Properties::registerProperty(Temperature::class);
        Properties::registerProperty(Timestamp::class);
        Properties::registerProperty(URL::class);
        Properties::registerProperty(UUID4::class);
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
/*        Site::setupRoutes(); */
        $this->registerTypes();
        $this->registerSemantics();
        $this->registerUnits(); 
    }
}

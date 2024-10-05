<?php

namespace Sunhill\Managers;

use Sunhill\Modules\SkinModule;
use Sunhill\Modules\AdminModule;
use Illuminate\Support\Facades\Route;
use Sunhill\Modules\FeatureModules\FeatureModule;
use Sunhill\Modules\Exceptions\CantProcessModuleException;

class SiteManager 
{
    
    protected $main_module;
    
    public function installMainmodule($module)
    {
        if (is_string($module) && class_exists($module)) {
            $module = new $module();
        }
        if (!is_a($module, FeatureModule::class)) {
            throw new CantProcessModuleException("The given parameter can't be processed to a main module");
        }
        $this->main_module = $module;
        return $module;
    }
    
    public function flushMainmodule()
    {
        unset($this->main_module);
    }
    
    public function getMainModule()
    {
        return $this->main_module;    
    }
    
    public function installSkin(SkinModule $skin)
    {
        
    }
    
    public function installAdminModule(AdminModule $module)
    {
        
    }
    
    public function get404Error()
    {
        return view('framework::errors.error404', ['sitename'=>env('APP_NAME','Sunhill')]);    
    }
    
    public function setupRoutes()
    {
        Route::fallback(function() { return $this->get404Error(); });
    }
}
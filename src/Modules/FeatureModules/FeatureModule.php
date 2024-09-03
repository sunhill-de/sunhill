<?php
/**
 * @file FeatureModule.php
 * Provides the basic class for all feature modules
 * Lang en
 * Reviewstatus: 2024-04-08
 * Localization:
 * Documentation:
 * Tests:
 * Coverage: unknown
 * PSR-State: complete
 */

namespace Sunhill\Framework\Modules\FeatureModules;

use Sunhill\Framework\Response\AbstractResponse;
use Sunhill\Framework\Traits\NameAndDescription;
use Sunhill\Framework\Modules\AbstractModule;
use Sunhill\Framework\Modules\Exceptions\CantProcessModuleException;
use Sunhill\Framework\Traits\Children;
use Sunhill\Framework\Modules\Exceptions\CantProcessResponseException;
use Sunhill\Framework\Response\ViewResponses\DefaultTileViewResponse;

/**
 * This class is a base class for a feature modules. A feature module is a collection of logical
 * functions (like manipulating an item) and can be mounted into the module tree of the application.
 * 
 * @author klaus
 *
 */
class FeatureModule extends AbstractModule
{
   
    use Children;
    
    protected function doAddSubmodule($module, string $name)
    {
        $this->addChild($module, $name);
    }
    
    public function addSubmodule($module, string $name = '', ?callable $callback = null): FeatureModule
    {
        if (is_string($module) && class_exists($module)) {
            $module = new $module();
        }
        if (is_a($module, FeatureModule::class)) {
            $this->doAddSubmodule($module, $name);
        } else {
            throw new CantProcessModuleException("The passed parameter can't be processed to a module");
        }
        
        if (!is_null($callback)) {
            $callback($module);
        }
        return $module;
    }
    
    public function addResponse($response, string $name = ''): AbstractResponse
    {
        if (is_string($response) && class_exists($response)) {
            $response = new $response();
        }
        if (is_a($response, AbstractResponse::class)) {
            $this->doAddSubmodule($response, $name);
        } else {
            throw new CantProcessResponseException("The passed parameter can't be processed to a response");
        }
        
        return $response;        
    }
    
    public function defaultIndex(): AbstractResponse
    {
        return $this->addIndex(DefaultTileViewResponse::class);
    }
    
    public function addIndex($response): AbstractResponse
    {
        if ((is_string($response) && class_exists($response))) {
            $response = new $response();
        }
        if (!is_a($response, AbstractResponse::class)) {
            throw new CantProcessResponseException("The given parameter can not be processed to a response for index");
        }
        $response->setName('')->setVisible(false)->addRoute($this->getName().'.index');
        return $this->addResponse($response);
    }
    
    /**
     * Returns the breadcrumbs array. Ths array is an associative array which keys are the link 
     * and its values are the description of the module
     * @return string
     */
    public function getBreadcrumbs()
    {
        if ($this->hasOwner()) {
            $result = $this->getOwner()->getBreadcrumbs();
        } else {
            $result = [];
        }
        $result[$this->getPath()] = $this->getDescription();
        return $result;
    }
}
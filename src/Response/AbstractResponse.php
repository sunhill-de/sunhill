<?php
/**
 * @file AbstractResponse.php
 * Provides the basic class for all other responses
 * Lang en
 * Reviewstatus: 2024-04-08
 * Localization:
 * Documentation:
 * Tests:
 * Coverage: unknown
 * PSR-State: complete
 */

namespace Sunhill\Framework\Response;

use Sunhill\Framework\Traits\NameAndDescription;
use Sunhill\Framework\Traits\Owner;
use Illuminate\Support\Facades\Route;

/**
 * A response is a way the framework can respond to a request of any kind
 * 
 * @author klaus
 *
 */
abstract class AbstractResponse
{
    
    /**
     * Provides the properties name and description
     */
    use NameAndDescription;

    use Owner;
    
    protected $visible = false;
    
    public function setVisible(bool $value = true): self
    {
        $this->visible = $value;
        return $this;
    }
    
    public function getVisible(): bool
    {
        return $this->visible;
    }
    
    protected $method = 'get';
    
    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }
    
    public function getMethod(): string
    {
        return $this->method;    
    }
    
    /**
     * Stores the default parameters from the framework
     * 
     * @var array
     */
    protected $parameters = [];
    
    /**
     * Sets the parameters for this response
     * 
     * @param array $parameters
     * @return self
     */
    public function setParameters(array $parameters): self
    {
        $this->parameters = $parameters;
        return $this;
    }
    
    /**
     * Gets the parameters for this response 
     * 
     * @return array
     */
    protected function getParameters(): array
    {
        return $this->parameters;    
    }
    
    public function getBreadcrumbs(): array
    {
        $result = $this->getOwner()->getBreadcrumbs();
        if (!empty($this->getName())) {
            $result[$this->getPath()] = $this->getName();
        }
        return $result;
    }
    
    /**
     * Returns the response
     * 
     * @return unknown
     */
    public function getResponse()
    {
        if ($response = $this->prepareResponse()) {
            return $response;
        }
        return $this->getErrorResponse();    
    }
    
    /**
     * Prepares the response. It either returns a response string of false if something went wrong
     */
    abstract protected function prepareResponse();
    
    protected function getErrorResponse()
    {
        
    }
    
    public function addRoute(string $alias=''): self
    {
        $route = $this->getPath().$this->arguments;
        if ($route == '//') {
            $route = '/';
            if (empty($alias)) {
                $alias = 'mainpage';
            }
        }
        if (empty($alias)) {
            $keys = $this->getHirachy();
            $alias = implode('.', array_keys($keys));
        }
        if (substr($route,-1) == '/') {
            $route = substr($route,0,-1);
        }
        $method = $this->getMethod();
        Route::$method($route, function(...$args) {
            foreach ($args as $key => $value) {
                $method = 'set_'.$key;
                $this->$method($value);
            }
            return $this->getResponse(); 
        })->name($alias);
        return $this;
    }
    
    protected $arguments = '';
    
    public function setArguments(string $arguments): self
    {
        $this->arguments = $arguments;
        return $this;        
    }
}
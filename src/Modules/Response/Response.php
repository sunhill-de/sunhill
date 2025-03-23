<?php
/**
 * @file Response.php
 * Provides the basic class for all other responses
 * Lang en
 * Reviewstatus: 2025-03-22
 * Localization: complete
 * Documentation: complete
 * Tests:
 * Coverage: unknown
 * PSR-State: complete
 */

namespace Sunhill\Modules\Response;

use Sunhill\Modules\Module;
use Illuminate\Support\Facades\Route;
use Sunhill\Exceptions\SunhillUserException;

/**
 * A response is a way the framework can respond to a request of any kind
 *
 * @author klaus
 *
 */
abstract class Response extends Module
{
    
    /**
     * Visible indicates if this response is visible in the menu structure
     * 
     * @var boolean
     */
    protected $visible = false;
    
    /**
     * Responses are always a kind of html respose. Method defines with html verb should be used
     * 
     * @var string
     */
    protected $method = 'get';
    
    /**
     * Stores the default parameters from the framework
     *
     * @var array
     */
    protected $parameters = [];
    
    protected $arguments = '';
    
    /**
     * In case the renderer recognizes an error, this could be displayed here
     * 
     * @var string
     */
    protected string $error_message = '';
    
    /**
     * If not null with this string a return link for the error page could be presented
     * 
     * @var unknown
     */
    protected ?string $return_link = null;
    
    /**
     * Setter for visible
     * 
     * @param bool $value
     * @return static
     */
    public function setVisible(bool $value = true): static
    {
        $this->visible = $value;
        return $this;
    }
    
    /**
     * Getter for visible
     * 
     * @return bool
     */
    public function getVisible(): bool
    {
        return $this->visible;
    }
    
    /**
     * Setter for method
     * 
     * @param string $method
     * @return self
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }
    
    /**
     * Getter for method
     * 
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }
    
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
    
    /**
     * Returns the response
     *
     * @return string
     */
    public function getResponse(): string
    {
        try {
            $response = $this->prepareResponse();
        } catch (SunhillUserException $e) {
            if (empty($this->error_message)) {
                $this->error_message = $e->getMessage();
            }
            $response = false;
        }
        if ($response !== false) {
            return $response;
        }
        return $this->getErrorResponse();
    }
    
    /**
     * Prepares the response. It either returns a response string of false if something went wrong
     */
    abstract protected function prepareResponse(): string|false;
    
    /**
     * An user error occured while running in console.
     * 
     * @param string $message
     */
    protected function getConsoleErrorResponse(string $message)
    {
        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        $out->writeln("There was an user error with following message: ".$message);
        return "";
        
    }
    
    /**
     * An user error occured while running in browser
     * 
     * @param string $message
     * @param string $return_link
     */
    protected function getWebErrorResponse(string $message, ?string $return_link): string
    {
        return view('sunhill:error.usererror', ['error_message'=>$this->error_message,'return_link'=>$this->return_link]);
    }
    
    /**
     * This method detect if we are running in console or in browser and calls the right method
     * 
     */
    protected function getErrorResponse()
    {
        if (app()->runningInConsole()) {
            return $this->getConsoleErrorResponse($this->error_message);
        } else {
            return $this->getWebErrorResponse($this->error_message, $this->return_link);
        }
    }
    
    private function getRoute(): string
    {
        $route = '/'.$this->getParentNames('/').(!empty($this->arguments)?'/'.$this->arguments:'');
        return str_replace('//','/',$route);
    }
    
    private function getAlias(string $alias, string $route): string
    {
        if (!empty($alias)) {
            return $alias;
        }
        if ($route == '/') {
            return 'mainpage';
        }
        $alias = $this->getParentNames('.');
        return ($alias[0] == '.')?substr($alias,1):$alias;
    }
    
    public function addRoute(string $alias=''): static
    {
        $route = $this->getRoute(); 
        $alias = $this->getAlias($alias, $route);
        
        $method = $this->getMethod();
        Route::$method($route, function(...$args) {
            $param_names = explode('/', $this->arguments);
            for ($i=0;$i<count($args);$i++) {
                $method = 'set_'.str_replace(['{','?','}'],'',$param_names[$i]);
                $this->$method($args[$i]);                
            }
            return $this->getResponse();
        })->name($alias);
        return $this;
    }
    
    public function setArguments(string $arguments): static
    {
        $this->arguments = $arguments;
        return $this;
    }
}

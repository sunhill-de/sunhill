<?php

namespace Sunhill\Plugins;

use Sunhill\Plugins\Exceptions\WrongInstallersFormatException;

class Plugin 
{
    
    /**
     * The name of this plugin. Must be unique
     * 
     * @var string
     */
    protected $name = 'no name';
    
    /**
     * Setter for $name
     * 
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Getter for name
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->name;    
    }
    
    /**
     * Author of this plugin
     * 
     * @var string
     */
    protected $autor = 'no author';
    
    /**
     * Setter for $author
     * 
     * @param string $author
     * @return self
     */
    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }
    
    /**
     * Getter for $author
     * 
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;    
    }
    
    /**
     * The version of this plugin
     * 
     * @var string
     */
    protected $version = '0.0.0';
    
    /**
     * Setter for $version
     * 
     * @param string $version
     * @return self
     */
    public function setVersion(string $version): self
    {
        $this->version = $version;
        return $this;
    }
    
    /**
     * Getter for version
     * 
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
    
    /**
     * The state of this plugin
     * 
     * @var string
     */
    protected $state = 'enabled';
    
    /**
     * Setter for state
     * 
     * @param string $state
     * @return self
     */
    public function setState(string $state): self
    {
        $this->state = $state;
        return $this;
    }
    
    /**
     * Getter for state
     * 
     * @return string
     */
    public function getState(): string
    {
       return $this->state; 
    }
    
    /**
     * Array of strings that stores the features of this plugin
     * 
     * @var array
     */
    protected $provides = [];
    
    /**
     * Tests if the given plugin has a certain feature
     * 
     * @param string $feature
     * @return bool
     */
    public function doesProvide(string $feature): bool
    {
        return in_array($feature, $this->provides);    
    }
    
    /**
     * Manually adds a feature to this module, normally this shouldn't be necessary because every pluigin should
     * know what features it provides
     * 
     * @param string $feature
     * @return self
     */
    public function addFeature(string $feature): self
    {
        $this->provides[] = $feature;
        return $this;
    }
    
    public function handle(...$arguments): mixed
    {
        return true;
    }
    
    protected $installers = [];
    
    /**
     * Checks if the installers are in an expected format
     */
    protected function checkInstallers()
    {
        if (!is_array($this->installers)) {
           throw new WrongInstallersFormatException("The installers are not an array");
        }        
    }

    /**
     * Returns all installers that match a given criteria
     * 
     * @param string $relation
     * @param string $version
     * @return unknown[]
     */
    protected function getInstallers(string $relation, string $version)
    {
        $result = [];
        foreach ($this->installers as $inst_version => $installer) {
            if (version_compare($inst_version, $version, $relation)) {
                $result[] = $installer;
            }
        }
        return $result;
    }
    
    /**
     * Executes a list of installers
     * 
     * @param array $list
     */
    protected function executeInstallers(array $list)
    {
        foreach ($list as $installer) {
            if (is_string($installer) && (class_exists($installer))) {
                $installer = new $installer();
            };
            if (is_a($installer, PluginInstaller::class)) {
                $installer->execute();
            } else {
               throw new WrongInstallersFormatException("The given installer is not a PluginInstaller"); 
            }
        }
    }
    
    public function setup()
    {
        
    }
    
    public function boot()
    {
        
    }
    
    public function install()
    {
        $this->checkInstallers();
        $this->executeInstallers($this->getInstallers('=','0'));
    }
    
    public function uninstall()
    {
        $this->checkInstallers();        
        $this->executeInstallers($this->getInstallers('=','-1'));
    }
    
    public function upgrade(string $from)
    {
        $this->checkInstallers();        
        $this->executeInstallers($this->getInstallers('>',$from));
    }

    protected $dependencies = [];
    
    public function getDependencies(): array
    {
        return $this->dependencies;
    }
}
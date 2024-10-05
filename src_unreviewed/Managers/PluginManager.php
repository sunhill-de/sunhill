<?php

namespace Sunhill\Managers;

use Illuminate\Support\Facades\Config;
use Sunhill\Managers\Exceptions\InvalidPlugInException;
use Sunhill\Plugins\Plugin;
use Sunhill\Plugins\PluginQuery;
use Sunhill\Managers\Exceptions\UnmatchedPluginDependencyException;
use Sunhill\Managers\Exceptions\PluginNotFoundException;

class PluginManager 
{

    protected $plugins = [];
    
    protected function loadPlugin(string $path)
    {
        $parts = explode('/', $path);
        $plugin_name = array_pop($parts);
        $plugin_file = $path.'/'.$plugin_name.'.php';
        require($plugin_file);
        $plugin = new $plugin_name();
        if (!is_a($plugin,Plugin::class)) {
            throw new InvalidPlugInException("'$plugin_name' is not a plugin");
        }
        $this->plugins[$plugin->getName()] = $plugin;
    }
    
    protected function loadPluginsFrom(string $dir)
    {
        if (!file_exists($dir)) {
            throw new InvalidPlugInException("The directory '$dir' doesnt exist.");
        }
        if (!is_dir($dir)) {
            throw new InvalidPlugInException("The directory '$dir' is not a dir.");            
        }
        $directory = dir($dir);
        while (($entry = $directory->read()) !== false) {
            if (($entry == '.') || ($entry == '..')) {
                continue;
            }
            if (!is_dir($dir.'/'.$entry)) {
                throw new InvalidPlugInException("Unexpected directory structure in plugin dir");
            }
            $this->loadPlugin($dir.'/'.$entry);
        }
    }
    
    protected function checkDependencies(string $plugin)
    {
        foreach ($this->plugins[$plugin]->getDependencies() as $dependency) {
            if (!isset($this->plugins[$dependency])) {
                throw new UnmatchedPluginDependencyException("The plugin '$plugin' has an unmatched dependeny for '$dependency'");
            }
        }
    }
    
    protected function bootPlugin(string $plugin)
    {
        $this->plugins[$plugin]->boot();
    }
    
    protected function installPlugin(string $name)
    {
        if (!isset($this->known_plugins[$name])) {
            $this->plugins[$name]->install();
            $this->known_plugins[$name] = $this->plugins[$name]->getVersion();
        }
    }
    
    protected function uninstallPlugin(string $name)
    {
        if (isset($this->known_plugins[$name])) {
            $this->plugins[$name]->uninstall();
            unset($this->known_plugins[$name]);
        }
    }
    
    protected function upgradePlugin(string $name)
    {
        if (version_compare($this->known_plugins[$name],$this->plugins[$name]->getVersion(),'<')) {
            $this->plugins[$name]->upgrade($this->known_plugins[$name]);            
        }
    }
    
    protected function checkInstalledPlugins()
    {
        foreach ($this->plugins as $name => $plugin) {
            $this->checkDependencies($name);
            $this->installPlugin($name);
            $this->upgradePlugin($name);
            $this->bootPlugin($name);
        }
    }
    
    protected function checkRemovedPlugins()
    {
        
    }
    
    public function setupPlugins()
    {
        $this->loadPluginsFrom(Config::get('plugin_dir',base_path('/plugins')));
        $this->checkInstalledPlugins();
    }
    
    protected $known_plugins = [];
    
    public function setKnownPlugins(array $plugins): self
    {
        $this->known_plugins = $plugins;
        return $this;
    }
    
    public function getKnownPlugins(): array
    {
        return $this->known_plugins;    
    }
    
    public function query()
    {
        return new PluginQuery(array_values($this->plugins));
    }
    
    /**
     * Just returns all installed plugins
     * 
     * @return array
     */
    public function getPlugins()
    {
        return $this->plugins;
    }
    
    /**
     * Returns the plugin with the name $name or raises an exception when it doesn't exist
     * @param string $name
     * @return Plugin
     */
    public function getPlugin(string $name): Plugin
    {
        if (!isset($this->plugins[$name])) {
            throw new PluginNotFoundException("The plugin '$name' was not found");
        }
        return $this->plugins[$name];
    }
    
    /**
     * Sets the plugins. Mostly for testing purposes
     * 
     * @param array $plugin
     */
    public function setPlugins(array $plugins)
    {
        $this->plugins = $plugins;
    }
}
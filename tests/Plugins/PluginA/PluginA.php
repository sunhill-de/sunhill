<?php

use Sunhill\Framework\Plugins\Plugin;

require(dirname(__FILE__).'/src/ModuleA.php');
require(dirname(__FILE__).'/src/ModuleB.php');

class PluginA extends Plugin
{
    
    public $data = '';
    
    protected $version = '1.0.0';
    
    protected $name = 'PluginA';
    
    protected $dependencies = ['PluginB'];
    
    protected $author = 'John Doe';
    
    public function boot()
    {
        $moduleA = new ModuleA();
        $moduleB = new ModuleB();
        $this->data = 'Boot:'.$moduleA->doSomething().$moduleB->doSomething();
    }
    
    
}
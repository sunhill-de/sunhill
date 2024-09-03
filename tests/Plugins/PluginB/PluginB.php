<?php

use Sunhill\Framework\Plugins\Plugin;

class PluginB extends Plugin
{
    
    public $data = '';
 
    protected $version = '1.0.0';
    
    protected $name = 'PluginB';
    
    protected $dependencies = [];
    
    protected $author = 'Jane Doe';
    
    public function boot()
    {
        $this->data = 'Boot: PluginB';    
    }
}
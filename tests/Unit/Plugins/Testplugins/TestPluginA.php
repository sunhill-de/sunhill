<?php

namespace Sunhill\Tests\Unit\Plugins\Testplugins;

use Sunhill\Plugins\Plugin;

class TestPluginA extends Plugin
{
    
    protected $name = 'TestPluginA';
    
    protected $author = 'Some Author';
    
    protected $version = '0.0.1';
    
    protected $state = 'enabled';
    
    protected $provides = ['marketeer'];
    
    public $installers = [];
}
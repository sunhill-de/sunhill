<?php

use Illuminate\Support\Facades\Config;
use Sunhill\Framework\Facades\Plugins;
use Sunhill\Tests\TestCase;

uses(TestCase::class);


test('boot', function () {
    Config::set('plugin_dir',dirname(__FILE__).'/../Plugins');
    Plugins::setKnownPlugins(['PluginA'=>'1.0.0']);
    Plugins::setupPlugins();
    
    expect(Plugins::getPlugin('PluginA')->data)->toEqual('Boot:ModuleAModuleB');
});

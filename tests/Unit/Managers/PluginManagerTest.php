<?php

namespace Sunhill\Framwork\Tests\Units\Managers;

use Sunhill\Framework\Tests\TestCase;
use Sunhill\Framework\Managers\PluginManager;
use Sunhill\Framework\Plugins\Plugin;
use Sunhill\Framework\Managers\Exceptions\InvalidPlugInException;
use Sunhill\Framework\Plugins\PluginQuery;
use Sunhill\Framework\Managers\Exceptions\UnmatchedPluginDependencyException;
use Sunhill\Framework\Managers\Exceptions\PluginNotFoundException;

uses(TestCase::class);

test('Read plugin directory', function() 
{
    $test = new PluginManager();
    callProtectedMethod($test, 'loadPluginsFrom', [dirname(__FILE__).'/IndepentPlugins']);
    $list = $test->getPlugins();
    $plugins = array_keys($list);
    sort($plugins);
    expect($plugins)->toBe(['ManagerPluginA','ManagerPluginB']);
    expect(is_a($list['ManagerPluginA'],Plugin::class))->toBe(true);
    expect($test->getPlugin('ManagerPluginB')->getName())->toBe('ManagerPluginB');
});

it('fails when accessing an unknown plugin', function()
{
    $test = new PluginManager();
    $test->getPlugin('unknown');  
})->throws(PluginNotFoundException::class);

it('fails when reading wrong named plugin', function()
{
    $test = new PluginManager();
    callProtectedMethod($test, 'loadPluginsFrom', [dirname(__FILE__).'/InmvalidPluginsWrongName']);
})->throws(InvalidPlugInException::class);

it('fails when reading wrong typed plugin', function()
{
    $test = new PluginManager();
    callProtectedMethod($test, 'loadPluginsFrom', [dirname(__FILE__).'/InmvalidPluginsWrongType']);
})->throws(InvalidPlugInException::class);

it('fails when reading wrong plugin dir structure', function()
{
    $test = new PluginManager();
    callProtectedMethod($test, 'loadPluginsFrom', [dirname(__FILE__).'/InmvalidPluginsWrongStructure']);
})->throws(InvalidPlugInException::class);

test('query() calls PluginQuery', function()
{
    $test = new PluginManager();
    expect(is_a($test->query(), PluginQuery::class))->toBe(true);
});

test('Dependencies match', function()
{
   $test = new PluginManager();
   $pluginA = \Mockery::mock(Plugin::class);
   $pluginA->shouldReceive('getName')->andReturn('PluginA');
   $pluginA->shouldReceive('getDependencies')->andReturn(['PluginB']);
   $pluginB = \Mockery::mock(Plugin::class);
   $pluginB->shouldReceive('getName')->andReturn('PluginB');
   $pluginB->shouldReceive('getDependencies')->andReturn(['PluginA']);
   $test->setPlugins(['PluginA'=>$pluginA,'PluginB'=>$pluginB]);
   
   callProtectedMethod($test, 'checkDependencies', ['PluginA']);
   expect(true)->toBe(true); // Sorry, but checkDependecies just must not throw something
});

it('Dependencies mismatch', function()
{
    $test = new PluginManager();
    $pluginA = \Mockery::mock(Plugin::class);
    $pluginA->shouldReceive('getName')->andReturn('PluginA');
    $pluginA->shouldReceive('getDependencies')->andReturn(['PluginC']);
    $pluginB = \Mockery::mock(Plugin::class);
    $pluginB->shouldReceive('getName')->andReturn('PluginB');
    $pluginB->shouldReceive('getDependencies')->andReturn(['PluginA']);
    $test->setPlugins(['PluginA'=>$pluginA,'PluginB'=>$pluginB]);
    
    callProtectedMethod($test, 'checkDependencies', ['PluginA']);
})->throws(UnmatchedPluginDependencyException::class);

test('Boot plugins', function()
{
    $test = new PluginManager();
    $pluginA = \Mockery::mock(Plugin::class);
    $pluginA->shouldReceive('boot')->once();
    $test->setPlugins(['PluginA'=>$pluginA]);

    callProtectedMethod($test, 'bootPlugin', ['PluginA']);
    expect(true)->toBe(true); // Sorry, but checkDependecies just must not throw something
});

test('Install plugin', function()
{
    $test = new PluginManager();
    $test->setKnownPlugins([]);
    
    $plugin = \Mockery::mock(Plugin::class);
    $plugin->shouldReceive('install')->once();
    $plugin->shouldReceive('getVersion')->once()->andReturn('1.0.0');
    $test->setPlugins(['PluginA'=>$plugin]);
    
    callProtectedMethod($test, 'installPlugin',['PluginA']);
    $known = $test->getKnownPlugins();
    expect(isset($known['PluginA']))->toBe(true);
});

test('Install plugin not called when installed', function()
{
    $test = new PluginManager();
    $test->setKnownPlugins(['PluginA'=>'1.0.0']);
    
    $plugin = \Mockery::mock(Plugin::class);
    $plugin->shouldReceive('install')->never();
    $test->setPlugins(['PluginA'=>$plugin]);
    
    callProtectedMethod($test, 'installPlugin',['PluginA']);
});

test('Uninstall plugin', function()
{
    $test = new PluginManager();
    $test->setKnownPlugins(['PluginA'=>'1.0.0']);
    
    $plugin = \Mockery::mock(Plugin::class);
    $plugin->shouldReceive('uninstall')->once();
    $test->setPlugins(['PluginA'=>$plugin]);
    
    callProtectedMethod($test, 'uninstallPlugin',['PluginA']);
    $known = $test->getKnownPlugins();
    expect(isset($known['PluginA']))->toBe(false);
});

test('Uninstall plugin not called when still installed', function()
{
    $test = new PluginManager();
    $test->setKnownPlugins([]);
    
    $plugin = \Mockery::mock(Plugin::class);
    $plugin->shouldReceive('uninstall')->never();
    $test->setPlugins(['PluginA'=>$plugin]);
    
    callProtectedMethod($test, 'uninstallPlugin',['PluginA']);
});

test('Upgrade plugins', function()
{
    $test = new PluginManager(); 
    $test->setKnownPlugins(['PluginA'=>'0.1.0']);
    
    $plugin = \Mockery::mock(Plugin::class);
    $plugin->shouldReceive('getVersion')->once()->andReturn('1.0.0');
    $plugin->shouldReceive('upgrade')->once()->with('0.1.0');
    $test->setPlugins(['PluginA'=>$plugin]);
    
    callProtectedMethod($test, 'upgradePlugin',['PluginA']);
});

test('Upgrade plugins not called when version match', function()
{
    $test = new PluginManager();
    $test->setKnownPlugins(['PluginA'=>'1.0.0']);
    
    $plugin = \Mockery::mock(Plugin::class);
    $plugin->shouldReceive('getVersion')->once()->andReturn('1.0.0');
    $plugin->shouldReceive('upgrade')->never();
    $test->setPlugins(['PluginA'=>$plugin]);
    
    callProtectedMethod($test, 'upgradePlugin',['PluginA']);
});

test('Call all necessary methods when setting plugins up', function()
{
    $test = new PluginManager();
    $test->setKnownPlugins(['PluginA'=>'1.0.0','PluginB'=>'0.1.0']);
    
    $pluginA = \Mockery::mock(Plugin::class);
    $pluginA->shouldReceive('getName')->andReturn('PluginA');
    $pluginA->shouldReceive('getDependencies')->once()->andReturn(['PluginB']);
    $pluginA->shouldReceive('boot')->once();
    $pluginA->shouldReceive('getVersion')->atLeast(1)->andReturn('1.0.0');
    
    $pluginB = \Mockery::mock(Plugin::class);
    $pluginB->shouldReceive('getName')->andReturn('PluginB');
    $pluginB->shouldReceive('getDependencies')->once()->andReturn([]);
    $pluginB->shouldReceive('boot')->once();
    $pluginB->shouldReceive('upgrade')->with('0.1.0')->once();
    $pluginB->shouldReceive('getVersion')->atLeast(1)->andReturn('1.0.0');
    
    $pluginC = \Mockery::mock(Plugin::class);
    $pluginC->shouldReceive('getName')->andReturn('PluginC');
    $pluginC->shouldReceive('getDependencies')->once()->andReturn([]);
    $pluginC->shouldReceive('boot')->once();
    $pluginC->shouldReceive('install')->once();
    $pluginC->shouldReceive('getVersion')->atLeast(1)->andReturn('1.0.0');
    
    $test->setPlugins(['PluginA'=>$pluginA,'PluginB'=>$pluginB,'PluginC'=>$pluginC]);
    
    callProtectedMethod($test, 'checkInstalledPlugins');
});



<?php

namespace Sunhill\Framework\Tests\Unit\Plugins;

use Sunhill\Framework\Tests\TestCase;
use Sunhill\Framework\Tests\Unit\Plugins\Testplugins\TestPluginA;
use Sunhill\Framework\Plugins\PluginInstaller;
use Sunhill\Framework\Plugins\Exceptions\WrongInstallersFormatException;

uses(TestCase::class);

test('Installers is called', function() 
{
    $installer = \Mockery::mock(PluginInstaller::class);
    $installer->shouldReceive('execute')->once();
    $uninstaller = \Mockery::mock(PluginInstaller::class);
    $uninstaller->shouldReceive('execute')->never();
    $upgrader1 = \Mockery::mock(PluginInstaller::class);
    $upgrader1->shouldReceive('execute')->never();
    $upgrader2 = \Mockery::mock(PluginInstaller::class);
    $upgrader2->shouldReceive('execute')->never();
    $upgrader3 = \Mockery::mock(PluginInstaller::class);
    $upgrader3->shouldReceive('execute')->never();
        
    $test = new TestPluginA();
    $test->installers = [
        '0'=>$installer,
        '0.0.1'=>$upgrader1,
        '0.1.1'=>$upgrader2,
        '1.0.0'=>$upgrader3,
        '-1'=>$uninstaller];
    $test->install();
});

test('Uninstaller is called', function()
{
    $installer = \Mockery::mock(PluginInstaller::class);
    $installer->shouldReceive('execute')->never();
    $uninstaller = \Mockery::mock(PluginInstaller::class);
    $uninstaller->shouldReceive('execute')->once();
    $upgrader1 = \Mockery::mock(PluginInstaller::class);
    $upgrader1->shouldReceive('execute')->never();
    $upgrader2 = \Mockery::mock(PluginInstaller::class);
    $upgrader2->shouldReceive('execute')->never();
    $upgrader3 = \Mockery::mock(PluginInstaller::class);
    $upgrader3->shouldReceive('execute')->never();
    
    $test = new TestPluginA();
    $test->installers = [
        '0'=>$installer,
        '0.0.2'=>$upgrader1,
        '0.1.1'=>$upgrader2,
        '1.0.0'=>$upgrader3,
        '-1'=>$uninstaller];
    $test->uninstall();
});

test('Upgraders are called', function($u1,$u2,$u3,$version)
{
    $installer = \Mockery::mock(PluginInstaller::class);
    $installer->shouldReceive('execute')->never();
    $uninstaller = \Mockery::mock(PluginInstaller::class);
    $uninstaller->shouldReceive('execute')->never();
    
    $upgrader1 = \Mockery::mock(PluginInstaller::class);
    if ($u1) {
        $upgrader1->shouldReceive('execute')->once();
    } else {
        $upgrader1->shouldReceive('execute')->never();        
    }
    $upgrader2 = \Mockery::mock(PluginInstaller::class);
    if ($u2) {
        $upgrader2->shouldReceive('execute')->once();
    } else {
        $upgrader2->shouldReceive('execute')->never();
    }
    $upgrader3 = \Mockery::mock(PluginInstaller::class);
    if ($u3) {
        $upgrader3->shouldReceive('execute')->once();
    } else {
        $upgrader3->shouldReceive('execute')->never();
    }
    
    $test = new TestPluginA();
    $test->installers = [
        '0'=>$installer,
        '0.0.1'=>$upgrader1,
        '0.1.1'=>$upgrader2,
        '1.0.0'=>$upgrader3,
        '-1'=>$uninstaller];
    $test->upgrade($version);    
})->with(
    [
        [false,true,true,'0.0.1'],
        [false,true,true,'0.0.2'],
        [false,true,true,'0.1.0'],
        [false,false,true,'0.1.1'],
    ]);

it('Raises exception when installers are not an array', function() {
    $test = new TestPluginA();
    $test->installers = 'boo';
    $test->install();
})->throws(WrongInstallersFormatException::class);

it('Raises exception when a installer is not a PluginInstaller', function() {
    $test = new TestPluginA();
    $installer = new \StdClass();
    $test->installers = [$installer];
    $test->install();    
})->throws(WrongInstallersFormatException::class);
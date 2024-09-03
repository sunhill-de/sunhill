<?php

namespace Sunhill\Tests\Unit\Plugins;

use Sunhill\Tests\TestCase;
use Sunhill\Tests\Unit\Plugins\Testplugins\TestPluginA;

uses(TestCase::class);

test('Plugin doesProvide()', function()
{
    $test = new TestPluginA();
    
    expect($test->doesProvide('marketeer'))->toBe(true);
    expect($test->doesProvide('notafeature'))->toBe(false);    
});

test('Plugin addFeature() works', function()
{
    $test = new TestPluginA();
    $test->addFeature('testfeature');
    expect($test->doesProvide('testfeature'))->toBe(true);    
});

test('Plugin name functions', function()
{
    $test = new TestPluginA();
    $test->setName('TestName');
    
    expect($test->getName())->toBe('TestName');
});

test('Plugin author functions', function() {
    $test = new TestPluginA();
    $test->setAuthor('TestName');
    
    expect($test->getAuthor())->toBe('TestName');    
});
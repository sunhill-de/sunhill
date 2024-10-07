<?php

/*
 * Tests src/Filters/Filter.php
 */

use Sunhill\Filter\FilterContainer;
use Sunhill\Tests\TestCase;
use Sunhill\Filter\Filter;

uses(TestCase::class);

test('Filter match', function() 
{
    $container = \Mockery::mock(FilterContainer::class);
    $container->shouldReceive('hasCondition')->once()->with('something')->andReturn(true);
    $container->shouldReceive('getCondition')->once()->with('something')->andReturn(true);
    
    $test = new \Sunhill\Filter\Filter();
    $test->setContainer($container);
    $test::clearConditions();
    $test::addCondition('something', true);
    
    expect($test->matches($container))->toBe(true);
});

test('Filter match many', function()
{
    $container = \Mockery::mock(FilterContainer::class);
    $container->shouldReceive('hasCondition')->once()->with('something')->andReturn(true);
    $container->shouldReceive('hasCondition')->once()->with('somethingelse')->andReturn(true);
    $container->shouldReceive('getCondition')->once()->with('something')->andReturn(true);
    $container->shouldReceive('getCondition')->once()->with('somethingelse')->andReturn('ABC');
    
    $test = new Filter();
    $test->setContainer($container);
    $test::clearConditions();
    $test::addCondition('something', true);
    $test::addCondition('somethingelse', 'ABC');
    
    expect($test->matches())->toBe(true);
});

test('Filter fails', function()
{
    $container = \Mockery::mock(FilterContainer::class);
    $container->shouldReceive('getCondition')->once()->with('something')->andReturn(false);
    $container->shouldReceive('hasCondition')->once()->with('something')->andReturn(true);
    
    $test = new Filter();
    $test->setContainer($container);
    $test::clearConditions();
    $test::addCondition('something', true);
    
    expect($test->matches())->toBe(false);    
});

test('Filter match alternative (match all)', function()
{
    $container = \Mockery::mock(FilterContainer::class);
    $container->shouldReceive('getCondition')->once()->with('something')->andReturn(true);
    $container->shouldReceive('hasCondition')->once()->with('something')->andReturn(true);
    $container->shouldReceive('getCondition')->never()->with('somethingelse')->andReturn('ABC');
    $container->shouldReceive('hasCondition')->never()->with('somethingelse')->andReturn(true);
    
    $test = new Filter();
    $test->setContainer($container);
    $test::clearConditions();
    $test::addAlternativeCondition(['something'=>true,'somethingelse'=>'ABC']);
    
    expect($test->matches())->toBe(true);    
});

test('Filter match alternative (match first)', function()
{
    $container = \Mockery::mock(FilterContainer::class);
    $container->shouldReceive('getCondition')->once()->with('something')->andReturn(true);
    $container->shouldReceive('hasCondition')->once()->with('something')->andReturn(true);
    $container->shouldReceive('getCondition')->never()->with('somethingelse')->andReturn('DEF');
    $container->shouldReceive('hasCondition')->never()->with('somethingelse')->andReturn(true);
    
    $test = new Filter();
    $test->setContainer($container);
    $test::clearConditions();
    $test::addAlternativeCondition(['something'=>true,'somethingelse'=>'ABC']);
    
    expect($test->matches())->toBe(true);
});

test('Filter match alternative (match last)', function()
{
    $container = \Mockery::mock(FilterContainer::class);
    $container->shouldReceive('getCondition')->once()->with('something')->andReturn(false);
    $container->shouldReceive('hasCondition')->once()->with('something')->andReturn(true);
    $container->shouldReceive('getCondition')->once()->with('somethingelse')->andReturn('ABC');
    $container->shouldReceive('hasCondition')->once()->with('somethingelse')->andReturn(true);
    
    $test = new Filter();
    $test->setContainer($container);
    $test::clearConditions();
    $test::addAlternativeCondition(['something'=>true,'somethingelse'=>'ABC']);
    
    expect($test->matches())->toBe(true);
});

test('Filter match alternative (match none)', function()
{
    $container = \Mockery::mock(FilterContainer::class);
    $container->shouldReceive('getCondition')->once()->with('something')->andReturn(false);
    $container->shouldReceive('hasCondition')->once()->with('something')->andReturn(true);
    $container->shouldReceive('getCondition')->once()->with('somethingelse')->andReturn('DEF');
    $container->shouldReceive('hasCondition')->once()->with('somethingelse')->andReturn(true);
    
    $test = new Filter();
    $test->setContainer($container);
    $test::clearConditions();
    $test::addAlternativeCondition(['something'=>true,'somethingelse'=>'ABC']);
    
    expect($test->matches())->toBe(false);
});


test('Filter fails many', function()
{
    $container = \Mockery::mock(FilterContainer::class);
    $container->shouldReceive('getCondition')->once()->with('something')->andReturn(true);
    $container->shouldReceive('hasCondition')->once()->with('something')->andReturn(true);
    $container->shouldReceive('getCondition')->once()->with('somethingelse')->andReturn('DEF');
    $container->shouldReceive('hasCondition')->once()->with('somethingelse')->andReturn(true);
    
    $test = new Filter();
    $test->setContainer($container);
    $test::clearConditions();
    $test::addCondition('something', true);
    $test::addCondition('somethingelse', 'ABC');
    
    expect($test->matches())->toBe(false);
});

test('static access works', function()
{
   $test = new Filter();
   expect($test->getGroup())->toBe('');
   expect($test->getPriority())->toBe(50);
});
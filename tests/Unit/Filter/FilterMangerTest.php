<?php

/*
 * Tests src/Managers/FilterManager.php
 */

use Sunhill\Filter\Filter;
use Sunhill\Filter\FilterException;
use Sunhill\Filter\FilterContainer;
use Sunhill\Tests\TestCase;
use Sunhill\Managers\FilterManager;
use Sunhill\Tests\SunhillTestCase;

uses(SunhillTestCase::class);

test('get grouped filters works', function()
{
    $test = new FilterManager();
    $filter1 = \Mockery::mock(Filter::class);
    $filter1->shouldReceive('getGroup')->andReturn('groupA');
    $filter1->shouldReceive('getPriority')->andReturn(80);
    $filter2 = \Mockery::mock(Filter::class);
    $filter2->shouldReceive('getGroup')->andReturn('groupB');
    $filter2->shouldReceive('getPriority')->andReturn(80);
    $filter3 = \Mockery::mock(Filter::class);
    $filter3->shouldReceive('getGroup')->andReturn('groupA');
    $filter3->shouldReceive('getPriority')->andReturn(20);
    $test->addFilters([$filter1,$filter2,$filter3]);
    
    $list = $test->getFiltersByGroup('groupA');
    expect(count($list))->toBe(2);
    expect($list[0]->getPriority())->toBe(20);
    expect($list[1]->getPriority())->toBe(80);
});

test('get grouped filters works with mixed addFilter arguments', function()
{
    $test = new FilterManager();
    $filter1 = \Mockery::mock(Filter::class);
    $filter1->shouldReceive('getGroup')->andReturn('groupA');
    $filter1->shouldReceive('getPriority')->andReturn(80);
    $filter2 = \Mockery::mock(Filter::class);
    $filter2->shouldReceive('getGroup')->andReturn('groupB');
    $filter2->shouldReceive('getPriority')->andReturn(80);
    $filter3 = \Mockery::mock(Filter::class);
    $filter3->shouldReceive('getGroup')->andReturn('groupA');
    $filter3->shouldReceive('getPriority')->andReturn(20);
    $test->addFilters([$filter1,$filter2]);
    $test->addFilters($filter3);
    
    $list = $test->getFiltersByGroup('groupA');
    expect(count($list))->toBe(2);
    expect($list[0]->getPriority())->toBe(20);
    expect($list[1]->getPriority())->toBe(80);
});

test('addFilter fails with unknown filter', function()
{
    $test = new FilterManager();
    $filter = 5;
    $test->addFilters($filter);
})->throws(FilterException::class);

test('execute filter list with sufficient', function() 
{
    $test = new FilterManager();

    $filter1 = \Mockery::mock(Filter::class);
    $filter1->shouldReceive('execute')->once()->andReturn('CONTINUE');
    $filter1->shouldReceive('matches')->once()->andReturn(true);
    $filter1->shouldReceive('setContainer');
    $filter2 = \Mockery::mock(Filter::class);
    $filter2->shouldReceive('execute')->once()->andReturn('SUFFICIENT');
    $filter2->shouldReceive('setContainer');
    $filter2->shouldReceive('matches')->once()->andReturn(true);
    $filter3 = \Mockery::mock(Filter::class);
    $filter3->shouldReceive('execute')->once()->andReturn('CONTINUE');
    $filter3->shouldReceive('setContainer');
    $filter3->shouldReceive('matches')->once()->andReturn(true);
    
    $list = [$filter1,$filter2,$filter3];
    $container = \Mockery::mock(FilterContainer::class);
    
    expect($test->executeFilters($list, $container))->toBe('SUCCESS');
});

test('execute filter list without sufficient', function()
{
    $test = new FilterManager();

    $filter1 = \Mockery::mock(Filter::class);
    $filter1->shouldReceive('execute')->once()->andReturn('CONTINUE');
    $filter1->shouldReceive('matches')->once()->andReturn(true);
    $filter1->shouldReceive('setContainer');
    $filter2 = \Mockery::mock(Filter::class);
    $filter2->shouldReceive('execute')->once()->andReturn('CONTINUE');
    $filter2->shouldReceive('matches')->once()->andReturn(true);
    $filter2->shouldReceive('setContainer');
    $filter3 = \Mockery::mock(Filter::class);
    $filter3->shouldReceive('execute')->once()->andReturn('CONTINUE');
    $filter3->shouldReceive('matches')->once()->andReturn(true);
    $filter3->shouldReceive('setContainer');
    
    $list = [$filter1,$filter2,$filter3];
    $container = \Mockery::mock(FilterContainer::class);
    
    expect($test->executeFilters($list, $container))->toBe('INSUFFICIENT');
});

test('execute filter list with stop and sufficient', function()
{
    $test = new FilterManager();
    
    $filter1 = \Mockery::mock(Filter::class);
    $filter1->shouldReceive('execute')->once()->andReturn('SUFFICIENT');
    $filter1->shouldReceive('matches')->once()->andReturn(true);
    $filter1->shouldReceive('setContainer');
    $filter2 = \Mockery::mock(Filter::class);
    $filter2->shouldReceive('execute')->once()->andReturn('STOP');
    $filter2->shouldReceive('matches')->once()->andReturn(true);
    $filter2->shouldReceive('setContainer');
    $filter3 = \Mockery::mock(Filter::class);
    $filter3->shouldReceive('execute')->never();
    $filter3->shouldReceive('matches')->never();
    
    $list = [$filter1,$filter2,$filter3];
    $container = \Mockery::mock(FilterContainer::class);
    
    expect($test->executeFilters($list, $container))->toBe('SUCCESS');
});

test('execute filter list with stop and without sufficient', function()
{
    $test = new FilterManager();
    
    $filter1 = \Mockery::mock(Filter::class);
    $filter1->shouldReceive('execute')->once()->andReturn('CONTINUE');
    $filter1->shouldReceive('matches')->once()->andReturn(true);
    $filter1->shouldReceive('setContainer');
    $filter2 = \Mockery::mock(Filter::class);
    $filter2->shouldReceive('execute')->once()->andReturn('STOP');
    $filter2->shouldReceive('matches')->once()->andReturn(true);
    $filter2->shouldReceive('setContainer');
    $filter3 = \Mockery::mock(Filter::class);
    $filter3->shouldReceive('execute')->never();
    $filter3->shouldReceive('matches')->never();
    
    $list = [$filter1,$filter2,$filter3];
    $container = \Mockery::mock(FilterContainer::class);
    
    expect($test->executeFilters($list, $container))->toBe('INSUFFICIENT');
});


test('execute filter list with failure', function()
{
    $test = new FilterManager();
    
    $filter1 = \Mockery::mock(Filter::class);
    $filter1->shouldReceive('execute')->once()->andReturn('CONTINUE');
    $filter1->shouldReceive('matches')->once()->andReturn(true);
    $filter1->shouldReceive('setContainer');
    $filter2 = \Mockery::mock(Filter::class);
    $filter2->shouldReceive('execute')->once()->andReturn('FAILURE');
    $filter2->shouldReceive('matches')->once()->andReturn(true);
    $filter2->shouldReceive('setContainer');
    $filter3 = \Mockery::mock(Filter::class);
    $filter3->shouldReceive('execute')->never();
    $filter3->shouldReceive('matches')->never();
    
    $list = [$filter1,$filter2,$filter3];
    $container = \Mockery::mock(FilterContainer::class);
    
    expect($test->executeFilters($list, $container))->toBe('FAILURE');
});

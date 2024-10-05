<?php


use Sunhill\Facades\Filters;
use Sunhill\Tests\Feature\Filter\Filters\GroupAFilter;
use Sunhill\Tests\Feature\Filter\Filters\GroupB_60Filter;
use Sunhill\Tests\Feature\Filter\Filters\GroupB_50Filter;
use Sunhill\Tests\Feature\Filter\Filters\GroupB_40Filter;
use Sunhill\Tests\Feature\Filter\Filters\GroupB_30Filter;
use Sunhill\Tests\Feature\Filter\Filters\GroupB_25Filter;
use Sunhill\Tests\Feature\Filter\Filters\GroupB_20Filter;
use Sunhill\Tests\Feature\Filter\Filters\GroupB_10Filter;
use Sunhill\Filter\FilterContainer;
use Sunhill\Tests\TestCase;

uses(TestCase::class);
/**
 * Name            | Action       | Condition
 * ----------------+--------------+-----------
 * GroupB_10Filter | CONTINUE     |
 * GroupB_20Filter | STOP         |
 * GroupB_30Filter | SUFFICIENT   |
 * GroupB_40Filter | STOP         |
 * GroupB_50Filter | CONTINUE     |
 * GroupB_60Filter | CONTINUE     | additional = ABC
 */
test('Choose the right filters', function() 
{
    Filters::clearFilters();
    Filters::addFilters([
        GroupAFilter::class,
        GroupB_60Filter::class,
        GroupB_50Filter::class,
        GroupB_40Filter::class,
        GroupB_30Filter::class,
        GroupB_25Filter::class,
        GroupB_20Filter::class,
        GroupB_10Filter::class,
    ]);
    $container = new FilterContainer();
    $container->setCondition('condition_10',true);
    $container->setCondition('groupA','');
    $container->setCondition('groupB','');
    
    Filters::execute('GroupB', $container);
    
    expect($container->getCondition('groupA'))->toBe('');
});

test('Sufficient execution', function()
{
    Filters::clearFilters();
    Filters::addFilters([
        GroupAFilter::class,
        GroupB_60Filter::class,
        GroupB_50Filter::class,
        GroupB_40Filter::class,
        GroupB_30Filter::class,
        GroupB_25Filter::class,
        GroupB_20Filter::class,
        GroupB_10Filter::class,
    ]);
    $container = new FilterContainer();
    $container->setCondition('condition_10',true);
    $container->setCondition('condition_20',false);
    $container->setCondition('condition_25',false);
    $container->setCondition('condition_30',true);
    $container->setCondition('condition_40',false);
    $container->setCondition('condition_50',true);
    $container->setCondition('condition_60',false);
    $container->setCondition('additional','');
    $container->setCondition('groupA','');
    $container->setCondition('groupB','');    
    
    $result = Filters::execute('GroupB', $container);
    
    expect($result)->toBe('SUCCESS');
    expect($container->getCondition('groupB'))->toBe('_10_30_50');
});

test('Stop before sufficient execution', function()
{
    Filters::clearFilters();
    Filters::addFilters([
        GroupAFilter::class,
        GroupB_60Filter::class,
        GroupB_50Filter::class,
        GroupB_40Filter::class,
        GroupB_30Filter::class,
        GroupB_25Filter::class,
        GroupB_20Filter::class,
        GroupB_10Filter::class,
    ]);
    
    $container = new FilterContainer();
    $container->setCondition('condition_10',true);
    $container->setCondition('condition_20',true);
    $container->setCondition('condition_25',false);
    $container->setCondition('condition_30',true);
    $container->setCondition('condition_40',false);
    $container->setCondition('condition_50',true);
    $container->setCondition('condition_60',false);
    $container->setCondition('additional','');
    $container->setCondition('groupA','');
    $container->setCondition('groupB','');
    
    $result = Filters::execute('GroupB', $container);
    
    expect($result)->toBe('INSUFFICIENT');
    expect($container->getCondition('groupB'))->toBe('_10_20');
});

test('Stop after sufficient execution', function()
{
    Filters::clearFilters();
    Filters::addFilters([
        GroupAFilter::class,
        GroupB_60Filter::class,
        GroupB_50Filter::class,
        GroupB_40Filter::class,
        GroupB_30Filter::class,
        GroupB_25Filter::class,
        GroupB_20Filter::class,
        GroupB_10Filter::class,
    ]);
    
    $container = new FilterContainer();
    $container->setCondition('condition_10',true);
    $container->setCondition('condition_20',false);
    $container->setCondition('condition_25',false);
    $container->setCondition('condition_30',true);
    $container->setCondition('condition_40',true);
    $container->setCondition('condition_50',true);
    $container->setCondition('condition_60',false);
    $container->setCondition('additional','');
    $container->setCondition('groupA','');
    $container->setCondition('groupB','');
    
    $result = Filters::execute('GroupB', $container);
    
    expect($result)->toBe('SUCCESS');
    expect($container->getCondition('groupB'))->toBe('_10_30_40');
});

test('sufficent with additional condition execution', function()
{
    Filters::clearFilters();
    Filters::addFilters([
        GroupAFilter::class,
        GroupB_60Filter::class,
        GroupB_50Filter::class,
        GroupB_40Filter::class,
        GroupB_30Filter::class,
        GroupB_25Filter::class,
        GroupB_20Filter::class,
        GroupB_10Filter::class,
    ]);
    
    $container = new FilterContainer();
    $container->setCondition('condition_10',true);
    $container->setCondition('condition_20',false);
    $container->setCondition('condition_25',false);
    $container->setCondition('condition_30',true);
    $container->setCondition('condition_40',false);
    $container->setCondition('condition_50',true);
    $container->setCondition('condition_60',true);
    $container->setCondition('additional','ABC');
    $container->setCondition('groupA','');
    $container->setCondition('groupB','');
    
    $result = Filters::execute('GroupB', $container);
    
    expect($result)->toBe('SUCCESS');
    expect($container->getCondition('groupB'))->toBe('_10_30_50_60');
});

test('sufficent without additional condition execution', function()
{
    Filters::clearFilters();
    Filters::addFilters([
        GroupAFilter::class,
        GroupB_60Filter::class,
        GroupB_50Filter::class,
        GroupB_40Filter::class,
        GroupB_30Filter::class,
        GroupB_25Filter::class,
        GroupB_20Filter::class,
        GroupB_10Filter::class,
    ]);
    
    $container = new FilterContainer();
    $container->setCondition('condition_10',true);
    $container->setCondition('condition_20',false);
    $container->setCondition('condition_25',false);
    $container->setCondition('condition_30',true);
    $container->setCondition('condition_40',false);
    $container->setCondition('condition_50',true);
    $container->setCondition('condition_60',true);
    $container->setCondition('additional','DEF');
    $container->setCondition('groupA','');
    $container->setCondition('groupB','');
    
    $result = Filters::execute('GroupB', $container);
    
    expect($result)->toBe('SUCCESS');
    expect($container->getCondition('groupB'))->toBe('_10_30_50');
});


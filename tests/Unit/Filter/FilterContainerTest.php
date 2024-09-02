<?php

use Sunhill\Filter\FilterContainer;
use Sunhill\Tests\TestCase;

uses(TestCase::class);

test('setCondition() and getCondition() works with scalar', function()
{
    $test = new FilterContainer();
    $test->setCondition('something',5);
    expect($test->getCondition('something'))->toBe(5);
});

test('setCondition() and getCondition() works with default', function()
{
    $test = new FilterContainer();
    $test->setCondition('something');
    expect($test->getCondition('something'))->toBe(true);
});

test('set_something() and get_something() works with callback', function()
{
    $test = new FilterContainer();
    $test->setCondition('something',function($file)
    {
        return 5;
    });
    expect($test->getCondition('something'))->toBe(5);
});


<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tests\Feature\Properties\PooledRecordProperty\Examples\IncludeParentProperty;
use Sunhill\Tests\Feature\Properties\PooledRecordProperty\Examples\IncludeChildProperty;
use Sunhill\Tests\Feature\Properties\PooledRecordProperty\Examples\EmbedParentProperty;
use Sunhill\Tests\Feature\Properties\PooledRecordProperty\Examples\EmbedChildProperty;

uses(SunhillTestCase::class);

test('load included parent', function()
{
   $test = new IncludeParentProperty();
   $test->load(1);
   
   expect($test->parent_str)->toBe('BBB');
});

test('load included child', function()
{
    $test = new IncludeChildProperty();
    $test->load(1);
    
    expect($test->parent_str)->toBe('BCE');
    expect($test->child_str)->toBe('BCB');
});

test('load embedded parent', function()
{
    $test = new EmbedParentProperty();
    $test->load(1);
    
    expect($test->parent_str)->toBe('BBB');
});

test('load embedded child', function()
{
    $test = new EmbedChildProperty();
    $test->load(1);
    
    expect($test->parent_str)->toBe('BBB');
    expect($test->child_str)->toBe('BCB');
});
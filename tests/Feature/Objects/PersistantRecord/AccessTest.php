<?php

use Sunhill\Properties\Tests\TestCase;
use Sunhill\Properties\Tests\Feature\Objects\PersistantRecord\Samples\ParentRecord;
use Sunhill\Properties\Tests\Feature\Objects\PersistantRecord\Samples\ChildRecord;
use Sunhill\Properties\Tests\Feature\Objects\PersistantRecord\Samples\EmptyChildRecord;
use Sunhill\Properties\Tests\Feature\Objects\PersistantRecord\Samples\GrandChildRecord;
use Sunhill\Properties\Tests\Feature\Objects\PersistantRecord\Samples\EmptyGrandChildRecord;

uses(TestCase::class);

test('accessing parent class', function()
{
    $test = new ParentRecord();
    $test->parentint = 123;
    $test->parentvarchar = 'ABC';
    
    expect($test->parentint)->toBe(123);
    expect($test->parentvarchar)->toBe('ABC');
});

test('accessing child class with include', function()
{
    ParentRecord::$handle_inheritance = 'include';
    $test = new ChildRecord();
    $test->parentint = 123;
    $test->parentvarchar = 'ABC';
    $test->childint = 234;
    $test->childvarchar = 'DEF';

    expect($test->parentint)->toBe(123);
    expect($test->parentvarchar)->toBe('ABC');
    expect($test->childint)->toBe(234);
    expect($test->childvarchar)->toBe('DEF');
    
});

test('accessing empty child class with include', function()
{
    ParentRecord::$handle_inheritance = 'include';
    $test = new EmptyChildRecord();
    $test->parentint = 123;
    $test->parentvarchar = 'ABC';

    expect($test->parentint)->toBe(123);
    expect($test->parentvarchar)->toBe('ABC');
});

test('acessing grandchild class with include', function()
{
    ParentRecord::$handle_inheritance = 'include';
    $test = new GrandChildRecord();
    $test->parentint = 123;
    $test->parentvarchar = 'ABC';
    $test->childint = 234;
    $test->childvarchar = 'DEF';
    $test->grandchildint = 345;
    $test->grandchildvarchar = 'GHI';
    
    expect($test->parentint)->toBe(123);
    expect($test->parentvarchar)->toBe('ABC');
    expect($test->childint)->toBe(234);
    expect($test->childvarchar)->toBe('DEF');
    expect($test->grandchildint)->toBe(345);
    expect($test->grandchildvarchar)->toBe('GHI');
});

test('setup an empty grandchild class having elements with include', function()
{
    ParentRecord::$handle_inheritance = 'include';
    $test = new EmptyGrandChildRecord();
    $test->parentint = 123;
    $test->parentvarchar = 'ABC';
    $test->grandchildint = 345;
    $test->grandchildvarchar = 'GHI';
    
    expect($test->parentint)->toBe(123);
    expect($test->parentvarchar)->toBe('ABC');
    expect($test->grandchildint)->toBe(345);
    expect($test->grandchildvarchar)->toBe('GHI');
});

test('setup a child class having elements with embed', function()
{
    ParentRecord::$handle_inheritance = 'embed';
    $test = new ChildRecord();
    $test->parentint = 123;
    $test->parentvarchar = 'ABC';
    $test->childint = 234;
    $test->childvarchar = 'DEF';
    
    expect($test->parentint)->toBe(123);
    expect($test->parentvarchar)->toBe('ABC');
    expect($test->childint)->toBe(234);
    expect($test->childvarchar)->toBe('DEF');
});

test('setup a empty child class having elements with embed', function()
{
    ParentRecord::$handle_inheritance = 'embed';
    $test = new EmptyChildRecord();
    $test->parentint = 123;
    $test->parentvarchar = 'ABC';
    
    expect($test->parentint)->toBe(123);
    expect($test->parentvarchar)->toBe('ABC');
});

test('setup a grandchild class having elements with embed', function()
{
    ParentRecord::$handle_inheritance = 'embed';
    $test = new GrandChildRecord();
    $test->parentint = 123;
    $test->parentvarchar = 'ABC';
    $test->childint = 234;
    $test->childvarchar = 'DEF';
    $test->grandchildint = 345;
    $test->grandchildvarchar = 'GHI';
    
    expect($test->parentint)->toBe(123);
    expect($test->parentvarchar)->toBe('ABC');
    expect($test->childint)->toBe(234);
    expect($test->childvarchar)->toBe('DEF');
    expect($test->grandchildint)->toBe(345);
    expect($test->grandchildvarchar)->toBe('GHI');
});

test('setup an empty grandchild class having elements with embed', function()
{
    ParentRecord::$handle_inheritance = 'embed';
    $test = new EmptyGrandChildRecord();
    $test->parentint = 123;
    $test->parentvarchar = 'ABC';
    $test->grandchildint = 345;
    $test->grandchildvarchar = 'GHI';
    
    expect($test->parentint)->toBe(123);
    expect($test->parentvarchar)->toBe('ABC');
    expect($test->grandchildint)->toBe(345);
    expect($test->grandchildvarchar)->toBe('GHI');
});


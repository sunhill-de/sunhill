<?php

use Sunhill\Properties\Tests\TestCase;
use Sunhill\Properties\Tests\Feature\Objects\PersistantRecord\Samples\ParentRecord;
use Sunhill\Properties\Tests\Feature\Objects\PersistantRecord\Samples\ChildRecord;
use Sunhill\Properties\Tests\Feature\Objects\PersistantRecord\Samples\EmptyChildRecord;
use Sunhill\Properties\Tests\Feature\Objects\PersistantRecord\Samples\GrandChildRecord;
use Sunhill\Properties\Tests\Feature\Objects\PersistantRecord\Samples\EmptyGrandChildRecord;

uses(TestCase::class);

test('setup a parent class having elements', function()
{
    $test = new ParentRecord();
    expect($test->hasElement('parentint'))->toBe(true);
    expect($test->hasElement('parentvarchar'))->toBe(true);
});

test('setup a child class having elements with include', function()
{
    ParentRecord::$handle_inheritance = 'include';
    $test = new ChildRecord();
    expect($test->hasElement('parentint'))->toBe(true);
    expect($test->hasElement('parentvarchar'))->toBe(true);
    expect($test->hasElement('childint'))->toBe(true);
    expect($test->hasElement('childvarchar'))->toBe(true);
});

test('setup a empty child class having elements with include', function()
{
    ParentRecord::$handle_inheritance = 'include';
    $test = new EmptyChildRecord();
    expect($test->hasElement('parentint'))->toBe(true);
    expect($test->hasElement('parentvarchar'))->toBe(true);
});

test('setup a grandchild class having elements with include', function()
{
    ParentRecord::$handle_inheritance = 'include';
    $test = new GrandChildRecord();
    expect($test->hasElement('parentint'))->toBe(true);
    expect($test->hasElement('parentvarchar'))->toBe(true);
    expect($test->hasElement('childint'))->toBe(true);
    expect($test->hasElement('childvarchar'))->toBe(true);
    expect($test->hasElement('grandchildint'))->toBe(true);
    expect($test->hasElement('grandchildvarchar'))->toBe(true);
});

test('setup an empty grandchild class having elements with include', function()
{
    ParentRecord::$handle_inheritance = 'include';
    $test = new EmptyGrandChildRecord();
    expect($test->hasElement('parentint'))->toBe(true);
    expect($test->hasElement('parentvarchar'))->toBe(true);
    expect($test->hasElement('childint'))->toBe(false);
    expect($test->hasElement('childvarchar'))->toBe(false);
    expect($test->hasElement('grandchildint'))->toBe(true);
    expect($test->hasElement('grandchildvarchar'))->toBe(true);
});

test('setup a child class having elements with embed', function()
{
    ParentRecord::$handle_inheritance = 'embed';
    $test = new ChildRecord();
    expect($test->hasElement('parentint'))->toBe(true);
    expect($test->hasElement('parentvarchar'))->toBe(true);
    expect($test->hasElement('childint'))->toBe(true);
    expect($test->hasElement('childvarchar'))->toBe(true);
});

test('setup a empty child class having elements with embed', function()
{
    ParentRecord::$handle_inheritance = 'embed';
    $test = new EmptyChildRecord();
    expect($test->hasElement('parentint'))->toBe(true);
    expect($test->hasElement('parentvarchar'))->toBe(true);
});

test('setup a grandchild class having elements with embed', function()
{
    ParentRecord::$handle_inheritance = 'embed';
    $test = new GrandChildRecord();
    expect($test->hasElement('parentint'))->toBe(true);
    expect($test->hasElement('parentvarchar'))->toBe(true);
    expect($test->hasElement('childint'))->toBe(true);
    expect($test->hasElement('childvarchar'))->toBe(true);
    expect($test->hasElement('grandchildint'))->toBe(true);
    expect($test->hasElement('grandchildvarchar'))->toBe(true);
});

test('setup an empty grandchild class having elements with embed', function()
{
    ParentRecord::$handle_inheritance = 'embed';
    $test = new EmptyGrandChildRecord();
    expect($test->hasElement('parentint'))->toBe(true);
    expect($test->hasElement('parentvarchar'))->toBe(true);
    expect($test->hasElement('childint'))->toBe(false);
    expect($test->hasElement('childvarchar'))->toBe(false);
    expect($test->hasElement('grandchildint'))->toBe(true);
    expect($test->hasElement('grandchildvarchar'))->toBe(true);
});


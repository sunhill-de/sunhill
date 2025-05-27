<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\Exceptions\InvalidIDException;
use Sunhill\Storage\Exceptions\IDNotFoundException;
use Sunhill\Properties\Exceptions\InvalidPropertyException;

uses(SunhillDatabaseTestCase::class);

test('Load a dummy from database', function()
{
    Dummy::prepareDatabase($this);
    $test = new Dummy();    
    $test->load(1);
    expect($test->dummyint)->toBe(123);
})->group('read');

test('Load a dummy loads the tags', function()
{
    Dummy::prepareDatabase($this);
    $test = new Dummy();
    $test->load(1);
    expect(count($test->_tags))->toBe(1);
    expect($test->_tags[0]->getName())->toBe('TagA');
})->group('read');

test('Load a dummy loads with more tags', function()
{
    Dummy::prepareDatabase($this);
    $test = new Dummy();
    $test->load(2);
    expect(count($test->_tags))->toBe(2);
    expect($test->_tags[0]->getName())->toBe('TagA');
})->group('read');

test('Load a dummy loads with no tags', function()
{
    Dummy::prepareDatabase($this);
    $test = new Dummy();
    $test->load(5);
    expect(count($test->_tags))->toBe(0);
})->group('read');

test('Load a dummy loads attributes', function()
{
    Dummy::prepareDatabase($this);
    $test = new Dummy();
    $test->load(1);
    expect($test->hasAttributes())->toBe(true);
    expect($test->str_attribute)->toBe('attribute');
    expect($test->int_attribute)->toBe(888);
})->group('read')->group('attribute')->skip();

test('Load a dummy loads with no attributes', function()
{
    Dummy::prepareDatabase($this);
    $test = new Dummy();
    $test->load(5);
    expect($test->hasAttributes())->toBe(false);    
})->group('read')->group('attribute');

it('fails when id is invalid', function()
{
    Dummy::prepareDatabase($this);
    $test = new Dummy();
    $test->load('abc');    
})->group('read')->throws(InvalidIDException::class);

it('fails when id does not exist', function()
{
    Dummy::prepareDatabase($this);
    $test = new Dummy();
    $test->load(999);
})->group('read')->throws(IDNotFoundException::class);

it('fails when id does not match expected class', function()
{
    Dummy::prepareDatabase($this);
    $test = new Dummy();
    $test->load(9);
})->group('read')->throws(InvalidPropertyException::class);
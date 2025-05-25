<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tags\Tag;
use Sunhill\Facades\Properties;

uses(SunhillTestCase::class);

test('Write tag by id', function()
{
   $test = new Dummy();
   $test->_tags->add(1);
   $test->_tags->add(2);
   expect(count($test->_tags))->toBe(2);
   expect($test->_tags->count())->toBe(2);
   expect($test->_tags[0]->getID())->toBe(1);
});

test('Write tag by tag', function()
{
    $test = new Dummy();
    $test->_tags->add(new Tag(1));
    $test->_tags->add(new Tag(2));
    expect(count($test->_tags))->toBe(2);
    expect($test->_tags->count())->toBe(2);
    expect($test->_tags[0]->getID())->toBe(1);
});

test('Write tag by id and index', function()
{
    $test = new Dummy();
    $test->_tags[] = 1;
    $test->_tags[] = 2;
    expect(count($test->_tags))->toBe(2);
    expect($test->_tags->count())->toBe(2);
    expect($test->_tags[0]->getID())->toBe(1);
});

test('Write tag by tag and index', function()
{
    $test = new Dummy();
    $test->_tags[] = new Tag(1);
    $test->_tags[] = new Tag(2);
    expect(count($test->_tags))->toBe(2);
    expect($test->_tags->count())->toBe(2);
    expect($test->_tags[0]->getID())->toBe(1);
});

test('Write tag by name', function()
{
   Properties::shouldReceive('searchTag')->with('TagA')->andReturn(new Tag(1));
   Properties::shouldReceive('searchTag')->with('TagB')->andReturn(new Tag(2));
   $test = new Dummy();
   $test->_tags[] = 'TagA';
   $test->_tags[] = 'TagB';
   expect(count($test->_tags))->toBe(2);
   expect($test->_tags->count())->toBe(2);
   expect($test->_tags[0]->getID())->toBe(1);
});

test('Clear tags',function()
{
    $test = new Dummy();
    $test->_tags[] = 1;
    $test->_tags[] = 2;
    $test->_tags->clear();
    expect(count($test->_tags))->toBe(0);    
});


<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tags\Tag;
use Sunhill\Facades\Properties;

uses(SunhillTestCase::class);

test('Write tag by id', function()
{
   $tagA = new Tag();
   setProtectedProperty($tagA, 'name', 'TagA');
   setProtectedProperty($tagA, 'tag_id', 1);
    Properties::shouldReceive('loadTag')->with(1)->andReturn($tagA);
    $tagB = new Tag();
    setProtectedProperty($tagB, 'name', 'TagB');
    setProtectedProperty($tagB, 'tag_id', 2);
    Properties::shouldReceive('loadTag')->with(2)->andReturn($tagB);
   $test = new Dummy();
   $test->_tags->add(1);
   $test->_tags->add(2);
   expect(count($test->_tags))->toBe(2);
   expect($test->_tags->count())->toBe(2);
   expect($test->_tags[0]->getID())->toBe(1);
});

test('Write tag by tag', function()
{
    Properties::shouldReceive('loadTagData')->with(1)->andReturn(makeStdClass(['id'=>1,'name'=>'TagA','options'=>0,'parent_id'=>null]));
    Properties::shouldReceive('loadTagData')->with(2)->andReturn(makeStdClass(['id'=>2,'name'=>'TagB','options'=>0,'parent_id'=>null]));
    
    $test = new Dummy();
    $test->_tags->add(new Tag(1));
    $test->_tags->add(new Tag(2));
    expect(count($test->_tags))->toBe(2);
    expect($test->_tags->count())->toBe(2);
    expect($test->_tags[0]->getID())->toBe(1);
});

test('Write tag by id and index', function()
{
    $tagA = new Tag();
    setProtectedProperty($tagA, 'name', 'TagA');
    setProtectedProperty($tagA, 'tag_id', 1);
    Properties::shouldReceive('loadTag')->with(1)->andReturn($tagA);
    $tagB = new Tag();
    setProtectedProperty($tagB, 'name', 'TagB');
    setProtectedProperty($tagB, 'tag_id', 2);
    Properties::shouldReceive('loadTag')->with(2)->andReturn($tagB);
    
    $test = new Dummy();
    $test->_tags[] = 1;
    $test->_tags[] = 2;
    expect(count($test->_tags))->toBe(2);
    expect($test->_tags->count())->toBe(2);
    expect($test->_tags[0]->getID())->toBe(1);
});

test('Write tag by tag and index', function()
{
    Properties::shouldReceive('loadTagData')->with(1)->andReturn(makeStdClass(['id'=>1,'name'=>'TagA','options'=>0,'parent_id'=>null]));
    Properties::shouldReceive('loadTagData')->with(2)->andReturn(makeStdClass(['id'=>2,'name'=>'TagB','options'=>0,'parent_id'=>null]));
    
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
   Properties::shouldReceive('loadTagData')->with(1)->andReturn(makeStdClass(['id'=>1,'name'=>'TagA','options'=>0,'parent_id'=>null]));
   Properties::shouldReceive('loadTagData')->with(2)->andReturn(makeStdClass(['id'=>2,'name'=>'TagB','options'=>0,'parent_id'=>null]));
   $test = new Dummy();
   $test->_tags[] = 'TagA';
   $test->_tags[] = 'TagB';
   expect(count($test->_tags))->toBe(2);
   expect($test->_tags->count())->toBe(2);
   expect($test->_tags[0]->getID())->toBe(1);
});

test('Clear tags',function()
{
    $tagA = new Tag();
    setProtectedProperty($tagA, 'name', 'TagA');
    setProtectedProperty($tagA, 'tag_id', 1);
    Properties::shouldReceive('loadTag')->with(1)->andReturn($tagA);
    $tagB = new Tag();
    setProtectedProperty($tagB, 'name', 'TagB');
    setProtectedProperty($tagB, 'tag_id', 2);
    Properties::shouldReceive('loadTag')->with(2)->andReturn($tagB);
    
    $test = new Dummy();
    $test->_tags[] = 1;
    $test->_tags[] = 2;
    $test->_tags->clear();
    expect(count($test->_tags))->toBe(0);    
});


<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tags\TagList;
use Sunhill\Tags\Tag;
use Sunhill\Storage\AbstractStorage;

uses(SunhillTestCase::class);

test('adding tags and count() works', function()
{
    $tag = \Mockery::mock(Tag::class);
    $tag->shouldReceive('getID')->andReturn(1);
    
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setValue')->with('_tags',[]);
    $storage->shouldReceive('setIndexedValue')->with('_tags', null, 1);
    $test = new TagList($storage);
    
    expect(count($test))->toBe(0);
    $test->add($tag);
    expect(count($test))->toBe(1);
});

test('array access works', function()
{
    $tag = \Mockery::mock(Tag::class);
    $tag->shouldReceive('getID')->andReturn(1);
    ;
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setValue')->with('_tags',[]);
    $storage->shouldReceive('setIndexedValue')->with('_tags', null, 1);
    $test = new TagList($storage);
    
    $test[] = $tag;
    
    expect($test[0])->toBe($tag);
});

test('array overwrite works', function()
{
    $tag = \Mockery::mock(Tag::class);
    $tag->shouldReceive('getID')->andReturn(1);

    $newtag = \Mockery::mock(Tag::class);
    $newtag->shouldReceive('getID')->andReturn(2);
    
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setValue')->with('_tags',[]);
    $storage->shouldReceive('setIndexedValue')->once()->with('_tags', null, 1);
    $storage->shouldReceive('setIndexedValue')->once()->with('_tags', null, 2);
    $test = new TagList($storage);
    
    $test[] = $tag;
    $test[0] = $newtag;
    
    expect($test[0])->toBe($newtag);
});

test('array remove works', function()
{
    $tag = \Mockery::mock(Tag::class);
    $tag->shouldReceive('getID')->andReturn(1);
    
    $newtag = \Mockery::mock(Tag::class);
    $newtag->shouldReceive('getID')->andReturn(2);
    
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setValue')->with('_tags',[]);
    $storage->shouldReceive('setIndexedValue')->once()->with('_tags', null, 1);
    $storage->shouldReceive('setIndexedValue')->once()->with('_tags', null, 2);
    $storage->shouldReceive('unsetIndexedValue')->once()->with('_tags', 0);
    $test = new TagList($storage);
    
    $test[] = $tag;
    $test[] = $newtag;
    unset($test[0]);
    
    expect($test[0])->toBe($newtag);
});

test('ignore duplicate tag', function()
{
    $tag = \Mockery::mock(Tag::class);
    $tag->shouldReceive('getID')->andReturn(1);
    
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setValue')->with('_tags',[]);
    $storage->shouldReceive('setIndexedValue')->once()->with('_tags', null, 1);
    $test = new TagList($storage);
    
    $test[] = $tag;
    $test[] = $tag;
    
    expect(count($test))->toBe(1);
});

test('clear works', function()
{
    $tag = \Mockery::mock(Tag::class);
    $tag->shouldReceive('getID')->andReturn(1);
    $newtag = \Mockery::mock(Tag::class);
    $newtag->shouldReceive('getID')->andReturn(2);
    
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setValue')->with('_tags',[]);
    $storage->shouldReceive('setIndexedValue')->once()->with('_tags', null, 1);
    $storage->shouldReceive('setIndexedValue')->once()->with('_tags', null, 2);
    $storage->shouldReceive('clearArray')->once()->with('_tags');
    $test = new TagList($storage);
    
    $test[] = $tag;
    $test[] = $newtag;
    
    expect(count($test))->toBe(2);
    $test->clear();
    expect(count($test))->toBe(0);
});


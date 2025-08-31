<?php

/**
 * @file TagTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: yes
 */

use Sunhill\Facades\Properties;
use Sunhill\Tags\Tag;
use Sunhill\Tests\SunhillLaravelTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;

uses(SunhillLaravelTestCase::class);

test('Write tag by id', function () {
    $tagA = Mockery::mock(Tag::class);
    $tagA->shouldReceive('getName')->andReturn('TagA');
    $tagA->shouldReceive('getID')->andReturn(1);
    Properties::shouldReceive('loadTag')->with(1)->andReturn($tagA);
    $tagB = Mockery::mock(Tag::class);
    $tagB->shouldReceive('getID')->andReturn(2);
    Properties::shouldReceive('loadTag')->with(2)->andReturn($tagB);
    $test = new Dummy;
    $test->_tags->add(1);
    $test->_tags->add(2);
    expect(count($test->_tags))->toBe(2);
    expect($test->_tags->count())->toBe(2);
    expect($test->_tags[0]->getID())->toBe(1);
});

test('Write tag by tag', function () {
    $tagA = Mockery::mock(Tag::class);
    $tagA->shouldReceive('getID')->andReturn(1);
    $tagB = Mockery::mock(Tag::class);
    $tagB->shouldReceive('getID')->andReturn(2);

    $test = new Dummy;
    $test->_tags->add($tagA);
    $test->_tags->add($tagB);
    expect(count($test->_tags))->toBe(2);
    expect($test->_tags->count())->toBe(2);
    expect($test->_tags[0]->getID())->toBe(1);
});

test('Write tag by id and index', function () {
    $tagA = Mockery::mock(Tag::class);
    $tagA->shouldReceive('getID')->andReturn(1);
    Properties::shouldReceive('loadTag')->with(1)->andReturn($tagA);
    $tagB = Mockery::mock(Tag::class);
    $tagB->shouldReceive('getID')->andReturn(2);
    Properties::shouldReceive('loadTag')->with(2)->andReturn($tagB);

    $test = new Dummy;
    $test->_tags[] = 1;
    $test->_tags[] = 2;
    expect(count($test->_tags))->toBe(2);
    expect($test->_tags->count())->toBe(2);
    expect($test->_tags[0]->getID())->toBe(1);
});

test('Write tag by tag and index', function () {
    $tagA = Mockery::mock(Tag::class);
    $tagA->shouldReceive('getID')->andReturn(1);
    Properties::shouldReceive('loadTag')->with(1)->andReturn($tagA);
    $tagB = Mockery::mock(Tag::class);
    $tagB->shouldReceive('getID')->andReturn(2);
    Properties::shouldReceive('loadTag')->with(2)->andReturn($tagB);

    $test = new Dummy;
    $test->_tags[] = $tagA;
    $test->_tags[] = $tagB;
    expect(count($test->_tags))->toBe(2);
    expect($test->_tags->count())->toBe(2);
    expect($test->_tags[0]->getID())->toBe(1);
});

test('Write tag by name', function () {
    $tagA = Mockery::mock(Tag::class);
    $tagA->shouldReceive('getID')->andReturn(1);
    $tagB = Mockery::mock(Tag::class);
    $tagB->shouldReceive('getID')->andReturn(2);
    Properties::shouldReceive('searchTag')->with('TagA')->andReturn($tagA);
    Properties::shouldReceive('searchTag')->with('TagB')->andReturn($tagB);
    $test = new Dummy;
    $test->_tags[] = 'TagA';
    $test->_tags[] = 'TagB';
    expect(count($test->_tags))->toBe(2);
    expect($test->_tags->count())->toBe(2);
    expect($test->_tags[0]->getID())->toBe(1);
});

test('Clear tags', function () {
    $tagA = Mockery::mock(Tag::class);
    $tagA->shouldReceive('getID')->andReturn(1);
    Properties::shouldReceive('loadTag')->with(1)->andReturn($tagA);
    $tagB = Mockery::mock(Tag::class);
    $tagB->shouldReceive('getID')->andReturn(2);
    Properties::shouldReceive('loadTag')->with(2)->andReturn($tagB);

    $test = new Dummy;
    $test->_tags[] = 1;
    $test->_tags[] = 2;
    $test->_tags->clear();
    expect(count($test->_tags))->toBe(0);
});

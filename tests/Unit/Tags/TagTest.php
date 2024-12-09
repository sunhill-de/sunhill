<?php

namespace Sunhill\Tests\Unit\Tags;

use Sunhill\Tags\Tag;
use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\Database\Seeds\TagsSeeder;
use Sunhill\Tests\Database\Seeds\TagCacheSeeder;
use Sunhill\Tests\Database\Seeds\TagObjectAssignsSeeder;

uses(SunhillDatabaseTestCase::class);

test('load a tag', function()
{
    $this->seed([TagsSeeder::class,TagCacheSeeder::class,TagObjectAssignsSeeder::class]);
    
    $test = new Tag();
    $test->load(1);
    expect($test->getName())->toBe('TagA');
    expect($test->getFullPath())->toBe('TagA');
});

test('lazy load', function()
{
    $this->seed([TagsSeeder::class,TagCacheSeeder::class,TagObjectAssignsSeeder::class]);
    $test = new Tag();
    $test->load(1);
    expect(getProtectedProperty($test, 'name'))->toBe('');
    expect($test->getName())->toBe('TagA');
    expect(getProtectedProperty($test, 'name'))->toBe('TagA');    
});

test('getFullpath()', function()
{
    $this->seed([TagsSeeder::class,TagCacheSeeder::class,TagObjectAssignsSeeder::class]);
    $test = new Tag();
    $test->load(8);
    expect($test->getFullPath())->toBe('TagE.TagF.TagG');    
});

test('getter and setters', function()
{
    $test = new Tag();
    $parent = new Tag();
    
    $test->setParent($parent)->setName('Test')->setOptions(Tag::TO_LEAFABLE);
    $this->assertEquals($parent,$test->getParent());
    $this->assertEquals('Test',$test->getName());
    $this->assertEquals(Tag::TO_LEAFABLE,$test->getOptions());
    $this->assertTrue($test->isLeafable());
    $test->unsetLeafable();
    $this->assertFalse($test->isLeafable());
    $test->setLeafable();
    $this->assertTrue($test->isLeafable());
    
});

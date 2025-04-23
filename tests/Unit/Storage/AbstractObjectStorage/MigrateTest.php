<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tests\Unit\Storage\AbstractObjectStorage\DummyAbstractObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Tests\TestSupport\Objects\ParentObject;

uses(SunhillTestCase::class);

test('assembleStructure', function($class, $structure)
{
    $test = new DummyAbstractObjectStorage();
    $test->setStructure($class::getExpectedStructure());
    
})->with([
    [ParentObject::class,['parent_int'=>['type'=>'integer'],'parent_string'=>['type'=>'string','max_len'=>3]]],
    [ChildObject::class,['child_int'=>['type'=>'integer'],'child_string'=>['type'=>'string','max_len'=>3]]],
]);

test('Migrate just the parent', function()
{
    $test = new DummyAbstractObjectStorage();
    $test::$DataPool = $test::$Data;
    unsset($test::$DataPool['childobjects']);
    unsset($test::$DataPool['childobjects_child_sarray']);
    unsset($test::$DataPool['parentobjects']);
    unsset($test::$DataPool['parentobjects_parent_sarray']);
    $test->setStructure(ParentObject::getExpectedStructure());
    
    $test->migrate();
    
    expect(array_key_exists($test::$DataPool['parentobjects']))->toBe(true);
    expect(array_key_exists($test::$DataPool['parentobjects_parent_sarray']))->toBe(true);
});

test('Migrate the child with existing parent', function()
{
    $test = new DummyAbstractObjectStorage();
    $test::$DataPool = $test::$Data;
    unsset($test::$DataPool['childobjects']);
    unsset($test::$DataPool['childobjects_child_sarray']);
    $test->setStructure(ChildObject::getExpectedStructure());
    
    $test->migrate();
    
    expect(array_key_exists($test::$DataPool['parentobjects']))->toBe(true);
    expect(array_key_exists($test::$DataPool['parentobjects_parent_sarray']))->toBe(true);
    expect(array_key_exists($test::$DataPool['childobjects']))->toBe(true);
    expect(array_key_exists($test::$DataPool['childobjects_child_sarray']))->toBe(true);
});

test('Migrate the child without existing parent', function()
{
    $test = new DummyAbstractObjectStorage();
    $test::$DataPool = $test::$Data;
    unsset($test::$DataPool['childobjects']);
    unsset($test::$DataPool['childobjects_child_sarray']);
    unsset($test::$DataPool['parentobjects']);
    unsset($test::$DataPool['parentobjects_parent_sarray']);
    $test->setStructure(ChildObject::getExpectedStructure());
    
    $test->migrate();
    
    expect(array_key_exists($test::$DataPool['parentobjects']))->toBe(true);
    expect(array_key_exists($test::$DataPool['parentobjects_parent_sarray']))->toBe(true);
    expect(array_key_exists($test::$DataPool['childobjects']))->toBe(true);
    expect(array_key_exists($test::$DataPool['childobjects_child_sarray']))->toBe(true);
});

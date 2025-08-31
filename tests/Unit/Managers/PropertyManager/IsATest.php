<?php
/**
 * @file IsATest.php
 * tests: /src/Managers/PropertiesManagaer.php
 * free of dependent units: yes
 */

use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Objects\ORMObject;
use Sunhill\Tests\SunhillLaravelTestCase;

uses(SunhillLaravelTestCase::class);

test('IsA works as expected', function($test_class, $test_item, $expect)
{
    Properties::registerProperty(ORMObject::class);
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    Properties::registerProperty(DummyGrandChild::class);
    Properties::registerProperty(ParentObject::class);
    
    if (is_callable($test_class)) {
        $test_class = $test_class();
    }
    if (is_callable($test_item)) {
        $test_item = $test_item();
    }
    expect(Properties::isA($test_class, $test_item))->toBe($expect);
})->group('manager')->with([
    [Dummy::class, Dummy::class, true],
    ['Dummy', Dummy::class, true],
    [Dummy::class, 'Dummy', true],
    [function() { return new Dummy(); }, Dummy::class, true],
    ['Dummy','Dummy', true],

    [DummyChild::class, Dummy::class, true],
    ['DummyChild', Dummy::class, true],
    [DummyChild::class, 'Dummy', true],
    [function() { return new DummyChild(); }, Dummy::class, true],
    ['DummyChild','Dummy', true],

    [DummyGrandChild::class, Dummy::class, true],
    ['DummyGrandChild', Dummy::class, true],
    [DummyGrandChild::class, 'Dummy', true],
    [function() { return new DummyGrandChild(); }, Dummy::class, true],
    ['DummyGrandChild','Dummy', true],

    [Dummy::class, ORMObject::class, true],
    ['Dummy', ORMObject::class, true],
    [Dummy::class, 'Object', true],
    [function() { return new Dummy(); }, ORMObject::class, true],
    ['Dummy','Object', true],
    
    [Dummy::class, ParentObject::class, false],
    ['Dummy', ParentObject::class, false],
    [Dummy::class, 'ParentObject', false],
    [function() { return new Dummy(); }, ParentObject::class, false],
    ['Dummy','ParentObject', false],    
    ]);

test('IsAClass works as expected', function($test_class, $test_item, $expect)
{
    Properties::registerProperty(ORMObject::class);
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    Properties::registerProperty(DummyGrandChild::class);
    Properties::registerProperty(ParentObject::class);
    
    if (is_callable($test_class)) {
        $test_class = $test_class();
    }
    if (is_callable($test_item)) {
        $test_item = $test_item();
    }
    expect(Properties::isAClass($test_class, $test_item))->toBe($expect);
})->group('manager')->with([
    [Dummy::class, Dummy::class, true],
    ['Dummy', Dummy::class, true],
    [Dummy::class, 'Dummy', true],
    [function() { return new Dummy(); }, Dummy::class, true],
    ['Dummy','Dummy', true],
    
    [DummyChild::class, Dummy::class, false],
    ['DummyChild', Dummy::class, false],
    [DummyChild::class, 'Dummy', false],
    [function() { return new DummyChild(); }, Dummy::class, false],
    ['DummyChild','Dummy', false],
    
    [DummyGrandChild::class, Dummy::class, false],
    ['DummyGrandChild', Dummy::class, false],
    [DummyGrandChild::class, 'Dummy', false],
    [function() { return new DummyGrandChild(); }, Dummy::class, false],
    ['DummyGrandChild','Dummy', false],
    
    [Dummy::class, ORMObject::class, false],
    ['Dummy', ORMObject::class, false],
    [Dummy::class, 'Object', false],
    [function() { return new Dummy(); }, ORMObject::class, false],
    ['Dummy','Object', false],
    
    [Dummy::class, ParentObject::class, false],
    ['Dummy', ParentObject::class, false],
    [Dummy::class, 'ParentObject', false],
    [function() { return new Dummy(); }, ParentObject::class, false],
    ['Dummy','ParentObject', false],
    ]);

test('IsSubclassOf works as expected', function($test_class, $test_item, $expect)
{
    Properties::registerProperty(ORMObject::class);
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    Properties::registerProperty(DummyGrandChild::class);
    Properties::registerProperty(ParentObject::class);
    
    if (is_callable($test_class)) {
        $test_class = $test_class();
    }
    if (is_callable($test_item)) {
        $test_item = $test_item();
    }
    expect(Properties::isSubclassOf($test_class, $test_item))->toBe($expect);
})->group('manager')->with([
    [Dummy::class, Dummy::class, false],
    ['Dummy', Dummy::class, false],
    [Dummy::class, 'Dummy', false],
    [function() { return new Dummy(); }, Dummy::class, false],
    ['Dummy','Dummy', false],
    
    [DummyChild::class, Dummy::class, true],
    ['DummyChild', Dummy::class, true],
    [DummyChild::class, 'Dummy', true],
    [function() { return new DummyChild(); }, Dummy::class, true],
    ['DummyChild','Dummy', true],
    
    [DummyGrandChild::class, Dummy::class, true],
    ['DummyGrandChild', Dummy::class, true],
    [DummyGrandChild::class, 'Dummy', true],
    [function() { return new DummyGrandChild(); }, Dummy::class, true],
    ['DummyGrandChild','Dummy', true],
    
    [Dummy::class, ORMObject::class, true],
    ['Dummy', ORMObject::class, true],
    [Dummy::class, 'Object', true],
    [function() { return new Dummy(); }, ORMObject::class, true],
    ['Dummy','Object', true],
    
    [Dummy::class, ParentObject::class, false],
    ['Dummy', ParentObject::class, false],
    [Dummy::class, 'ParentObject', false],
    [function() { return new Dummy(); }, ParentObject::class, false],
    ['Dummy','ParentObject', false],
    ]);

/*
test('isSubclassOf works as expectec', function($test_class, $param, $expect)
{
/*    Properties::registerProperty(ORMObject::class);
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    Properties::registerProperty(DummyGrandChild::class);
    Properties::registerProperty(ParentObject::class);
    Properties::registerProperty(ChildObject::class);
    
    $test = new $test_class();
    expect(Properties::isSubclassOf($test,$param))->toBe($expect); 
})
//->skip()->group('manager')->group('class')
->with(
    [Dummy::class, Dummy::class, false],
    [DummyChild::class, Dummy::class, true],
    [DummyGrandChild::class, Dummy::class, true],
    [Dummy::class, ParentObject::class, false],
    [Dummy::class, ORMObject::class, true],
    
    [Dummy::class, 'Dummy', false],
    [DummyChild::class, 'Dummy', true],
    [DummyGrandChild::class, 'Dummy', true],
    [Dummy::class, 'ParentObject', false],
    [Dummy::class, 'Object', true],
    );
*/

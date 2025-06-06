<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Objects\ORMObject;

uses(SunhillTestCase::class);
/*
test('IsA works as expected', function($test_class, $param, $expect)
{
    Properties::registerProperty(ORMObject::class);
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    Properties::registerProperty(DummyGrandChild::class);
    Properties::registerProperty(ParentObject::class);
    Properties::registerProperty(ChildObject::class);
    
    $test = new $test_class();
    expect(Properties::isA($test,$param))->toBe($expect);
})->skip()->group('manager')->with([
    [Dummy::class, Dummy::class, true],
    [DummyChild::class, Dummy::class, true],
    [DummyGrandChild::class, Dummy::class, true],
    [Dummy::class, ParentObject::class, false],
    [Dummy::class, ORMObject::class, true],
    
    [Dummy::class, 'Dummy', true],
    [DummyChild::class, 'Dummy', true],
    [DummyGrandChild::class, 'Dummy', true],
    [Dummy::class, 'ParentObject', false],
    [Dummy::class, 'Object', true],
]);

test('isAClass works as expectec', function($test_class, $param, $expect)
{
    Properties::registerProperty(ORMObject::class);
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    Properties::registerProperty(DummyGrandChild::class);
    Properties::registerProperty(ParentObject::class);
    Properties::registerProperty(ChildObject::class);
    
    $test = new $test_class();
    expect(Properties::isAClass($test,$param))->toBe($expect);    
})->skip()->group('manager')->group('class')->with(
    [Dummy::class, Dummy::class, true],
    [DummyChild::class, Dummy::class, false],
    [DummyGrandChild::class, Dummy::class, false],
    [Dummy::class, ParentObject::class, false],
    [Dummy::class, ORMObject::class, false],
    
    [Dummy::class, 'Dummy', true],
    [DummyChild::class, 'Dummy', false],
    [DummyGrandChild::class, 'Dummy', false],
    [Dummy::class, 'ParentObject', false],
    [Dummy::class, 'Object', false],    
    );

test('isSubclassOf works as expectec', function($test_class, $param, $expect)
{
    Properties::registerProperty(ORMObject::class);
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    Properties::registerProperty(DummyGrandChild::class);
    Properties::registerProperty(ParentObject::class);
    Properties::registerProperty(ChildObject::class);
    
    $test = new $test_class();
    expect(Properties::isSubclassOf($test,$param))->toBe($expect);
})->skip()->group('manager')->group('class')->with(
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
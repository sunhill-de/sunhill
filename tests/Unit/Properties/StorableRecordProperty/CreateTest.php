<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Properties\StorableRecordProperty;
use Sunhill\Types\TypeInteger;
use Sunhill\Storage\PersistentSingleStorage;

uses(SimpleTestCase::class);

test('create prefills with default values', function()
{
   $storage = \Mockery::mock(PersistentSingleStorage::class);
   $storage->shouldReceive('setValue')->with('default_element',10)->once();
   $storage->shouldReceive('getIsInitialized')->with('default_element')->once()->andReturn(true);
   
   $test = new StorableRecordProperty();
   $default_element = new TypeInteger();
   $nodefault_element = new TypeInteger();
   
   $test->appendElement($default_element, 'default_element')->setDefault(10);
   $test->appendElement($nodefault_element, 'nondefault_element');
   $test->setStorage($storage);
   
   $test->create();
});

test('create and commit work', function()
{
    $storage = \Mockery::mock(PersistentSingleStorage::class);
    $storage->shouldReceive('commit')->once();
    
    $test = new StorableRecordProperty();
    $int_element1 = new TypeInteger();
    $int_element2 = new TypeInteger();
    $test->appendElement($int_element1, 'int_element1');
    $test->appendElement($int_element2, 'int_element2');
    $test->setStorage($storage);
    
    $test->create();
    $test->commit();
});

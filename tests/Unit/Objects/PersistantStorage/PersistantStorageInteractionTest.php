<?php

use Sunhill\Properties\Tests\TestCase;
use Sunhill\Properties\Tests\Unit\Objects\PersistantStorage\Samples\DummyPersistantStorage;
use Sunhill\Properties\Objects\AbstractPersistantRecord;
use Sunhill\Properties\Types\TypeInteger;
use Sunhill\Properties\Objects\AbstractStorageAtom;
use Sunhill\Properties\Properties\ArrayProperty;

uses(TestCase::class);

test('simple load test', function()
{
    $integer = \Mockery::mock(TypeInteger::class);
    $record = \Mockery::mock(AbstractPersistantRecord::class);
    $record->shouldReceive('exportElements')->atLeast(1)->andReturn([
        'simplestorage'=>['simplefield'=>$integer]
    ]);
    $atom = \Mockery::mock(AbstractStorageAtom::class);
    $atom->shouldReceive('loadDirectory')->with(1)->andReturn(['id'=>1,'_created_at'=>'2024-05-22 09:54:00','_class'=>'sample']);
    $atom->shouldReceive('loadRecord')->with(1,'simplestorage',['simplefield'=>$integer])->andReturn(['id'=>1,'simplefield'=>123]);
    
    $test = new DummyPersistantStorage($record);
    $test->atom = $atom;
    $test->load(1);
    expect($test->getValue('simplefield'))->toBe(123);
});

test('simple store test', function()
{
    $record = \Mockery::mock(AbstractPersistantRecord::class);
    $record->shouldReceive('exportElements')->atLeast(1)->andReturn([
        'simplestorage'=>['simplefield'=>new TypeInteger()]
    ]);
    $atom = \Mockery::mock(AbstractStorageAtom::class);
    $atom->shouldReceive('storeDirectory')->once()->andReturn(1);
    $atom->shouldReceive('storeRecord')->once()->with(1,'simplestorage',['simplefield'=>new TypeInteger()],['simplefield'=>123]);
    
    $test = new DummyPersistantStorage($record);
    $test->atom = $atom;
    $test->setValue('simplefield', 123);
    $test->commit();
});

test('simple update test, changed', function()
{
    $record = \Mockery::mock(AbstractPersistantRecord::class);
    $record->shouldReceive('exportElements')->atLeast(1)->andReturn([
        'simplestorage'=>['simplefield'=>new TypeInteger()]
    ]);
    $atom = \Mockery::mock(AbstractStorageAtom::class);
    $atom->shouldReceive('updateDirectory')->once()->with(1);
    $atom->shouldReceive('updateRecord')->once()->with(1, 'simplestorage',['simplefield'=>new TypeInteger()],['simplefield'=>123],['simplefield'=>321]);
    
    $test = new DummyPersistantStorage($record);
    $test->atom = $atom;
    setProtectedProperty($test, 'shadows', ['simplefield'=>123]);
    $test->setValue('simplefield', 321);
    $test->setID(1);
    $test->commit();    
});

test('simple update test, unchanged', function()
{
    $record = \Mockery::mock(AbstractPersistantRecord::class);
    $record->shouldReceive('exportElements')->atLeast(1)->andReturn([
        'simplestorage'=>['simplefield'=>new TypeInteger()]
    ]);
    $atom = \Mockery::mock(AbstractStorageAtom::class);
    $atom->shouldReceive('updateDirectory')->never();
    $atom->shouldReceive('updateRecord')->never();
    
    $test = new DummyPersistantStorage($record);
    $test->atom = $atom;
    $test->setValue('simplefield', 321);
    $test->setID(1);
    $test->commit();
});

test('simple delete test', function()
{
    $record = \Mockery::mock(AbstractPersistantRecord::class);
    $record->shouldReceive('exportElements')->atLeast(1)->andReturn([
        'simplestorage'=>['simplefield'=>new TypeInteger()]
    ]);
    $atom = \Mockery::mock(AbstractStorageAtom::class);
    $atom->shouldReceive('deleteDirectory')->once()->with(1);
    $atom->shouldReceive('deleteRecord')->once()->with(1, 'simplestorage', ['simplefield'=>new TypeInteger()]);
    
    $test = new DummyPersistantStorage($record);
    $test->atom = $atom;
    $test->setID(1);
    $test->delete();    
});

test('complex load test', function()
{
    $integer = \Mockery::mock(TypeInteger::class);
    $array = \Mockery::mock(ArrayProperty::class);
    $array->shouldReceive('getAllowedElementTypes')->andReturn(TypeInteger::class);
    
    $record = \Mockery::mock(AbstractPersistantRecord::class);
    $record->shouldReceive('exportElements')->atLeast(1)->andReturn([
        'simplestorage'=>['simplefield'=>$integer,'simplearray'=>$array],
        'anotherstorage'=>['someinteger'=>$integer]
    ]);
    
    $atom = \Mockery::mock(AbstractStorageAtom::class);
    $atom->shouldReceive('loadDirectory')->once()->with(1)->andReturn(['id'=>1,'created_at'=>'2024-05-22 09:54:00','class'=>'sample']);
    $atom->shouldReceive('loadRecord')->once()->with(1,'simplestorage',['simplefield'=>$integer,'simplearray'=>$array])->andReturn(['id'=>1,'simplefield'=>123,'simplearray'=>[1,2,3]]);
    $atom->shouldReceive('loadRecord')->once()->with(1,'anotherstorage',['someinteger'=>$integer])->andReturn(['id'=>1,'someinteger'=>234]);
    
    $test = new DummyPersistantStorage($record);
    $test->atom = $atom;
    $test->load(1);
    expect($test->getValue('simplefield'))->toBe(123);
    expect($test->getValue('someinteger'))->toBe(234);
});

test('complex store test', function()
{
    $integer = \Mockery::mock(TypeInteger::class);
    $array = \Mockery::mock(ArrayProperty::class);
    $array->shouldReceive('getAllowedElementTypes')->andReturn(TypeInteger::class);
    
    $record = \Mockery::mock(AbstractPersistantRecord::class);
    $record->shouldReceive('exportElements')->atLeast(1)->andReturn([
        'simplestorage'=>['simplefield'=>$integer,'simplearray'=>$array],
        'anotherstorage'=>['someinteger'=>$integer]
    ]);
    
    $atom = \Mockery::mock(AbstractStorageAtom::class);
    $atom->shouldReceive('storeDirectory')->once()->andReturn(1);
    $atom->shouldReceive('storeRecord')->once()->with(1,'simplestorage',['simplefield'=>$integer,'simplearray'=>$array],['simplefield'=>123,'simplearray'=>[1,2,3]]);
    $atom->shouldReceive('storeRecord')->once()->with(1,'anotherstorage',['someinteger'=>$integer],['someinteger'=>234]);
    
    $test = new DummyPersistantStorage($record);
    $test->atom = $atom;
    $test->setValue('simplefield', 123);
    $test->setValue('someinteger', 234);
    $test->setValue('simplearray', [1,2,3]);
    
    $test->commit();
});

test('complex update test some change', function()
{
    $integer = \Mockery::mock(TypeInteger::class);
    $array = \Mockery::mock(ArrayProperty::class);
    $array->shouldReceive('getAllowedElementTypes')->andReturn(TypeInteger::class);
    
    $record = \Mockery::mock(AbstractPersistantRecord::class);
    $record->shouldReceive('exportElements')->atLeast(1)->andReturn([
        'simplestorage'=>['simplefield'=>$integer,'simplearray'=>$array],
        'anotherstorage'=>['someinteger'=>$integer]
    ]);
    
    $atom = \Mockery::mock(AbstractStorageAtom::class);
    $atom->shouldReceive('updateDirectory')->once()->with(1);
    $atom->shouldReceive('updateRecord')->once()->with(1, 'simplestorage',['simplefield'=>$integer,'simplearray'=>$array],['simplefield'=>321],['simplefield'=>123]);
    
    $test = new DummyPersistantStorage($record);
    $test->atom = $atom;
    $test->setValue('simplefield', 123);
    $test->setValue('someinteger', 234);
    $test->setValue('simplearray', [1,3,4]);
    setProtectedProperty($test, 'shadows', ['simplefield'=>321]);
    $test->setID(1);
    
    $test->commit();
});

test('complex update test array change', function()
{
    $integer = \Mockery::mock(TypeInteger::class);
    $array = \Mockery::mock(ArrayProperty::class);
    $array->shouldReceive('getAllowedElementTypes')->andReturn(TypeInteger::class);
    
    $record = \Mockery::mock(AbstractPersistantRecord::class);
    $record->shouldReceive('exportElements')->atLeast(1)->andReturn([
        'simplestorage'=>['simplefield'=>$integer,'simplearray'=>$array],
        'anotherstorage'=>['someinteger'=>$integer]
    ]);
    
    $atom = \Mockery::mock(AbstractStorageAtom::class);
    $atom->shouldReceive('updateDirectory')->once()->with(1);
    $atom->shouldReceive('updateRecord')->once()->with(1, 'simplestorage',['simplefield'=>$integer,'simplearray'=>$array],['simplearray'=>[2,3,4]],['simplearray'=>[1,3,4]]);
    
    $test = new DummyPersistantStorage($record);
    $test->atom = $atom;
    $test->setValue('simplefield', 123);
    $test->setValue('someinteger', 234);
    $test->setValue('simplearray', [1,3,4]);
    setProtectedProperty($test, 'shadows', ['simplearray'=>[2,3,4]]);
    $test->setID(1);
    
    $test->commit();
});

test('complex update test both storages change', function()
{
    $integer = \Mockery::mock(TypeInteger::class);
    $array = \Mockery::mock(ArrayProperty::class);
    $array->shouldReceive('getAllowedElementTypes')->andReturn(TypeInteger::class);
    
    $record = \Mockery::mock(AbstractPersistantRecord::class);
    $record->shouldReceive('exportElements')->atLeast(1)->andReturn([
        'simplestorage'=>['simplefield'=>$integer,'simplearray'=>$array],
        'anotherstorage'=>['someinteger'=>$integer]
    ]);
    
    $atom = \Mockery::mock(AbstractStorageAtom::class);
    $atom->shouldReceive('updateDirectory')->once()->with(1);
    $atom->shouldReceive('updateRecord')->once()->with(1, 'simplestorage',['simplefield'=>$integer,'simplearray'=>$array],['simplearray'=>[2,3,4]],['simplearray'=>[1,3,4]]);
    $atom->shouldReceive('updateRecord')->once()->with(1, 'anotherstorage',['someinteger'=>$integer],['someinteger'=>321],['someinteger'=>432]);
    
    $test = new DummyPersistantStorage($record);
    $test->atom = $atom;
    $test->setValue('simplefield', 123);
    $test->setValue('someinteger', 432);
    $test->setValue('simplearray', [1,3,4]);
    setProtectedProperty($test, 'shadows', ['simplearray'=>[2,3,4],'someinteger'=>321]);
    $test->setID(1);
    
    $test->commit();
});

test('complex delete test', function()
{
    $integer = \Mockery::mock(TypeInteger::class);
    $array = \Mockery::mock(ArrayProperty::class);
    $array->shouldReceive('getAllowedElementTypes')->andReturn(TypeInteger::class);
    
    $record = \Mockery::mock(AbstractPersistantRecord::class);
    $record->shouldReceive('exportElements')->atLeast(1)->andReturn([
        'simplestorage'=>['simplefield'=>$integer,'simplearray'=>$array],
        'anotherstorage'=>['someinteger'=>$integer]
    ]);
    
    $atom = \Mockery::mock(AbstractStorageAtom::class);
    $atom->shouldReceive('deleteDirectory')->once()->with(1);
    $atom->shouldReceive('deleteRecord')->once()->with(1, 'simplestorage', ['simplefield'=>$integer,'simplearray'=>$array]);
    $atom->shouldReceive('deleteRecord')->once()->with(1, 'anotherstorage', ['someinteger'=>$integer]);
    
    $test = new DummyPersistantStorage($record);
    $test->atom = $atom;
    $test->setID(1);
    $test->delete();
});



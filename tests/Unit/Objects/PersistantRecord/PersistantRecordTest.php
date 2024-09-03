<?php

use Sunhill\Properties\Tests\TestCase;
use Sunhill\Properties\Types\TypeInteger;
use Sunhill\Properties\Objects\Exceptions\TypeCannotBeEmbeddedException;
use Sunhill\Properties\Tests\Unit\Objects\PersistantRecord\Samples\EmptyPersistantRecord;
use Sunhill\Properties\Facades\Properties;
use Sunhill\Properties\Properties\AbstractRecordProperty;
use Sunhill\Properties\Types\TypeVarchar;
use Sunhill\Properties\Objects\Exceptions\TypeAlreadyEmbeddedException;
use Sunhill\Properties\Properties\Exceptions\DuplicateElementNameException;
use Sunhill\Properties\Storage\AbstractStorage;

uses(TestCase::class);

test('appendElement() works', function()
{
    $test = new EmptyPersistantRecord();
    $integer = \Mockery::mock(TypeInteger::class);
    $integer->shouldReceive('setOwner')->andReturn($integer);
    Properties::shouldReceive('createProperty')->with('integer')->andReturn($integer);
    $test->appendElement('testint','integer','teststorage');
    
    expect($test->hasElement('testint'))->toBe(true);
});

test('embedElement() works', function() 
{
    $test = new EmptyPersistantRecord();
    
    $integer = \Mockery::mock(TypeInteger::class);
    $integer->shouldReceive('setOwner')->with($test)->andReturn($integer);
    $varchar = \Mockery::mock(TypeVarchar::class);
    $varchar->shouldReceive('setOwner')->with($test)->andReturn($varchar);

    $record = \Mockery::mock(AbstractRecordProperty::class);
    $record->shouldReceive('getElements')->andReturn(
       [
           'intval'=>$integer,
           'charval'=>$varchar
           ]
       );
    
    Properties::shouldReceive('createProperty')->with('integer')->andReturn($integer);
    Properties::shouldReceive('createProperty')->with('varchar')->andReturn($varchar);
    Properties::shouldReceive('createProperty')->with('TestRecord')->andReturn($record);
    
    $test->embedElement('TestRecord');
    
    expect($test->hasElement('intval'))->toBe(true);
    expect($test->hasElement('charval'))->toBe(true);
    expect($test->hasEmbed('TestRecord'))->toBe(true);
    expect($test->hasInclude('TestRecord'))->toBe(false);
});

test('embedElement() fails with duplicate', function()
{
    $test = new EmptyPersistantRecord();
    
    $integer = \Mockery::mock(TypeInteger::class);
    $integer->shouldReceive('setOwner')->with($test)->andReturn($integer);
    $varchar = \Mockery::mock(TypeVarchar::class);
    $varchar->shouldReceive('setOwner')->with($test)->andReturn($varchar);
    
    $record = \Mockery::mock(AbstractRecordProperty::class);
    $record->shouldReceive('getElements')->andReturn(
        [
            'intval'=>$integer,
            'charval'=>$varchar
        ]
        );
    
    Properties::shouldReceive('createProperty')->with('integer')->andReturn($integer);
    Properties::shouldReceive('createProperty')->with('varchar')->andReturn($varchar);
    Properties::shouldReceive('createProperty')->with('TestRecord')->andReturn($record);
    
    $test->embedElement('TestRecord');
    $test->embedElement('TestRecord');    
})->throws(TypeAlreadyEmbeddedException::class);

test('embedElement() fails when called with wrong datatype', function()
{
    $test = new EmptyPersistantRecord();
    
    $integer = \Mockery::mock(TypeInteger::class);
    Properties::shouldReceive('createProperty')->with('integer')->andReturn($integer);
    
    $test->embedElement('integer');    
})->throws(TypeCannotBeEmbeddedException::class);

test('includeElement() works', function()
{
    $test = new EmptyPersistantRecord();
    
    $integer = \Mockery::mock(TypeInteger::class);
    $integer->shouldReceive('setOwner')->with($test)->andReturn($integer);
    $varchar = \Mockery::mock(TypeVarchar::class);
    $varchar->shouldReceive('setOwner')->with($test)->andReturn($varchar);
    
    $record = \Mockery::mock(AbstractRecordProperty::class);
    $record->shouldReceive('getElements')->andReturn(
        [
            'intval'=>$integer,
            'charval'=>$varchar
        ]
        );
    
    Properties::shouldReceive('createProperty')->with('integer')->andReturn($integer);
    Properties::shouldReceive('createProperty')->with('varchar')->andReturn($varchar);
    Properties::shouldReceive('createProperty')->with('TestRecord')->andReturn($record);
    
    $test->includeElement('TestRecord');
    
    expect($test->hasElement('intval'))->toBe(true);
    expect($test->hasElement('charval'))->toBe(true);
    expect($test->hasEmbed('TestRecord'))->toBe(false);
    expect($test->hasInclude('TestRecord'))->toBe(true);    
});

test('includeElement() fails when already included', function()
{
    $test = new EmptyPersistantRecord();
    
    $integer = \Mockery::mock(TypeInteger::class);
    $integer->shouldReceive('setOwner')->with($test)->andReturn($integer);
    $varchar = \Mockery::mock(TypeVarchar::class);
    $varchar->shouldReceive('setOwner')->with($test)->andReturn($varchar);
    
    $record = \Mockery::mock(AbstractRecordProperty::class);
    $record->shouldReceive('getElements')->andReturn(
        [
            'intval'=>$integer,
            'charval'=>$varchar
        ]
        );
    
    Properties::shouldReceive('createProperty')->with('integer')->andReturn($integer);
    Properties::shouldReceive('createProperty')->with('varchar')->andReturn($varchar);
    Properties::shouldReceive('createProperty')->with('TestRecord')->andReturn($record);
    
    $test->includeElement('TestRecord');    
    $test->includeElement('TestRecord');
})->throws(TypeAlreadyEmbeddedException::class);

test('includeElement() fails when called with wrong datatype', function()
{
    $test = new EmptyPersistantRecord();
    
    $integer = \Mockery::mock(TypeInteger::class);
    Properties::shouldReceive('createProperty')->with('integer')->andReturn($integer);
    
    $test->embedElement('integer');    
})->throws(TypeCannotBeEmbeddedException::class);

test('embedElement() fails when duplicate name', function()
{
    $test = new EmptyPersistantRecord();
    
    $integer = \Mockery::mock(TypeInteger::class);
    $integer->shouldReceive('setOwner')->with($test)->andReturn($integer);
    $varchar = \Mockery::mock(TypeVarchar::class);
    $varchar->shouldReceive('setOwner')->with($test)->andReturn($varchar);
    $record1 = \Mockery::mock(AbstractRecordProperty::class);
    $record1->shouldReceive('getElements')->andReturn(
        [
            'testint'=>$integer,
        ]
        );
    Properties::shouldReceive('createProperty')->with('integer')->andReturn($integer);
    Properties::shouldReceive('createProperty')->with('varchar')->andReturn($varchar);
    Properties::shouldReceive('createProperty')->with('EmbedRecord')->andReturn($record1);
    
    $test->appendElement('testint','integer');
    $test->embedElement('EmbedRecord');    
})->throws(DuplicateElementNameException::class);

test('includeElement() fails when duplicate name', function()
{
    $test = new EmptyPersistantRecord();
    
    $integer = \Mockery::mock(TypeInteger::class);
    $integer->shouldReceive('setOwner')->with($test)->andReturn($integer);
    $varchar = \Mockery::mock(TypeVarchar::class);
    $varchar->shouldReceive('setOwner')->with($test)->andReturn($varchar);
    $record1 = \Mockery::mock(AbstractRecordProperty::class);
    $record1->shouldReceive('getElements')->andReturn(
        [
            'testint'=>$integer,
        ]
        );
    Properties::shouldReceive('createProperty')->with('integer')->andReturn($integer);
    Properties::shouldReceive('createProperty')->with('varchar')->andReturn($varchar);
    Properties::shouldReceive('createProperty')->with('EmbedRecord')->andReturn($record1);
    
    $test->appendElement('testint','integer');
    $test->includeElement('EmbedRecord');
})->throws(DuplicateElementNameException::class);

test('getElement*() works', function() 
{
    $test = new EmptyPersistantRecord();
    
    $integer = \Mockery::mock(TypeInteger::class);
    $integer->shouldReceive('setOwner')->with($test)->andReturn($integer);
    $varchar = \Mockery::mock(TypeVarchar::class);
    $varchar->shouldReceive('setOwner')->with($test)->andReturn($varchar);
    $record1 = \Mockery::mock(AbstractRecordProperty::class);
    $record1->shouldReceive('getElements')->andReturn(
        [
            'intval1'=>$integer,
            'charval1'=>$varchar
        ]
        );
    $record2 = \Mockery::mock(AbstractRecordProperty::class);
    $record2->shouldReceive('getElements')->andReturn(
        [
            'intval2'=>$integer,
            'charval2'=>$varchar
        ]
        );
    Properties::shouldReceive('createProperty')->with('integer')->andReturn($integer);
    Properties::shouldReceive('createProperty')->with('varchar')->andReturn($varchar);
    Properties::shouldReceive('createProperty')->with('EmbedRecord')->andReturn($record1);
    Properties::shouldReceive('createProperty')->with('IncludeRecord')->andReturn($record2);
    
    $test->appendElement('testint','integer');
    $test->embedElement('EmbedRecord');
    $test->embedElement('IncludeRecord');

    $names = $test->getElementNames();
    sort($names);
    expect($names)->toBe(['charval1','charval2','intval1','intval2','testint']);
});

test('Method is passed to the storage', function($method) {
     $storage = \Mockery::mock(AbstractStorage::class);
     $storage->shouldReceive($method)->once()->andReturn(true);
     $test = new EmptyPersistantRecord();
     $test->setStorage($storage);
     
     $test->$method('dummy');
})->with(['isDirty','commit','rollback','migrate','upgrade','degrade','query']);

<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\Exceptions\StorageTableMissingException;
use Illuminate\Support\Facades\Schema;
use Sunhill\Storage\Exceptions\IDNotFoundException;
use Sunhill\Storage\Exceptions\InvalidIDException;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;

require_once('PrepareStorage.php');

uses(SunhillDatabaseTestCase::class);

test('fails when using a wrong id type', function()
{
    $test = new MysqlObjectStorage();
    $test->load('A');     
})->throws(InvalidIDException::class);

test('Read a dummy', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'dummy'));
    $test->load(1);
    
    expect($test->getValue('dummyint'))->toBe(123);
    expect($test->getIndexedValue('tags',0)->getID())->toBe(1);
});

test('Read a parentobject with array', function()
{
    
    $structure = 
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'parentobject'));
    $test->load(7);
    
    expect($test->getValue('parent_int'))->toBe(111);
    expect($test->getValue('parent_string'))->toBe('AAA');
    expect($test->getIndexedValue('parent_sarray',1))->toBe(11);
    
});

test('Read a parentobject with empty array', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'parentobject'));
    $test->load(8);
    
    expect($test->getValue('parent_int'))->toBe(222);
    expect($test->getValue('parent_string'))->toBe('BBB');
    expect($test->getElementCount('parent_sarray'))->toBe(0);
    
});

test('Read a childobject with both arrays', function()
{    
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    $test->load(9);
    
    expect($test->getValue('parent_int'))->toBe(333);
    expect($test->getValue('parent_string'))->toBe('CCC');
    expect($test->getIndexedValue('parent_sarray',1))->toBe(31);
    
    expect($test->getValue('child_int'))->toBe(212);
    expect($test->getValue('child_string'))->toBe('BCD');
    expect($test->getIndexedValue('child_sarray',1))->toBe(210);
});

test('Read a childobject with parent array', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    $test->load(10);
    
    expect($test->getValue('parent_int'))->toBe(444);
    expect($test->getValue('parent_string'))->toBe('DDD');
    expect($test->getIndexedValue('parent_sarray',1))->toBe(41);
    
    expect($test->getValue('child_int'))->toBe(222);
    expect($test->getValue('child_string'))->toBe('CDE');
    expect($test->getElementCount('child_sarray'))->toBe(0);
});

test('Read a childobject with child array', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    $test->load(11);
    
    expect($test->getValue('parent_int'))->toBe(555);
    expect($test->getValue('parent_string'))->toBe('EEE');
    expect($test->getElementCount('parent_sarray'))->toBe(0);
    
    expect($test->getValue('child_int'))->toBe(232);
    expect($test->getValue('child_string'))->toBe('DEF');
    expect($test->getIndexedValue('child_sarray',1))->toBe(410);
});

test('Read a childobject with both arrays empty', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    $test->load(12);
    
    expect($test->getValue('parent_int'))->toBe(666);
    expect($test->getValue('parent_string'))->toBe('FFF');
    expect($test->getElementCount('parent_sarray'))->toBe(0);
    
    expect($test->getValue('child_int'))->toBe(242);
    expect($test->getValue('child_string'))->toBe('EFG');
    expect($test->getElementCount('child_sarray'))->toBe(0);
});

it('fails when a table is missing', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    Schema::drop('parentobjects_parent_sarray');
    $test->load(12);
})->throws(StorageTableMissingException::class);

it('fails when reading an unknown id', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    $test->load(999);
})->throws(IDNotFoundException::class);

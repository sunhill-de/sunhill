<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\PoolMysqlStorage\PoolMysqlStorage;
use Sunhill\Storage\Exceptions\StorageTableMissingException;
use Illuminate\Support\Facades\Schema;
use Sunhill\Storage\Exceptions\IDNotFoundException;
use Sunhill\Storage\Exceptions\InvalidIDException;

require_once('PrepareStorage.php');

uses(SunhillDatabaseTestCase::class);

test('fails when using a wrong id type', function()
{
    $test = new PoolMysqlStorage();
    $test->delete('A');     
})->throws(InvalidIDException::class);

test('Delete a dummy', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'dummy'));
    $test->delete(1);
    
    $this->assertDatabaseMissing('objects',['id'=>1]);
    $this->assertDatabaseMissing('dummies',['id'=>1]);
});

test('Delete a parentobject with array', function()
{
    
    $structure = 
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'parentobject'));
    $test->delete(7);
    
    $this->assertDatabaseMissing('parentobjects',['id'=>7]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>7]);    
});

test('Delete a parentobject with empty array', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'parentobject'));
    $test->delete(8);
    
    $this->assertDatabaseMissing('parentobjects',['id'=>8]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>8]);
});

test('Delete a childobject with both arrays', function()
{    
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    $test->delete(9);
    
    $this->assertDatabaseMissing('parentobjects',['id'=>9]);
    $this->assertDatabaseMissing('childobjects',['id'=>9]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>9]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>9]);    
});

test('Delete a childobject with parent array', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    $test->delete(10);
    
    $this->assertDatabaseMissing('parentobjects',['id'=>10]);
    $this->assertDatabaseMissing('childobjects',['id'=>10]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>10]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>10]);
});

test('Read a childobject with child array', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    $test->delete(11);
    
    $this->assertDatabaseMissing('parentobjects',['id'=>11]);
    $this->assertDatabaseMissing('childobjects',['id'=>11]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>11]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>11]);
});

test('Read a childobject with both arrays empty', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    $test->delete(12);
    
    $this->assertDatabaseMissing('parentobjects',['id'=>12]);
    $this->assertDatabaseMissing('childobjects',['id'=>12]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>12]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>12]);
});

it('fails when a table is missing', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    Schema::drop('parentobjects_parent_sarray');
    $test->delete(12);
})->throws(StorageTableMissingException::class);

it('fails when reading an unknown id', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    $test->delete(999);
})->throws(IDNotFoundException::class);

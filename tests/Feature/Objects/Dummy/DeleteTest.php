<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\Exceptions\IDNotFoundException;
use Sunhill\Storage\Exceptions\InvalidIDException;

uses(SunhillDatabaseTestCase::class);

test('delete a dummy (not loaded by id)', function()
{
    Dummy::prepareDatabase($this);
    $write = new Dummy();
    
    $write->delete(1);
    
    $this->assertDatabaseMissing('dummies',['id'=>1]);
});

test('delete a dummy (not found)', function()
{
    Dummy::prepareDatabase($this);
    $write = new Dummy();
    
    $write->delete(999);    
})->throws(IDNotFoundException::class);

test('delete a dummy (loaded)', function()
{
    Dummy::prepareDatabase($this);
    $write = new Dummy();
    $write->load(1);
    $write->delete();
    
    $this->assertDatabaseMissing('dummies',['id'=>1]);    
})->group('delete');

test('delete a dummy static', function()
{
    Dummy::prepareDatabase($this);
    Dummy::erase(1);
    
    $this->assertDatabaseMissing('dummies',['id'=>1]);    
})->group('delete');

test('delete a dummy static (not found)', function()
{
    Dummy::prepareDatabase($this);
    Dummy::erase(999);
})->group('delete')->throws(IDNotFoundException::class);

test('delete a dummy static (invalid id)', function()
{
    Dummy::prepareDatabase($this);
    Dummy::erase('abc');
})->group('delete')->throws(InvalidIDException::class);

test('delete a dummy (id is a dummy child)', function()
{
    Dummy::prepareDatabase($this);
    Dummy::erase(13);
    
    $this->assertDatabaseMissing('dummies',['id'=>13]);    
    $this->assertDatabaseMissing('dummychildren',['id'=>13]);
})->group('delete');

test('delete a dummy (id is a childobject)', function()
{
    Dummy::prepareDatabase($this);
    Dummy::erase(9);
    
    $this->assertDatabaseMissing('dummies',['id'=>9]);
    $this->assertDatabaseMissing('dummychildren',['id'=>9]);
})->group('delete');

<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;

uses(SunhillDatabaseTestCase::class);

test('commit with nothing to do', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(1);
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>1,'dummyint'=>123]);
    
})->group('update');

test('modify a dummy', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(1);
    $test->dummyint = 20;
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>1,'dummyint'=>20]);
})->group('update');

test('modify the object data of a dummy', function()
{
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    $test->load(1);
    $test->_read_cap = 'READERS';
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>1,'dummyint'=>123]);
    $this->assertDatabaseHas('objects',['id'=>1,'_read_cap'=>'READERS']);
})->group('update');

test('add a tag to a previously untagged dummy', function()
{
     
})->skip();

test('append a tag to a dummy', function()
{
    
})->skip();

test('remove a tag from a dummy', function()
{
    
})->skip();

test('clear tags from a dummy', function()
{
    
})->skip();


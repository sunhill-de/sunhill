<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Illuminate\Support\Facades\DB;
use Sunhill\Properties\Exceptions\NoDefaultSetException;
use Sunhill\Properties\Exceptions\InvalidValueException;

uses(SunhillDatabaseTestCase::class);

test('create a dummy with just dummyint', function()
{
    Dummy::prepareDatabase($this);
    $test = new Dummy();
    $test->create();
    $test->dummyint = 10;
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>$test->getID(),'dummyint'=>10]);
    $query = DB::table('objects')->where('id',$test->getID())->first();
    expect(is_null($query->_created_at))->toBe(false);
    expect(($query->_created_at == $query->_updated_at))->toBe(true);
})->group('create');

test('create a dummy and assign a tag to it', function()
{
    Dummy::prepareDatabase($this);
    $test = new Dummy();
    $test->create();
    $test->dummyint = 10;
    $test->_tags[] = 1;
    $test->commit();    
    $this->assertDatabaseHas('dummies',['id'=>$test->getID(),'dummyint'=>10]);
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>$test->getID(),'tag_id'=>1]);
})->group('create')->group('tag');

test('create a dummy and assign an attribute to it', function()
{
    Dummy::prepareDatabase($this);
    $test = new Dummy();
    $test->create();
    $test->dummyint = 10;
    $test->_tags[] = 1;
    $test->commit();
    $this->assertDatabaseHas('dummies',['id'=>$test->getID(),'dummyint'=>10]);
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>$test->getID(),'tag_id'=>1]);
})->group('create')->group('attribute')->skip();


it('fails when assigning wrong type', function()
{
    Dummy::prepareDatabase($this);
    $test = new Dummy();
    $test->create();
    $test->dummyint = 'ABC';
})->group('create')->throws(InvalidValueException::class);

it('fails when comitting without default value', function()
{
    Dummy::prepareDatabase($this);
    $test = new Dummy();
    $test->create();
    $test->commit();    
})->group('create')->throws(NoDefaultSetException::class);

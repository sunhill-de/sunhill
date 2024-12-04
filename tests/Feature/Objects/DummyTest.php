<?php

use Sunhill\Tests\SunhillKeepingDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Facades\Properties;
use Illuminate\Support\Facades\DB;

uses(SunhillKeepingDatabaseTestCase::class);

test('migrate Dummy', function()
{
    Properties::registerProperty(Dummy::class);
    Dummy::migrate();
    
    $this->assertDatabaseHasTable('dummies');
});

test('create a dummy', function()
{
    $test = new Dummy();
    $test->create();
    $test->dummyint = 10;
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>1,'dummyint'=>10]);
    $query = DB::table('objects')->where('id',1)->first();
    expect(is_null($query->_created_at))->toBe(false);
    expect(($query->_created_at == $query->_updated_at))->toBe(true);
})->depends('migrate Dummy');

test('load a dummy', function()
{
    $write = new Dummy();
    $write->create();
    $write->dummyint = 10;
    $write->commit();
    
    $id = $write->getID();
    expect($id)->toBe(1);
    
    $test = new Dummy();
    $test->load($id);
    expect($test->dummyint)->toBe(10);
})->depends('create a dummy');
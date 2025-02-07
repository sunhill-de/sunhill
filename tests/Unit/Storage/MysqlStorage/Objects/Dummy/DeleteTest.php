<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\Dummy;

uses(SunhillDatabaseTestCase::class);

test('delete a dummy with tags', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(Dummy::getExpectedStructure());
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    
    $this->assertDatabaseHas('dummies',['id'=>1]);
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1]);
    
    $test->delete(1);
    
    $this->assertDatabaseMissing('dummies',['id'=>1]);
    $this->assertDatabaseMissing('tagobjectassigns',['container_id'=>1]);
});

test('delete a dummy without tags', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(Dummy::getExpectedStructure());
    Dummy::prepareDatabase($this);
    
    $test = new Dummy();
    
    $this->assertDatabaseHas('dummies',['id'=>5]);
    $this->assertDatabaseMissing('tagobjectassigns',['container_id'=>5]);
    
    $test->delete(5);
    
    $this->assertDatabaseMissing('dummies',['id'=>5]);
    $this->assertDatabaseMissing('tagobjectassigns',['container_id'=>5]);
});
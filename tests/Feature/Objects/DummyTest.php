<?php

use Sunhill\Tests\SunhillKeepingDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Facades\Properties;

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
    
    $this->assertDatabaseHas('dummies',['dummyint'=>10]);
})->depends('migrate Dummy');
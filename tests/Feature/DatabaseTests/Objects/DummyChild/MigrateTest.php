<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Illuminate\Support\Facades\Schema;

uses(SunhillDatabaseTestCase::class);

test('migrate DummyChild with nothing to do', function()
{
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    DummyChild::migrate();
    
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseHasTable('dummychildren');
});

test('migrate DummyChild with both tables missing', function()
{
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);

    Schema::dropIfExists('dummies');
    Schema::dropIfExists('dummychildren');
    
    DummyChild::migrate();
    
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseHasTable('dummychildren');
});

test('migrate DummyChild with dummychildren table missing', function()
{
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    
    Schema::dropIfExists('dummychildren');
    
    DummyChild::migrate();
    
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseHasTable('dummychildren');
});

test('migrate DummyChild with dummies table missing', function()
{
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    
    Schema::dropIfExists('dummies');
    
    DummyChild::migrate();
    
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseHasTable('dummychildren');
});


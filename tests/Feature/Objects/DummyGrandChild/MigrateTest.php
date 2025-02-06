<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;

uses(SunhillDatabaseTestCase::class);

test('migrate DummyGrandChild', function()
{
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    Properties::registerProperty(DummyGrandChild::class);
    DummyGrandChild::migrate();
    
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseHasTable('dummychildren');
    $this->assertDatabaseHasTable('dummygrandchildren');
});


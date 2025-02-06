<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\DummyChild;

uses(SunhillDatabaseTestCase::class);

test('migrate DummyChild', function()
{
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    DummyChild::migrate();
    
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseHasTable('dummychildren');
});


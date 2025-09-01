<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;

uses(SunhillDatabaseTestCase::class);

test('migrate Dummy', function()
{
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(SkippingDummyChild::class);
    Properties::registerProperty(SkippingDummyGrandChild::class);
    SkippingDummyGrandChild::migrate();
    
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseHasTable('skippingdummychildren');
    $this->assertDatabaseHasTable('skippingdummygrandchildren');
});


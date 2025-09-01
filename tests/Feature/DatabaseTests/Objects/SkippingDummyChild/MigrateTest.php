<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;

uses(SunhillDatabaseTestCase::class);

test('migrate SkippingDummyChild', function()
{
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(SkippingDummyChild::class);
    SkippingDummyChild::migrate();
    
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseHasTable('skippingdummychildren');
});


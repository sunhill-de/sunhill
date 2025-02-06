<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Objects\Dummy;

uses(SunhillDatabaseTestCase::class);

test('migrate Dummy', function()
{
    Properties::registerProperty(Dummy::class);
    Dummy::migrate();
    
    $this->assertDatabaseHasTable('dummies');
});


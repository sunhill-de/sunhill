<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\ParentObject;

uses(SunhillDatabaseTestCase::class);

test('migrate ParentObject', function()
{
    Properties::registerProperty(ParentObject::class);
    ParentObject::migrate();
    
    $this->assertDatabaseHasTable('parentobjects');
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
});



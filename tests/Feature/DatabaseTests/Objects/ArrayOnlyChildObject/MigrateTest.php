<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\ArrayOnlyChildObject;

uses(SunhillDatabaseTestCase::class);

test('migrate ArrayOnlyChildObject', function()
{
    Properties::registerProperty(ParentObject::class);
    Properties::registerProperty(ArrayOnlyChildObject::class);
    ArrayOnlyChildObject::migrate();
    
    $this->assertDatabaseHasTable('parentobjects');
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
    $this->assertDatabaseHasTable('arrayonlychildobjects');
    $this->assertDatabaseHasTable('arrayonlychildobjects_child_sarray');
});


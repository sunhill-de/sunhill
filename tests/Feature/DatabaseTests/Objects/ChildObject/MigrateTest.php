<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Tests\TestSupport\Objects\ParentObject;

uses(SunhillDatabaseTestCase::class);

test('migrate ChildObject', function()
{
    Properties::registerProperty(ParentObject::class);
    Properties::registerProperty(ChildObject::class);
    ChildObject::migrate();
    
    $this->assertDatabaseHasTable('parentobjects');
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
    $this->assertDatabaseHasTable('childobjects');
    $this->assertDatabaseHasTable('childobjects_child_sarray');
});


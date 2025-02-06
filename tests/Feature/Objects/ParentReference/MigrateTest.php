<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\Database\Seeds\ParentReferences_parent_rarraySeeder;
use Sunhill\Tests\TestSupport\Objects\ParentReference;

uses(SunhillDatabaseTestCase::class);

test('migrate ParentReference', function()
{
    Properties::registerProperty(ParentReference::class);
    ParentReference::migrate();
    
    $this->assertDatabaseHasTable('parentreferences');
    $this->assertDatabaseHasTable('parentreferences_parent_rarray');
});


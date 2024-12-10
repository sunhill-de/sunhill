<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\Scenarios\Attributes\AttributeScenario;
use Sunhill\Tests\TestSupport\Attributes\SimpleIntAttribute;

uses(SunhillDatabaseTestCase::class);

test('test read simple int attribute', function()
{
   $scenario = new AttributeScenario($this);
   $scenario->migrate();
   $scenario->seed();
   
   $test = new SimpleIntAttribute();
   $test->load(3);
   expect($test->value)->toBe(999);
});
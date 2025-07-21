<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\BigParent;

uses(SunhillDatabaseTestCase::class);

test('Migrate and seed', function()
{
    BigParent::migrate();
    foreach (BigParent::$TEST_DATA as $entry) {
        $object = new BigParent();
        foreach ($entry as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $array_key => $array_value) {                   
                    if (is_int($array_value)) {
                        $object->$key[] = $array_value;
                    } else {
                        $object->$key[$array_key] = $array_value;
                    }
                }
            } else {
                $object->$key = $value;
            }
        }
        $object->commit();
    }
    $this->assertDatabaseHasColumn('bigparent',['p_string'=>'Iron Maiden']);
});
<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\BigParent;
use Sunhill\Tests\Database\Seeds\ObjectsSeeder;
use Sunhill\Tests\Database\Seeds\DummiesSeeder;
use Sunhill\Tests\Database\Seeds\TagsSeeder;

uses(SunhillDatabaseTestCase::class);

test('Migrate and seed', function()
{
    $this->seed([
        ObjectsSeeder::class,
        DummiesSeeder::class,
        TagsSeeder::class,
    ]);
    BigParent::migrate();
    foreach (BigParent::$TEST_DATA as $entry) {
        $object = new BigParent();
        $object->create();
        foreach ($entry as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $array_key => $array_value) {                   
                    if (is_int($array_key)) {
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
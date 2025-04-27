<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tests\Unit\Storage\AbstractObjectStorage\DummyAbstractObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Tests\TestSupport\Objects\ParentObject;

uses(SunhillTestCase::class);

function convertStructure(array $input): \stdClass
{
    $expected = new \stdClass();
    foreach ($input as $field=>$info) {
        if (is_array($info)) {
            $expected->$field = convertStructure($info);
        } else {
            $expected->$field = $info;
        }
    }
    return $expected;
}

test('assembleStructure', function($class, $storage_id, $structure)
{
    $test = new DummyAbstractObjectStorage();
    $test->setStructure($class::getExpectedStructure());
    $expected = convertStructure($structure);
    expect($test->assembleStructure($storage_id) == $expected)->toBe(true);
})->with([
    [ParentObject::class,'parentobjects',[
        'parent_int'=>['type'=>'integer'],
        'parent_string'=>['type'=>'string','max_len'=>3],
        'parent_sarray'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
    ]],
    [ChildObject::class,'childobjects',[
        'child_int'=>['type'=>'integer'],
        'child_string'=>['type'=>'string','max_len'=>3],
        'child_sarray'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
    ]],
]);

test('getStructureDiff()', function($new, $old, $expected)
{
    $test = new DummyAbstractObjectStorage();
    $result = $test->getStructureDiff(convertStructure($old), convertStructure($new));

    expect($result == convertStructure($expected))->toBe(true);
})->with([
    'both the same'=>[
        [
            'test_int'=>['type'=>'integer'],            
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_int'=>['given'=>[],'new'=>[]],
            'test_string'=>['given'=>[],'new'=>[]],
            'test_array'=>['given'=>[],'new'=>[]],
        ]
    ],
    'both the same with joker'=>[
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_int'=>['*'],
            'test_string'=>['*'],
            'test_array'=>['*']
        ],
        [
            'test_int'=>['given'=>[],'new'=>[]],
            'test_string'=>['given'=>[],'new'=>[]],
            'test_array'=>['given'=>[],'new'=>[]],
        ]
    ],
    'Standard field appended'=>[
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_int'=>['given'=>[],'new'=>['type'=>'integer']],
            'test_string'=>['given'=>[],'new'=>[]],
            'test_array'=>['given'=>[],'new'=>[]],
        ]
    ],
    'Standard field appended with given as joker'=>[
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_string'=>['*'],
            'test_array'=>['*']
        ],
        [
            'test_int'=>['given'=>[],'new'=>['type'=>'integer']],
            'test_string'=>['given'=>[],'new'=>[]],
            'test_array'=>['given'=>[],'new'=>[]],
        ]
    ],
    'Standard field dropped'=>[
        [
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_int'=>['given'=>['type'=>'integer'],'new'=>[]],
            'test_string'=>['given'=>[],'new'=>[]],
            'test_array'=>['given'=>[],'new'=>[]],
        ]
    ],
    'Standard field dropped with given as joker'=>[
        [
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_int'=>['*'],
            'test_string'=>['*'],
            'test_array'=>['*']
        ],
        [
            'test_int'=>['given'=>['*'],'new'=>[]],
            'test_string'=>['given'=>[],'new'=>[]],
            'test_array'=>['given'=>[],'new'=>[]],
        ]
    ],
    'Array field appended'=>[
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>3],
        ],
        [
            'test_int'=>['given'=>[],'new'=>[]],
            'test_string'=>['given'=>[],'new'=>[]],
            'test_array'=>['given'=>[],'new'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']],
        ]
    ],
    'Array field dropped'=>[
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>3],
        ],
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_int'=>['given'=>[],'new'=>[]],
            'test_string'=>['given'=>[],'new'=>[]],
            'test_array'=>['given'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer'],'new'=>[]],
        ]
    ],
    'Standard field type changed'=>[
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_int'=>['type'=>'string'],
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_int'=>['given'=>['type'=>'string'],'new'=>['type'=>'integer']],
            'test_string'=>['given'=>[],'new'=>[]],
            'test_array'=>['given'=>[],'new'=>[]],
        ]
    ],
    'Standard field attribute changed'=>[
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>30],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_int'=>['given'=>[],'new'=>[]],
            'test_string'=>['given'=>['max_len'=>30],'new'=>['max_len'=>3]],
            'test_array'=>['given'=>[],'new'=>[]],
        ]
    ],
    'Standard field attribute changed with joker'=>[
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>'*'],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_int'=>['given'=>[],'new'=>[]],
            'test_string'=>['given'=>[],'new'=>[]],
            'test_array'=>['given'=>[],'new'=>[]],
        ]
    ],
    'Array field index type changed'=>[
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'string','element_type'=>'integer']
        ],
        [
            'test_int'=>['given'=>[],'new'=>[]],
            'test_string'=>['given'=>[],'new'=>[]],
            'test_array'=>['given'=>['index_type'=>'string'],'new'=>['index_type'=>'integer']],
        ]
    ],
    'Array field element type changed'=>[
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'string']
        ],
        [
            'test_int'=>['given'=>[],'new'=>[]],
            'test_string'=>['given'=>[],'new'=>[]],
            'test_array'=>['given'=>['element_type'=>'string'],'new'=>['element_type'=>'integer']],
        ]
    ], 
    'Complete table appended'=>[
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            '€'            
        ],
        [
            'test_int'=>['given'=>['€'],'new'=>['type'=>'integer']],
            'test_string'=>['given'=>['€'],'new'=>['type'=>'string','max_len'=>3]],
            'test_array'=>['given'=>['€'],'new'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']],
        ]
    ],
    'Complete table with joker'=>[
        [
            'test_int'=>['type'=>'integer'],
            'test_string'=>['type'=>'string','max_len'=>3],
            'test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer']
        ],
        [
            '*'
        ],
        [
            'test_int'=>['given'=>[],'new'=>[]],
            'test_string'=>['given'=>[],'new'=>[]],
            'test_array'=>['given'=>[],'new'=>[]],
        ]
    ]
]);

test('Migrate just the parent', function()
{
    $test = new DummyAbstractObjectStorage();
    $test::$DataPool = $test::$Data;
    unset($test::$DataPool['childobjects']);
    unset($test::$DataPool['childobjects_child_sarray']);
    unset($test::$DataPool['parentobjects']);
    unset($test::$DataPool['parentobjects_parent_sarray']);
    $test->setStructure(ParentObject::getExpectedStructure());
    
    $test->migrate();
    
    expect(array_key_exists($test::$DataPool['parentobjects']))->toBe(true);
    expect(array_key_exists($test::$DataPool['parentobjects_parent_sarray']))->toBe(true);
});

test('Migrate the child with existing parent', function()
{
    $test = new DummyAbstractObjectStorage();
    $test::$DataPool = $test::$Data;
    unset($test::$DataPool['childobjects']);
    unset($test::$DataPool['childobjects_child_sarray']);
    $test->setStructure(ChildObject::getExpectedStructure());
    
    $test->migrate();
    
    expect(array_key_exists($test::$DataPool['parentobjects']))->toBe(true);
    expect(array_key_exists($test::$DataPool['parentobjects_parent_sarray']))->toBe(true);
    expect(array_key_exists($test::$DataPool['childobjects']))->toBe(true);
    expect(array_key_exists($test::$DataPool['childobjects_child_sarray']))->toBe(true);
});

test('Migrate the child without existing parent', function()
{
    $test = new DummyAbstractObjectStorage();
    $test::$DataPool = $test::$Data;
    unset($test::$DataPool['childobjects']);
    unset($test::$DataPool['childobjects_child_sarray']);
    unset($test::$DataPool['parentobjects']);
    unset($test::$DataPool['parentobjects_parent_sarray']);
    $test->setStructure(ChildObject::getExpectedStructure());
    
    $test->migrate();
    
    expect(array_key_exists($test::$DataPool['parentobjects']))->toBe(true);
    expect(array_key_exists($test::$DataPool['parentobjects_parent_sarray']))->toBe(true);
    expect(array_key_exists($test::$DataPool['childobjects']))->toBe(true);
    expect(array_key_exists($test::$DataPool['childobjects_child_sarray']))->toBe(true);
});

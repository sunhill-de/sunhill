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
    expect($test->assembleStructure($storage_id))->toEqual($expected);
})->with([
    [ParentObject::class,'parentobjects',[
        'parentobjects'=>[
            'parent_int'=>['type'=>'integer'],
            'parent_string'=>['type'=>'string','max_len'=>3],
        ],
        'parentobjects_parent_sarray'=>[
            'type'=>'array','index_type'=>'integer','element_type'=>'integer'
        ]
    ]],
    [ChildObject::class,'childobjects',[
        'parentobjects'=>[
            'parent_int'=>['type'=>'integer'],
            'parent_string'=>['type'=>'string','max_len'=>3],
        ],
        'parentobjects_parent_sarray'=>[
            'type'=>'array','index_type'=>'integer','element_type'=>'integer'
        ],
        'childobjects'=>[
            'child_int'=>['type'=>'integer'],
            'child_string'=>['type'=>'string','max_len'=>3],            
        ],        
        'childobjects_child_sarray'=>[
            'type'=>'array',
            'index_type'=>'integer',
            'element_type'=>'integer']
    ]],
]);

test('getStructureDiff()', function($new, $old, $expected)
{
    $test = new DummyAbstractObjectStorage();
    $result = $test->getStructureDiff(convertStructure($old), convertStructure($new));
    $expected = convertStructure($expected);
//    expect($result == convertStructure($expected))->toBe(true);
    expect($result)->toEqual($expected);
})->with([
   'both the same'=>[
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],                
            ],
            'someobject_test_array'=>[
                'type'=>'array',
                'index_type'=>'integer',
                'element_type'=>'integer'                
            ]
        ],
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
            'someobject_test_array'=>[
                'type'=>'array',
                'index_type'=>'integer',
                'element_type'=>'integer'
            ]
        ],
        [
            'given'=>[],
            'new'=>[]
        ]
    ],
    'both the same with joker'=>[
        [
            'someobject'=>
            [
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
            'someobject_test_array'=>
            [
                'type'=>'array',
                'index_type'=>'integer',
                'element_type'=>'integer'
            ]
        ],
        [
            'someobject'=>'*',
            'someobject_test_array'=>'*'
        ],
        [
            'given'=>[],
            'new'=>[]
        ]
    ],
    'Standard field appended'=>[
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
            'someobject_test_array'=>[
                'type'=>'array',
                'index_type'=>'integer',
                'element_type'=>'integer'
            ]
        ],
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
            ],
            'someobject_test_array'=>[
                'type'=>'array',
                'index_type'=>'integer',
                'element_type'=>'integer'
            ]
        ],
        [
            'given'=>[],
            'new'=>['someobject'=>['test_string'=>['type'=>'string','max_len'=>3]]]
        ]
    ],
    
    'Standard field appended with given as joker'=>[
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
            'someobject_test_array'=>[
                'type'=>'array',
                'index_type'=>'integer',
                'element_type'=>'integer'
            ]
        ],
        [
            'someobject_test_array'=>'*'
        ],
        [
            'given'=>[],
            'new'=>['someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ]]            
        ]
    ],
    'Standard field dropped'=>[
        [
            'someobject'=>[
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
            'someobject_test_array'=>[
                'type'=>'array',
                'index_type'=>'integer',
                'element_type'=>'integer'
            ]
        ],
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
            'someobject_test_array'=>[
                'type'=>'array',
                'index_type'=>'integer',
                'element_type'=>'integer'
            ]
        ],
        [
            'given'=>['someobject'=>['test_int'=>['type'=>'integer']]],
            'new'=>[],
        ]
    ],
    'Standard field dropped with given as joker'=>[
        [
            'someobject_test_array'=>[
                'type'=>'array',
                'index_type'=>'integer',
                'element_type'=>'integer'
            ]
        ],
        [
            'someobject'=>'*',
            'someobject_test_array'=>'*'
        ],
        [
            'given'=>['someobject'=>'*'],
            'new'=>[]
        ]
    ],
    'Array field appended'=>[
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],                
            ],
            'someobject_test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer'],
        ],
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
        ],
        [
            'given'=>[],
            'new'=>[
                'someobject_test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer'],                
            ]            
        ]
    ],
    'Array field dropped'=>[
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
        ],
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
            'someobject_test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer'],
        ],
        [
            'given'=>['someobject_test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer'],],
            'new'=>[]
        ]
    ],
    'Standard field type changed (int)'=>[
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
            'someobject_test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer'],
        ],
        [
            'someobject'=>[
                'test_int'=>['type'=>'string','max_len'=>3],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
            'someobject_test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer'],
        ],
        [
            'given'=>['someobject'=>['test_int'=>['type'=>'string','max_len'=>3]]],
            'new'=>['someobject'=>['test_int'=>['type'=>'integer']]],
        ]
    ],
    'Standard field type changed (string)'=>[
                [
                    'someobject'=>[
                        'test_int'=>['type'=>'integer'],
                        'test_string'=>['type'=>'string','max_len'=>3],
                    ],
                    'someobject_test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer'],
                ],
                [
                    'someobject'=>[
                        'test_int'=>['type'=>'integer'],
                        'test_string'=>['type'=>'integer'],
                    ],
                    'someobject_test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer'],
                ],
                [
                    'given'=>['someobject'=>['test_string'=>['type'=>'integer']]],
                    'new'=>['someobject'=>['test_string'=>['type'=>'string','max_len'=>3]]]
                ],
   ],                 
   'Standard field attribute changed'=>[
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
            'someobject_test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer'],
        ],
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>30],
            ],
            'someobject_test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer'],
        ],
        [
            'given'=>['someobject'=>['test_string'=>['max_len'=>30]]],
            'new'=>['someobject'=>['test_string'=>['max_len'=>3]]]
        ]
    ],
    'Standard field attribute changed with joker'=>[
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
            'someobject_test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer'],
        ],
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>'*'],
            ],
            'someobject_test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer'],
        ],
        [
            'given'=>[],
            'new'=>[]
        ]
    ],
    'Array field index type changed'=>[
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
            'someobject_test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer'],
        ],
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
            'someobject_test_array'=>['type'=>'array','index_type'=>'string','element_type'=>'integer'],
        ],
        [
            'given'=>['someobject_test_array'=>['index_type'=>'string']],
            'new'=>['someobject_test_array'=>['index_type'=>'integer']],            
        ]
    ],
    'Array field element type changed'=>[
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
            'someobject_test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer'],
        ],
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
            'someobject_test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'string'],
        ],
        [
            'given'=>['someobject_test_array'=>['element_type'=>'string']],
            'new'=>['someobject_test_array'=>['element_type'=>'integer']],
        ]
    ], 
    'Array field with asterik in given'=>[
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
            'someobject_test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer'],
        ],
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
            'someobject_test_array'=>['type'=>'array','index_type'=>'*','element_type'=>'*'],
        ],
        [
            'given'=>[],
            'new'=>[],
        ]
    ],
    'Complete table appended'=>[
        [
            'someobject'=>[
                'test_int'=>['type'=>'integer'],
                'test_string'=>['type'=>'string','max_len'=>3],
            ],
            'someobject_test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer'],
        ],
        [
        ],
        [
            'given'=>[],
            'new'=>[
                'someobject'=>[
                    'test_int'=>['type'=>'integer'],
                    'test_string'=>['type'=>'string','max_len'=>3],
                ],
                'someobject_test_array'=>['type'=>'array','index_type'=>'integer','element_type'=>'integer'],
            ],                        
        ]
    ],    
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
    
    expect(array_key_exists('parentobjects',$test::$DataPool))->toBe(true);
    expect(array_key_exists('parentobjects_parent_sarray',$test::$DataPool))->toBe(true);
});

test('Migrate the child with existing parent', function()
{
    $test = new DummyAbstractObjectStorage();
    $test::$DataPool = $test::$Data;
    unset($test::$DataPool['childobjects']);
    unset($test::$DataPool['childobjects_child_sarray']);
    $test->setStructure(ChildObject::getExpectedStructure());
    
    $test->migrate();
    
    expect(array_key_exists('parentobjects',$test::$DataPool))->toBe(true);
    expect(array_key_exists('parentobjects_parent_sarray',$test::$DataPool))->toBe(true);
    expect(array_key_exists('childobjects',$test::$DataPool))->toBe(true);
    expect(array_key_exists('childobjects_child_sarray',$test::$DataPool))->toBe(true);
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
    
    expect(array_key_exists('parentobjects',$test::$DataPool))->toBe(true);
    expect(array_key_exists('parentobjects_parent_sarray',$test::$DataPool))->toBe(true);
    expect(array_key_exists('childobjects',$test::$DataPool))->toBe(true);
    expect(array_key_exists('childobjects_child_sarray',$test::$DataPool))->toBe(true);
});

<?php

namespace Sunhill\Tests\TestSupport\Objects;

use Sunhill\Objects\ORMObject;
use Sunhill\Types\TypeInteger;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Types\TypeVarchar;
use Sunhill\Tests\Database\Seeds\ObjectsSeeder;
use Sunhill\Tests\Database\Seeds\TagsSeeder;
use Sunhill\Tests\Database\Seeds\TagCacheSeeder;
use Sunhill\Tests\Database\Seeds\TagObjectAssignsSeeder;
use Sunhill\Tests\Database\Seeds\ParentObjectsSeeder;
use Sunhill\Tests\Database\Seeds\ParentObjects_parent_sarraySeeder;
use Sunhill\Types\TypeFloat;
use Sunhill\Types\TypeDate;
use Sunhill\Types\TypeTime;
use Sunhill\Types\TypeDateTime;
use Sunhill\Types\TypeBoolean;

class BigParent extends ORMObject
{
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->addProperty(TypeInteger::class,'p_int')->default(100);
        $builder->addProperty(TypeVarchar::class,'p_string')->setMaxLen(30);
        $builder->addProperty(TypeFloat::class,'p_float');
        $builder->addProperty(TypeDate::class,'p_date')->nullable()->default(null);
        $builder->addProperty(TypeTime::class,'p_time')->nullable();
        $builder->addProperty(TypeDateTime::class,'p_datetime');
        $builder->addProperty(TypeBoolean::class,'p_bool')->default(true);
        $builder->referRecord(Dummy::class, 'p_dummy_ref')->nullable()->default(null);
        $builder->referRecord(BigParent::class,'p_self_ref')->setAllowedProperty(BigParent::class)->nullable()->default(null);
        $builder->referRecord(Dummy::class,'p_mult_ref')->setAllowedProperty([Dummy::class,ChildObject::class]);
        $builder->array('p_s_array')->setAllowedElementType(TypeVarchar::class);
        $builder->array('p_i_array')->setAllowedElementType(TypeInteger::class);
        $builder->array('p_f_array')->setAllowedElementType(TypeFloat::class);
        $builder->array('p_s_map')->setIndexType('string')->setAllowedElementType(TypeVarchar::class);
        $builder->array('p_i_map')->setIndexType('string')->setAllowedElementType(TypeInteger::class);
        $builder->array('p_f_map')->setIndexType('string')->setAllowedElementType(TypeFloat::class);
        $builder->arrayOfReferences('p_r_array')->setAllowedElementType(DummyChild::class);
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'BigParent');
        static::addInfo('description', 'A more complex object for feature tests.', true);
        static::addInfo('storage_id', 'bigparents');
        static::addInfo('taggable', true);
        static::addInfo('attributable', true);
    }

    public static $TEST_DATA = [
        [
            'p_int'=>837,
            'p_string'=>'Iron Maiden',
            'p_float'=>92.23,
            'p_date'=>'2025-07-21',
            'p_time'=>'06:35:10',
            'p_datetime'=>'2020-12-24 18:00:00',
            'p_bool'=>true,
            'p_dummy_ref'=>1,
            'p_self_ref'=>null,
            'p_mult_ref'=>2,
            'p_s_array'=>['php','c','c++'],
            'p_i_array'=>[498,296,199],
            'p_f_array'=>[3.89,2.99,12.986],
            'p_s_map'=>['keyA'=>'ValueA','keyB'=>'ValueB'],
            'p_i_map'=>['No1'=>498,'No2'=>296,'LastNo'=>199],
            'p_f_map'=>['a'=>3.89,'b'=>2.99,'c'=>12.986],
          //  'p_r_array'=>[1,2,3]
        ],
        [
            'p_int'=>294,
            'p_string'=>'Muse',
            'p_float'=>1.92,
            'p_date'=>'2022-06-21',
            'p_time'=>'06:40:00',
            'p_datetime'=>'2022-11-24 17:23:11',
            'p_bool'=>false,
            'p_dummy_ref'=>2,
            'p_self_ref'=>null,
            'p_mult_ref'=>1,
            'p_s_array'=>['pascal','fortran','c++'],
            'p_i_array'=>[198,432,832],
            'p_f_array'=>[3.89,2.99,12.986],
            'p_s_map'=>['keyA'=>'NewA','keyB'=>'NewB','keyC'=>'NewC'],
            'p_i_map'=>['No1'=>111,'No2'=>298],
            'p_f_map'=>['a'=>192.22,'c'=>9.445],
          //  'p_r_array'=>[2]
        ]
        
    ];

    public static function getExpectedStructure()
    {
        $result = new \stdClass();
        $result->name = "bigparents";
        $result->type = "record";
        $result->elements = [];
        
        $result->elements['p_int'] = makeStdClass([
            'name'=>'p_int',
            'type'=>'integer',
            'storage_subid'=>'bigparents',
            'default'=>100
        ]);
        $result->elements['p_string'] = makeStdClass([
            'name'=>'p_string',
            'type'=>'string',
            'storage_subid'=>'bigparents',
            'max_length'=>30
        ]);
        $result->elements['p_float'] = makeStdClass([
            'name'=>'p_float',
            'type'=>'float',
            'storage_subid'=>'bigparents'
        ]);
        $result->elements['p_date'] = makeStdClass([
            'name'=>'p_date',
            'type'=>'date',
            'storage_subid'=>'bigparents',
            'nullable'=>true,
            'default'=>null
        ]);
        $result->elements['p_time'] = makeStdClass([
            'name'=>'p_time',
            'type'=>'time',
            'storage_subid'=>'bigparents',
            'nullable'=>true,
            'default'=>null
        ]);
        $result->elements['p_datetime'] = makeStdClass([
            'name'=>'p_datetime',
            'type'=>'datetime',
            'storage_subid'=>'bigparents'
        ]);
        $result->elements['p_bool'] = makeStdClass([
            'name'=>'p_bool',
            'type'=>'boolean',
            'storage_subid'=>'bigparents',
            'default'=>true
        ]);
        $result->elements['p_dummy_ref'] = makeStdClass([
            'name'=>'p_dummy_ref',
            'type'=>'record',
            'storage_subid'=>'bigparents',
            'nullable'=>true,
            'default'=>null
        ]);
        $result->elements['p_self_ref'] = makeStdClass([
            'name'=>'p_self_ref',
            'type'=>'record',
            'storage_subid'=>'bigparents',
            'nullable'=>true,
            'default'=>null
        ]);
        $result->elements['p_mult_ref'] = makeStdClass([
            'name'=>'p_mult_ref',
            'type'=>'record',
            'storage_subid'=>'bigparents'
        ]);
        $result->elements['p_s_array'] = makeStdClass([
            'name'=>'p_s_array',
            'type'=>'array',
            'storage_subid'=>'bigparents',
            'element_type'=>'string',
            'index_type'=>'integer'
        ]);
        $result->elements['p_i_array'] = makeStdClass([
            'name'=>'p_i_array',
            'type'=>'array',
            'storage_subid'=>'bigparents',
            'element_type'=>'integer',
            'index_type'=>'integer'
        ]);
        $result->elements['p_f_array'] = makeStdClass([
            'name'=>'p_f_array',
            'type'=>'array',
            'storage_subid'=>'bigparents',
            'element_type'=>'float',
            'index_type'=>'integer'
        ]);
        $result->elements['p_s_map'] = makeStdClass([
            'name'=>'p_s_map',
            'type'=>'array',
            'storage_subid'=>'bigparents',
            'element_type'=>'string',
            'index_type'=>'string'
        ]);
        $result->elements['p_i_map'] = makeStdClass([
            'name'=>'p_i_map',
            'type'=>'array',
            'storage_subid'=>'bigparents',
            'element_type'=>'integer',
            'index_type'=>'string'
        ]);
        $result->elements['p_f_map'] = makeStdClass([
            'name'=>'p_f_map',
            'type'=>'array',
            'storage_subid'=>'bigparents',
            'element_type'=>'float',
            'index_type'=>'string'
        ]);
        $result->elements['p_r_array'] = makeStdClass([
            'name'=>'p_r_array',
            'type'=>'array',
            'storage_subid'=>'bigparents',
            'element_type'=>'record',
            'index_type'=>'integer'
        ]);
        
        $result->elements['_uuid'] = makeStdClass([
            'name'=>'_uuid',
            'type'=>'string',
            'max_length'=>40,
            'storage_subid'=>'objects'
        ]);
        $result->elements['_classname'] = makeStdClass([
            'name'=>'_classname',
            'type'=>'string',
            'max_length'=>40,
            'storage_subid'=>'objects'
        ]);
        $result->elements['_read_cap'] = makeStdClass([
            'name'=>'_read_cap',
            'type'=>'string',
            'max_length'=>20,
            'storage_subid'=>'objects'
        ]);
        $result->elements['_modify_cap'] = makeStdClass([
            'name'=>'_modify_cap',
            'type'=>'string',
            'max_length'=>20,
            'storage_subid'=>'objects'
        ]);
        $result->elements['_delete_cap'] = makeStdClass([
            'name'=>'_delete_cap',
            'type'=>'string',
            'max_length'=>20,
            'storage_subid'=>'objects'
        ]);
        $result->elements['_created_at'] = makeStdClass([
            'name'=>'_created_at',
            'type'=>'datetime',
            'storage_subid'=>'objects'
        ]);
        $result->elements['_updated_at'] = makeStdClass([
            'name'=>'_updated_at',
            'type'=>'datetime',
            'storage_subid'=>'objects'
        ]);
        
        $result->options = [
            'name'=>makeStdClass(['key'=>'name','translatable'=>false,'value'=>'BigParent']),
            'description'=>makeStdClass(['key'=>'description','translatable'=>true,'value'=>'A more complex object for feature tests.']),
            'storage_id'=>makeStdClass(['key'=>'storage_id','translatable'=>false,'value'=>'bigparents']),
            'taggable'=>makeStdClass(['key'=>'taggable','translatable'=>false,'value'=>true]),
            'attributable'=>makeStdClass(['key'=>'attributable','translatable'=>false,'value'=>true]),
        ];
        $result->skipping_members = [];
        
        return $result;
    }
    
}
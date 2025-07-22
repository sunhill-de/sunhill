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
        $builder->addProperty(TypeDate::class,'p_date')->defaultsNull();
        $builder->addProperty(TypeTime::class,'p_time')->nullable();
        $builder->addProperty(TypeDateTime::class,'p_datetime');
        $builder->addProperty(TypeBoolean::class,'p_bool')->default(true);
        $builder->referRecord(Dummy::class, 'p_dummy_ref');
        $builder->referRecord(BigParent::class,'p_self_ref')->setAllowedProperty(BigParent::class)->nullable();
        $builder->referRecord(Dummy::class,'p_mult_ref')->setAllowedProperty([Dummy::class,ChildObject::class]);
        $builder->array('p_s_array')->setAllowedElementType(TypeVarchar::class);
        $builder->array('p_i_array')->setAllowedElementType(TypeInteger::class);
        $builder->array('p_f_array')->setAllowedElementType(TypeFloat::class);
        $builder->array('p_s_map')->setIndexType('string')->setAllowedElementType(TypeVarchar::class);
        $builder->array('p_i_map')->setIndexType('string')->setAllowedElementType(TypeInteger::class);
        $builder->array('p_f_map')->setIndexType('string')->setAllowedElementType(TypeFloat::class);
        //$builder->arrayOfReferences('p_r_array')->setAllowedElementType(DummyChild::class);
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
}
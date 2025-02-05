<?php

/**
 * A very simple 2nd generation inherited testing object that inherited no additional field but implements one
 * @file
 */

namespace Sunhill\Tests\TestSupport\Objects;

use Sunhill\Types\TypeInteger;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Tests\Database\Seeds\ObjectsSeeder;
use Sunhill\Tests\Database\Seeds\DummiesSeeder;
use Sunhill\Tests\Database\Seeds\TagsSeeder;
use Sunhill\Tests\Database\Seeds\TagCacheSeeder;
use Sunhill\Tests\Database\Seeds\TagObjectAssignsSeeder;
use Sunhill\Tests\Database\Seeds\DummyChildrenSeeder;
use Sunhill\Tests\Database\Seeds\SkippingDummyChildrenSeeder;
use Sunhill\Tests\Database\Seeds\SkippingDummyGrandChildrenSeeder;

class SkippingDummyGrandChild extends SkippingDummyChild
{
  
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->addProperty(TypeInteger::class,'dummygrandchildint');
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'SkippingDummyGrandChild');
        static::addInfo('description', 'A simple grand child object with an integer memeber.', true);
        static::addInfo('storage_id', 'skippingdummygrandchildren');
        static::addInfo('taggable', true);
        static::addInfo('attributable', true);
    }

    public static function getExpectedStructure()
    {
        $result = new \stdClass();
        $result->name = "";
        $result->type = "record";
        $result->elements = [];
        
        $result->elements['dummyint'] = makeStdClass([
            'name'=>'dummyint',
            'type'=>'integer',
            'storage_subid'=>'dummies'            
        ]);
        $result->elements['dummygrandchildint'] = makeStdClass([
            'name'=>'dummygrandchildint',
            'type'=>'integer',
            'storage_subid'=>'skippingdummygrandchildren'
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
            'name'=>makeStdClass(['key'=>'name','translatable'=>false,'value'=>'SkippingDummyGrandChild']),
            'description'=>makeStdClass(['key'=>'description','translatable'=>true,'value'=>'A simple grand child object with an integer memeber.']),
            'storage_id'=>makeStdClass(['key'=>'storage_id','translatable'=>false,'value'=>'skippingdummygrandchildren']),
            'taggable'=>makeStdClass(['key'=>'taggable','translatable'=>false,'value'=>true]),
            'attributable'=>makeStdClass(['key'=>'attributable','translatable'=>false,'value'=>true]),
        ];
        $result->skipping_members = [SkippingDummyChild::class=>'skippingdummychildren'];
        
        return $result;
    }
  
    public static function prepareDatabase($test)
    {
        $test->seed([
            ObjectsSeeder::class,
            DummiesSeeder::class,
            SkippingDummyChildrenSeeder::class,
            SkippingDummyGrandChildrenSeeder::class,
            TagsSeeder::class,
            TagCacheSeeder::class,
            TagObjectAssignsSeeder::class
        ]);
    }
    
}
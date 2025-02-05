<?php

/**
 * A very simple testing inherited object with only one additional integer as a member
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

class DummyChild extends Dummy
{
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->addProperty(TypeInteger::class,'dummychildint');
    }
  
    protected static function setupInfos()
    {
        static::addInfo('name', 'DummyChild');
        static::addInfo('description', 'A simple child object with only one integer member.', true);
        static::addInfo('storage_id', 'dummychildren');
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
        $result->elements['dummychildint'] = makeStdClass([
            'name'=>'dummychildint',
            'type'=>'integer',
            'storage_subid'=>'dummychildren'
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
            'name'=>makeStdClass(['key'=>'name','translatable'=>false,'value'=>'DummyChild']),
            'description'=>makeStdClass(['key'=>'description','translatable'=>true,'value'=>'A simple object with only one integer member.']),
            'storage_id'=>makeStdClass(['key'=>'storage_id','translatable'=>false,'value'=>'dummychildren']),
            'taggable'=>makeStdClass(['key'=>'taggable','translatable'=>false,'value'=>true]),
            'attributable'=>makeStdClass(['key'=>'attributable','translatable'=>false,'value'=>true]),
        ];
        $result->skipping_members = [];
        
        return $result;
    }
  
    public static function prepareDatabase($test)
    {
        $test->seed([
            ObjectsSeeder::class,
            DummiesSeeder::class,
            DummyChildrenSeeder::class,
            TagsSeeder::class,
            TagCacheSeeder::class,
            TagObjectAssignsSeeder::class
        ]);
    }
    
}
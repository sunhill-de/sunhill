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

class ParentObject extends ORMObject
{
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->addProperty(TypeInteger::class,'parent_int');
        $builder->addProperty(TypeVarchar::class,'parent_string')->setMaxLen(3);
        $builder->array('parent_sarray')->setAllowedElementTypes(TypeInteger::class);
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'ParentObject');
        static::addInfo('description', 'A simple object with an int, string and array of int.', true);
        static::addInfo('storage_id', 'parentobjects');
    }

    public static function getExpectedStructure()
    {
        $result = new \stdClass();
        $result->name = "";
        $result->type = "record";
        $result->elements = [];
        
        $result->elements['parent_int'] = makeStdClass([
            'name'=>'parent_int',
            'type'=>'integer',
            'storage_subid'=>'parentobjects'
        ]);
        $result->elements['parent_string'] = makeStdClass([
            'name'=>'parent_string',
            'type'=>'string',
            'max_length'=>3,
            'storage_subid'=>'parentobjects'
        ]);
        $result->elements['parent_sarray'] = makeStdClass([
            'name'=>'parent_sarray',
            'type'=>'array',
            'storage_subid'=>'parentobjects',
            'element_type'=>TypeInteger::class,
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
            'name'=>makeStdClass(['key'=>'name','translatable'=>false,'value'=>'ParentObject']),
            'description'=>makeStdClass(['key'=>'description','translatable'=>true,'value'=>'A simple object with an int, string and array of int.']),
            'storage_id'=>makeStdClass(['key'=>'storage_id','translatable'=>false,'value'=>'parentobjects']),
            'taggable'=>makeStdClass(['key'=>'taggable','translatable'=>false,'value'=>true]),
            'attributable'=>makeStdClass(['key'=>'attributable','translatable'=>false,'value'=>true]),
        ];
        
        return $result;
    }
    
    public static function prepareDatabase($test)
    {
        $test->seed([
            ObjectsSeeder::class,
            ParentObjectsSeeder::class,
            ParentObjects_parent_sarraySeeder::class,
            TagsSeeder::class,
            TagCacheSeeder::class,
            TagObjectAssignsSeeder::class
        ]);
    }
    
}
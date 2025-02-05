<?php

namespace Sunhill\Tests\TestSupport\Objects;

use Sunhill\Types\TypeInteger;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Types\TypeVarchar;
use Sunhill\Tests\Database\Seeds\ObjectsSeeder;
use Sunhill\Tests\Database\Seeds\ParentObjectsSeeder;
use Sunhill\Tests\Database\Seeds\ParentObjects_parent_sarraySeeder;
use Sunhill\Tests\Database\Seeds\TagsSeeder;
use Sunhill\Tests\Database\Seeds\TagCacheSeeder;
use Sunhill\Tests\Database\Seeds\TagObjectAssignsSeeder;
use Sunhill\Tests\Database\Seeds\ChildObjectsSeeder;
use Sunhill\Tests\Database\Seeds\ChildObjects_child_sarraySeeder;

class ChildObject extends ParentObject
{
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->addProperty(TypeInteger::class,'child_int');
        $builder->addProperty(TypeVarchar::class,'child_string')->setMaxLen(3);
        $builder->array('child_sarray')->setAllowedElementTypes(TypeInteger::class);
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'ChildObject');
        static::addInfo('description', 'A simple derrived object with an int, string and array of int.', true);
        static::addInfo('storage_id', 'childobjects');
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
            'element_type'=>'integer',
            'index_type'=>'integer'
        ]);
        
        $result->elements['child_int'] = makeStdClass([
            'name'=>'child_int',
            'type'=>'integer',
            'storage_subid'=>'childobjects'
        ]);
        $result->elements['child_string'] = makeStdClass([
            'name'=>'child_string',
            'type'=>'string',
            'max_length'=>3,
            'storage_subid'=>'childobjects'
        ]);
        $result->elements['child_sarray'] = makeStdClass([
            'name'=>'child_sarray',
            'type'=>'array',
            'storage_subid'=>'childobjects',
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
            'name'=>makeStdClass(['key'=>'name','translatable'=>false,'value'=>'ChildObject']),
            'description'=>makeStdClass(['key'=>'description','translatable'=>true,'value'=>'A simple derrived object with an int, string and array of int.']),
            'storage_id'=>makeStdClass(['key'=>'storage_id','translatable'=>false,'value'=>'childobjects']),
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
            ParentObjectsSeeder::class,
            ParentObjects_parent_sarraySeeder::class,
            ChildObjectsSeeder::class,
            ChildObjects_child_sarraySeeder::class,           
            TagsSeeder::class,
            TagCacheSeeder::class,
            TagObjectAssignsSeeder::class
        ]);
    }
    
    
}
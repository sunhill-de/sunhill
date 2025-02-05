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
use Sunhill\Properties\ReferenceProperty;
use Sunhill\Tests\Database\Seeds\ParentReferencesSeeder;
use Sunhill\Tests\Database\Seeds\ParentReferences_parent_rarraySeeder;

class ParentReference extends ORMObject
{
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->addProperty(TypeInteger::class,'parent_int');
        $builder->referRecord(Dummy::class, 'parent_reference');
        $builder->arrayOfReferences('parent_rarray')->setAllowedElementTypes(Dummy::class);
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'ParentReference');
        static::addInfo('description', 'A simple object with an int, a reference to dummy and an array of dummies.', true);
        static::addInfo('storage_id', 'parentreferences');
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
            'storage_subid'=>'parentreferences'
        ]);
        $result->elements['parent_reference'] = makeStdClass([
            'name'=>'parent_reference',
            'type'=>'integer',
            'storage_subid'=>'parentreferences',
        ]);
        $result->elements['parent_rarray'] = makeStdClass([
            'name'=>'parent_rarray',
            'type'=>'array',
            'storage_subid'=>'parentreferences',
            'element_type'=>'integer',
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
            'name'=>makeStdClass(['key'=>'name','translatable'=>false,'value'=>'ParentReference']),
            'description'=>makeStdClass(['key'=>'description','translatable'=>true,'value'=>'A simple object with an int, a reference to dummy and an array of dummies.']),
            'storage_id'=>makeStdClass(['key'=>'storage_id','translatable'=>false,'value'=>'parentreferences']),
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
            ParentReferencesSeeder::class,
            ParentReferences_parent_rarraySeeder::class,
            TagsSeeder::class,
            TagCacheSeeder::class,
            TagObjectAssignsSeeder::class
        ]);
    }
    
}
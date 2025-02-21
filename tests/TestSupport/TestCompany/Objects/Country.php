<?php

/**
 * A testing collection class that represents a simple country
 * @file
 */

namespace Sunhill\Tests\TestSupport\Objects;

use Sunhill\Objects\Collection;
use Sunhill\Types\TypeInteger;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Tests\Database\Seeds\ObjectsSeeder;
use Sunhill\Tests\Database\Seeds\DummiesSeeder;
use Sunhill\Tests\Database\Seeds\TagsSeeder;
use Sunhill\Tests\Database\Seeds\TagCacheSeeder;
use Sunhill\Tests\Database\Seeds\TagObjectAssignsSeeder;

class Country extends Collection
{
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->addProperty(TypeVarchar::class,'name')
            ->setMaxLen(30)
            ->set_searchable()
            ->set_listable()
            ->set_visible()
            ->set_editable()
            ->set_groupeditable(false);
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'Country');
        static::addInfo('description', 'A testing class that represents a country.', true);
        static::addInfo('storage_id', 'counrties');
        static::addInfo('taggable', true);
        static::addInfo('attributable', true);
    }
    
    public static function getExpectedStructure()
    {
        $result = new \stdClass();
        $result->name = "";
        $result->type = "record";
        $result->elements = [];
        
        $result->elements['name'] = makeStdClass([
            'name'=>'name',
            'type'=>'varchar',
            'storage_subid'=>'countries',
            'searchable'=>true,
            'nullable'=>true
            'listable'=>true,
            'visible'=>true,
            'editable'=>true,
            'groupeditable'=>true]);                                                 
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
            'name'=>makeStdClass(['key'=>'name','translatable'=>false,'value'=>'Dummy']),
            'description'=>makeStdClass(['key'=>'description','translatable'=>true,'value'=>'A simple object with only one integer member.']),
            'storage_id'=>makeStdClass(['key'=>'storage_id','translatable'=>false,'value'=>'dummies']),
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
            TagsSeeder::class,
            TagCacheSeeder::class,
            TagObjectAssignsSeeder::class
        ]);        
    }
}

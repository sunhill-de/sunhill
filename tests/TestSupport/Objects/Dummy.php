<?php

namespace Sunhill\Tests\TestSupport\Objects;

use Sunhill\Objects\ORMObject;
use Sunhill\Types\TypeInteger;
use Sunhill\Properties\ElementBuilder;

class Dummy extends ORMObject
{
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->addProperty(TypeInteger::class,'dummyint');
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'Dummy');
        static::addInfo('description', 'A simple object with only one integer member.', true);
        static::addInfo('storage_id', 'dummies');
        static::addInfo('taggable', true);
        static::addInfo('attributable', true);
    }
    
    public static function getExpectedStructure()
    {
        $result = new \stdClass();
        $result->name = "";
        $result->type = "record";
        $result->elements = [];
        
        $result->elements['dummyint'] = new \stdClass();
        $result->elements['dummyint']->name = 'dummyint';
        $result->elements['dummyint']->type = 'integer';
        $result->elements['dummyint']->storage_subid = 'dummies';
        
        $result->elements['_uuid'] = new \stdClass();
        $result->elements['_uuid']->name = '_uuid';
        $result->elements['_uuid']->type = 'string';
        $result->elements['_uuid']->max_length = 40;
        $result->elements['_uuid']->storage_subid = 'objects';
        
        $result->elements['_classname'] = new \stdClass();
        $result->elements['_classname']->name = '_classname';
        $result->elements['_classname']->type = 'string';
        $result->elements['_classname']->max_length = 40;
        $result->elements['_classname']->storage_subid = 'objects';
        
        $result->elements['_read_cap'] = new \stdClass();
        $result->elements['_read_cap']->name = '_read_cap';
        $result->elements['_read_cap']->type = 'string';
        $result->elements['_read_cap']->max_length = 20;
        $result->elements['_read_cap']->storage_subid = 'objects';
        
        $result->elements['_modify_cap'] = new \stdClass();
        $result->elements['_modify_cap']->name = '_modify_cap';
        $result->elements['_modify_cap']->type = 'string';
        $result->elements['_modify_cap']->max_length = 20;
        $result->elements['_modify_cap']->storage_subid = 'objects';
        
        $result->elements['_delete_cap'] = new \stdClass();
        $result->elements['_delete_cap']->name = '_delete_cap';
        $result->elements['_delete_cap']->type = 'string';
        $result->elements['_delete_cap']->max_length = 20;
        $result->elements['_delete_cap']->storage_subid = 'objects';
        
        $result->elements['_created_at'] = new \stdClass();
        $result->elements['_created_at']->name = '_created_at';
        $result->elements['_created_at']->type = 'datetime';
        $result->elements['_created_at']->storage_subid = 'objects';
        
        $result->elements['_updated_at'] = new \stdClass();
        $result->elements['_updated_at']->name = '_updated_at';
        $result->elements['_updated_at']->type = 'datetime';
        $result->elements['_updated_at']->storage_subid = 'objects';
        
        $result->options = [
            'name'=>makeStdClass(['key'=>'name','translatable'=>false,'value'=>'Dummy']),
            'description'=>makeStdClass(['key'=>'description','translatable'=>true,'value'=>'A simple object with only one integer member.']),
            'storage_id'=>makeStdClass(['key'=>'storage_id','translatable'=>false,'value'=>'dummies']),
            'taggable'=>makeStdClass(['key'=>'taggable','translatable'=>false,'value'=>true]),
            'attributable'=>makeStdClass(['key'=>'attributable','translatable'=>false,'value'=>true]),
        ];
        
        return $result;
    }
}


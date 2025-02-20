<?php

    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->addProperty(TypeInteger::class,'parent_int')
            ->set_searchable()
            ->setDefault(5)
            ->nullable()
            ->set_listable()
            ->set_visible()
            ->set_editable()
            ->set_groupeditable();
        $builder->addProperty(TypeVarchar::class,'parent_string')
            ->setMaxLen(3)
            ->set_listable()
            ->set_visible()
            ->set_editable()
            ->set_groupeditable(false);
        $builder->addProperty(TypeFloat::class,'parent_float')
            ->set_searchable()
            ->nullable()
            ->set_listable()
            ->set_visible()
            ->set_editable()
            ->set_groupeditable();
        $builder->addProperty(TypeBool::class,'parent_bool)
            ->set_searchable()
            ->nullable()
            ->set_listable()
            ->set_visible()
            ->set_editable()
            ->set_groupeditable();
        $builder->array('parent_sarray')->setAllowedElementType(TypeInteger::class);
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'LargeParent');
        static::addInfo('description', 'A more complex class used mainly for feature tests.', true);
        static::addInfo('storage_id', 'lareparents');
        static::addInfo('taggable', true);
        static::addInfo('attributable', true);
        static::addInfo('editable', true);
        static::addInfo('addable', true);
        static::addInfo('listable', true);
        static::addInfo('deleteable', true);
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
            'storage_subid'=>'largeparents',
            'default'=>5,
            'nullable'=>true,
            'listable'=>true,
            'groupeditable'=>true,
            'editable'=>true,
            'visible'=>true,
        ]);
        $result->elements['parent_string'] = makeStdClass([
            'name'=>'parent_string',
            'type'=>'string',
            'max_length'=>3,
            'storage_subid'=>'largeparents'
            'default'=>null,
            'nullable'=>false,
            'listable'=>true,
            'groupeditable'=>false,
            'editable'=>true,
            'visible'=>true,
        ]);
        $result->elements['parent_float'] = makeStdClass([
            'name'=>'parent_float',
            'type'=>'float',
            'storage_subid'=>'largeparents'
            'default'=>new DefaultNull(),
            'nullable'=>true,
            'listable'=>true,
            'groupeditable'=>false,
            'editable'=>true,
            'visible'=>true,
        ]);
        $result->elements['parent_bool'] = makeStdClass([
            'name'=>'parent_bool',
            'type'=>'bool',
            'storage_subid'=>'largeparents'
            'default'=>new DefaultNull(),
            'nullable'=>true,
            'listable'=>true,
            'groupeditable'=>false,
            'editable'=>true,
            'visible'=>true,
        ]);
        $result->elements['parent_sarray'] = makeStdClass([
            'name'=>'parent_sarray',
            'type'=>'array',
            'storage_subid'=>'parentobjects',
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
            'name'=>makeStdClass(['key'=>'name','translatable'=>false,'value'=>'ParentObject']),
            'description'=>makeStdClass(['key'=>'description','translatable'=>true,'value'=>'A simple object with an int, string and array of int.']),
            'storage_id'=>makeStdClass(['key'=>'storage_id','translatable'=>false,'value'=>'parentobjects']),
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
            TagsSeeder::class,
            TagCacheSeeder::class,
            TagObjectAssignsSeeder::class
        ]);
    }
    
      }

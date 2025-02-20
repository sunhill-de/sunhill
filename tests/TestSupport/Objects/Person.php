<?php

    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->addProperty(TypeVarchar::class,'first_name')
            ->setMaxLen(30)
            ->nullable()
            ->set_listable()
            ->set_visible()
            ->set_editable()
            ->set_groupeditable(true);
        $builder->addProperty(TypeVarchar::class,'last_name')
            ->setMaxLen(30)
            ->set_listable()
            ->set_visible()
            ->set_editable()
            ->set_searchable()
            ->set_groupeditable(false);
        $builder->addProperty(TypeEnum::class,'sex')
            ->setEnumValues(['male','female','divers'])
            ->nullable()
            ->set_searchable()
            ->set_listable()
            ->set_visible()
            ->set_editable()
            ->set_groupeditable();
        $builder->addProperty(TypeDate::class,'date_of_birth')
            ->set_searchable()
            ->nullable()
            ->set_listable()
            ->set_visible()
            ->set_editable()
            ->set_groupeditable();
        $builder->referRecord(Country::class,'country_of_birth')
            ->set_searchable()
            ->set_listable()
            ->set_visible()
            ->set_keyfield('name')
            ->set_editable()
            ->set_groupeditable();
        $builder->addProperty(TypeBoolean::class,'confirmed')
            ->set_searchable()
            ->setDefault(false)
            ->set_listable(false)
            ->set_visible()
            ->set_editable()
            ->set_groupeditable();
        $builder->addProperty(TypeFloat::class,'height')     
            ->nullable()
            ->set_listable(false)
            ->set_visible()
            ->set_editable()
            ->set_groupeditable();
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'Person');
        static::addInfo('description', 'A more readable test object for feature tests.', true);
        static::addInfo('storage_id', 'persons');
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

        $result->elements['first_name'] = makeStdClass([
            'name'=>'first_name',
            'max_lenght'=>30,                                          
            'type'=>'varchar',
            'storage_subid'=>'persons',
            'searchable'=>true,
            'nullable'=>true
            'listable'=>true,
            'visible'=>true,
            'editable'=>true,
            'groupeditable'=>true]);
        $result->elements['last_name'] = makeStdClass([
            'name'=>'last_name',
            'max_lenght'=>30,                                          
            'type'=>'varchar',
            'storage_subid'=>'persons',
            'searchable'=>true,
            'nullable'=>false
            'listable'=>true,
            'visible'=>true,
            'editable'=>true,
            'groupeditable'=>false]);
        $result->elements['sex'] = makeStdClass([
            'name'=>'sex',
            'type'=>'enum',
            'storage_subid'=>'persons',
            'searchable'=>true,
            'nullable'=>true
            'listable'=>true,
            'visible'=>true,
            'editable'=>true,
            'groupeditable'=>true]);
        $result->elements['date_of_birth'] = makeStdClass([
            'name'=>'date_of_birth',
            'type'=>'date',
            'storage_subid'=>'persons',
            'searchable'=>true,
            'nullable'=>true
            'listable'=>true,
            'visible'=>true,
            'editable'=>true,
            'groupeditable'=>true]);
        $result->elements['country_of_birth'] = makeStdClass([
            'name'=>'country_of_birth',
            'type'=>'reference',
            'storage_subid'=>'persons',
            'searchable'=>true,
            'nullable'=>false
            'listable'=>true,
            'visible'=>true,
            'editable'=>true,
            'groupeditable'=>true
            'keyfield'=>'name']);
        $result->elements['confirmed'] = makeStdClass([
            'name'=>'confirmed',
            'type'=>'bool',
            'storage_subid'=>'persons',
            'searchable'=>true,
            'nullable'=>false
            'listable'=>false,
            'visible'=>true,
            'editable'=>true,
            'groupeditable'=>true]);
        $result->elements['height'] = makeStdClass([
            'name'=>'height',
            'type'=>'float',
            'storage_subid'=>'persons',
            'nullable'=>true,
            'listable'=>false,
            'visible'=>true,
            'editable'=>true,
            'groupeditable'=>true]);
        
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
            'editable'=>makeStdClass(['key'=>'editable','translatable'=>false,'value'=>true]),
            'addable'=>makeStdClass(['key'=>'addable','translatable'=>false,'value'=>true]),
            'listable'=>makeStdClass(['key'=>'listable','translatable'=>false,'value'=>true]),
            'deletable'=>makeStdClass(['key'=>'deleteable','translatable'=>false,'value'=>true]),
        ];
        $result->skipping_members = [];
        
        return $result;
    }
    
    public static function prepareDatabase($test)
    {
        $test->seed([
            ObjectsSeeder::class,
            PersonsSeeder::class,
            TagsSeeder::class,
            TagCacheSeeder::class,
            TagObjectAssignsSeeder::class
        ]);
    }
    
      }

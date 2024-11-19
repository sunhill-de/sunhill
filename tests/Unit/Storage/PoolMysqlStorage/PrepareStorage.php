<?php

use Sunhill\Tests\Database\Seeds\ObjectsSeeder;
use Sunhill\Tests\Database\Seeds\DummiesSeeder;
use Sunhill\Tests\Database\Seeds\TagsSeeder;
use Sunhill\Tests\Database\Seeds\TagCacheSeeder;
use Sunhill\Tests\Database\Seeds\TagObjectAssignsSeeder;
use Sunhill\Tests\Database\Seeds\ParentObjectsSeeder;
use Sunhill\Tests\Database\Seeds\ChildObjectsSeeder;
use Sunhill\Tests\Database\Seeds\ParentObjects_parent_sarraySeeder;
use Sunhill\Tests\Database\Seeds\ChildObjects_child_sarraySeeder;
use Sunhill\Storage\PoolMysqlStorage\PoolMysqlStorage;

function prepareStorage($test, string $type)
{
    switch ($type) {
        case 'dummy':
            $test->seed([ObjectsSeeder::class,DummiesSeeder::class,TagsSeeder::class,TagCacheSeeder::class,TagObjectAssignsSeeder::class]);
            return [
                '_uuid'=>makeStdclass(['name'=>'_uuid','type'=>'string','max_length'=>40,'storage_subid'=>'objects']),
                '_read_cap'=>makeStdclass(['name'=>'_read_cap','type'=>'string','max_length'=>20,'storage_subid'=>'objects']),
                '_modify_cap'=>makeStdclass(['name'=>'_modify_cap','type'=>'string','max_length'=>20,'storage_subid'=>'objects']),
                '_delete_cap'=>makeStdclass(['name'=>'_delete_cap','type'=>'string','max_length'=>20,'storage_subid'=>'objects']),
                '_created_at'=>makeStdclass(['name'=>'_created_at','type'=>'datetime','storage_subid'=>'objects']),
                '_modified_at'=>makeStdclass(['name'=>'_modified_at','type'=>'datetime','storage_subid'=>'objects']),
                'dummyint'=>makeStdclass(['name'=>'dummyint','type'=>'integer','storage_subid'=>'dummies']),
            ];
        case 'parentobject':
            $test->seed([
            ObjectsSeeder::class,
            ParentObjectsSeeder::class,
            ParentObjects_parent_sarraySeeder::class
            ]);
            return [
                '_uuid'=>makeStdclass(['name'=>'_uuid','type'=>'string','max_length'=>40,'storage_subid'=>'objects']),
                '_read_cap'=>makeStdclass(['name'=>'_read_cap','type'=>'string','max_length'=>20,'storage_subid'=>'objects']),
                '_modify_cap'=>makeStdclass(['name'=>'_modify_cap','type'=>'string','max_length'=>20,'storage_subid'=>'objects']),
                '_delete_cap'=>makeStdclass(['name'=>'_delete_cap','type'=>'string','max_length'=>20,'storage_subid'=>'objects']),
                '_created_at'=>makeStdclass(['name'=>'_created_at','type'=>'datetime','storage_subid'=>'objects']),
                '_modified_at'=>makeStdclass(['name'=>'_modified_at','type'=>'datetime','storage_subid'=>'objects']),
                'parent_int'=>makeStdclass(['name'=>'parent_int','type'=>'integer','storage_subid'=>'parentobjects']),
                'parent_string'=>makeStdclass(['name'=>'parent_string','type'=>'string','max_length'=>3,'storage_subid'=>'parentobjects']),
                'parent_sarray'=>makeStdClass(['name'=>'parent_sarray','type'=>'array','element_type'=>'Integer','storage_subid'=>'parentobjects']),
            ];
        case 'childobject':
            $test->seed([
            ObjectsSeeder::class,
            ParentObjectsSeeder::class,
            ParentObjects_parent_sarraySeeder::class,
            ChildObjectsSeeder::class,
            ChildObjects_child_sarraySeeder::class
            ]);
            return [
                '_uuid'=>makeStdclass(['name'=>'_uuid','type'=>'string','max_length'=>40,'storage_subid'=>'objects']),
                '_read_cap'=>makeStdclass(['name'=>'_read_cap','type'=>'string','max_length'=>20,'storage_subid'=>'objects']),
                '_modify_cap'=>makeStdclass(['name'=>'_modify_cap','type'=>'string','max_length'=>20,'storage_subid'=>'objects']),
                '_delete_cap'=>makeStdclass(['name'=>'_delete_cap','type'=>'string','max_length'=>20,'storage_subid'=>'objects']),
                '_created_at'=>makeStdclass(['name'=>'_created_at','type'=>'datetime','storage_subid'=>'objects']),
                '_modified_at'=>makeStdclass(['name'=>'_modified_at','type'=>'datetime','storage_subid'=>'objects']),
                'parent_int'=>makeStdclass(['name'=>'parent_int','type'=>'integer','storage_subid'=>'parentobjects']),
                'parent_string'=>makeStdclass(['name'=>'parent_string','type'=>'string','max_length'=>3,'storage_subid'=>'parentobjects']),
                'parent_sarray'=>makeStdClass(['name'=>'parent_sarray','type'=>'array','element_type'=>'Integer','storage_subid'=>'parentobjects']),
                'child_int'=>makeStdclass(['name'=>'child_int','type'=>'integer','storage_subid'=>'childobjects']),
                'child_string'=>makeStdclass(['name'=>'child_string','type'=>'string','max_length'=>3,'storage_subid'=>'childobjects']),
                'child_sarray'=>makeStdClass(['name'=>'child_sarray','type'=>'array','element_type'=>'Integer','storage_subid'=>'childobjects']),
            ];
    }
}

function fillObjectsDataset(PoolMysqlStorage $test, string $classname = 'Dummy')
{
    $test->setValue('_classname',$classname);
    $test->setValue('_uuid','11b47be8-05f1-4f7b-8a97-e1e6488dbd44');
    $test->setValue('_read_cap', null);
    $test->setValue('_write_cap', null);
    $test->setValue('_modify_cap', null);
    $test->setValue('_delete_cap', null);
    $test->setValue('_created_at', '2024-11-14 20:00:00');
    $test->setValue('_updated_at', '2024-11-14 20:00:00');
    $test->setValue('_tags',[]);
    $test->setValue('_attributes',[]);
}



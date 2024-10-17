<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\Database\Seeds\ObjectsSeeder;
use Sunhill\Tests\Database\Seeds\DummiesSeeder;
use Sunhill\Storage\PoolMysqlStorage\PoolMysqlStorage;
use Sunhill\Tests\Database\Seeds\TagsSeeder;
use Sunhill\Tests\Database\Seeds\TagCacheSeeder;
use Sunhill\Tests\Database\Seeds\TagObjectAssignsSeeder;
use Sunhill\Tests\Database\Seeds\ParentObjectsSeeder;
use Sunhill\Tests\Database\Seeds\ChildObjectsSeeder;
use Sunhill\Tests\Database\Seeds\ParentObjects_parent_sarraySeeder;
use Sunhill\Tests\Database\Seeds\ChildObjects_child_sarraySeeder;
use Sunhill\Storage\Exceptions\StorageTableMissingException;
use Illuminate\Support\Facades\Schema;

uses(SunhillDatabaseTestCase::class);

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

test('Read a dummy', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'dummy'));
    $test->load(1);
    
    expect($test->getValue('dummyint'))->toBe(123);
    expect($test->getIndexedValue('tags',0)->getID())->toBe(1);
});

test('Read a parentobject with array', function()
{
    
    $structure = 
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'parentobject'));
    $test->load(7);
    
    expect($test->getValue('parent_int'))->toBe(111);
    expect($test->getValue('parent_string'))->toBe('AAA');
    expect($test->getIndexedValue('parent_sarray',1))->toBe(11);
    
});

test('Read a parentobject with empty array', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'parentobject'));
    $test->load(8);
    
    expect($test->getValue('parent_int'))->toBe(222);
    expect($test->getValue('parent_string'))->toBe('BBB');
    expect($test->getElementCount('parent_sarray'))->toBe(0);
    
});

test('Read a childobject with both arrays', function()
{    
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    $test->load(9);
    
    expect($test->getValue('parent_int'))->toBe(333);
    expect($test->getValue('parent_string'))->toBe('CCC');
    expect($test->getIndexedValue('parent_sarray',1))->toBe(31);
    
    expect($test->getValue('child_int'))->toBe(212);
    expect($test->getValue('child_string'))->toBe('BCD');
    expect($test->getIndexedValue('child_sarray',1))->toBe(210);
});

test('Read a childobject with parent array', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    $test->load(10);
    
    expect($test->getValue('parent_int'))->toBe(444);
    expect($test->getValue('parent_string'))->toBe('DDD');
    expect($test->getIndexedValue('parent_sarray',1))->toBe(41);
    
    expect($test->getValue('child_int'))->toBe(222);
    expect($test->getValue('child_string'))->toBe('CDE');
    expect($test->getElementCount('child_sarray'))->toBe(0);
});

test('Read a childobject with child array', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    $test->load(11);
    
    expect($test->getValue('parent_int'))->toBe(555);
    expect($test->getValue('parent_string'))->toBe('EEE');
    expect($test->getElementCount('parent_sarray'))->toBe(0);
    
    expect($test->getValue('child_int'))->toBe(232);
    expect($test->getValue('child_string'))->toBe('DEF');
    expect($test->getIndexedValue('child_sarray',1))->toBe(410);
});

test('Read a childobject with both arrays empty', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    $test->load(12);
    
    expect($test->getValue('parent_int'))->toBe(666);
    expect($test->getValue('parent_string'))->toBe('FFF');
    expect($test->getElementCount('parent_sarray'))->toBe(0);
    
    expect($test->getValue('child_int'))->toBe(242);
    expect($test->getValue('child_string'))->toBe('EFG');
    expect($test->getElementCount('child_sarray'))->toBe(0);
});

it('fails when a table is missing', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    Schema::drop('parentobjects_parent_sarray');
    $test->load(12);
})->throws(StorageTableMissingException::class);

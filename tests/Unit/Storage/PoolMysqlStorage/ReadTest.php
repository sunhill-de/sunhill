<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\Database\Seeds\ObjectsSeeder;
use Sunhill\Tests\Database\Seeds\DummiesSeeder;
use Sunhill\Storage\PoolMysqlStorage\PoolMysqlStorage;

uses(SunhillDatabaseTestCase::class);

test('Read a dummy', function()
{
    $this->seed([ObjectsSeeder::class,DummiesSeeder::class]);
    
    $structure = [
        '_uuid'=>makeStdclass(['name'=>'_uuid','type'=>'string','max_length'=>40,'storage_subid'=>'objects']),
        '_read_cap'=>makeStdclass(['name'=>'_read_cap','type'=>'string','max_length'=>20,'storage_subid'=>'objects']),
        '_modify_cap'=>makeStdclass(['name'=>'_modify_cap','type'=>'string','max_length'=>20,'storage_subid'=>'objects']),
        '_delete_cap'=>makeStdclass(['name'=>'_delete_cap','type'=>'string','max_length'=>20,'storage_subid'=>'objects']),
        '_created_at'=>makeStdclass(['name'=>'_created_at','type'=>'datetime','storage_subid'=>'objects']),
        '_modified_at'=>makeStdclass(['name'=>'_modified_at','type'=>'datetime','storage_subid'=>'objects']),
        'dummyint'=>makeStdclass(['name'=>'dummyint','type'=>'integer','storage_subid'=>'dummies']),
    ];
    $test = new PoolMysqlStorage();
    $test->setStructure($structure);
    $test->load(1);
    
    expect($test->getValue('dummyint'))->toBe(123);
});
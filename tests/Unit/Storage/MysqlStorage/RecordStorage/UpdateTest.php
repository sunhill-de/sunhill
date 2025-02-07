<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\Exceptions\StorageTableMissingException;
use Illuminate\Support\Facades\Schema;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ChildObject;

require_once('PrepareStorage.php');

uses(SunhillDatabaseTestCase::class);

it('update fails when a table is missing', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    Schema::drop('parentobjects_parent_sarray');
    
    $test->setValue('parent_int',1509);
    $test->setValue('parent_string','ACDC');
    $test->setValue('parent_sarray',[12,34,56]);
    $test->setValue('child_int',5678);
    $test->setValue('child_string','ACAC');
    $test->setValue('child_sarray',[78,90,12]);
    
    $test->setValue('_classname','Dummy');
    $test->setValue('_uuid','11b47be8-05f1-4f7b-8a97-e1e6488dbd44');
    $test->setValue('_read_cap', null);
    $test->setValue('_write_cap', null);
    $test->setValue('_modify_cap', null);
    $test->setValue('_delete_cap', null);
    $test->setValue('_created_at', '2024-11-14 20:00:00');
    $test->setValue('_updated_at', '2024-11-14 20:00:00');
    $test->setValue('_tags',[]);
    $test->setValue('_attributes',[]);
    
    $test->commit();
})->throws(StorageTableMissingException::class);


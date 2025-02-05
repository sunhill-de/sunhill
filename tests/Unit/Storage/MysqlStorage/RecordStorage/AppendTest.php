<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\Exceptions\StorageTableMissingException;
use Illuminate\Support\Facades\Schema;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\Dummy;

require_once('PrepareStorage.php');

uses(SunhillDatabaseTestCase::class);

it('fails when a table is missing', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    Schema::drop('parentobjects_parent_sarray');
    
    $test->setValue('parent_int',1509);
    $test->setValue('parent_string','ACDC');
    $test->setValue('parent_sarray',[12,34,56]);
    $test->setValue('child_int',5678);
    $test->setValue('child_string','ACAC');
    $test->setValue('child_sarray',[78,90,12]);
    
    fillObjectsDataset($test);
    
    $test->commit();
})->throws(StorageTableMissingException::class);


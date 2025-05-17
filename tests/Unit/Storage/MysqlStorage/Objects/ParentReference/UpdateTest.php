<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\ParentReference;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Nothing to do (reference and array filled)', function()
{
    $test = prepareStorage(ParentReference::class, $this);
    pretendLoaded($test, 17, ParentReference::class);
    prepareObjectDataset($test, 'ParentReference');
    
    $test->commit();
    $this->assertDatabaseHas('parentreferences',['id'=>17,'parent_int'=>1111,'parent_reference'=>1]);
    $this->assertDatabaseHas('parentreferences_parent_rarray',['container_id'=>17,'index'=>0,'element'=>2]);
    $this->assertDatabaseHas('parentreferences_parent_rarray',['container_id'=>17,'index'=>1,'element'=>3]);
    $this->assertDatabaseHas('parentreferences_parent_rarray',['container_id'=>17,'index'=>2,'element'=>4]);
    
})->group('update');

test('Nothing to do (only array filled)', function()
{
    $test = prepareStorage(ParentReference::class, $this);
    pretendLoaded($test, 18, ParentReference::class);
    prepareObjectDataset($test, 'ParentReference');
    
    $test->commit();
    $this->assertDatabaseHas('parentreferences',['id'=>18,'parent_int'=>2222,'parent_reference'=>null]);
    $this->assertDatabaseHas('parentreferences_parent_rarray',['container_id'=>18,'index'=>0,'element'=>3]);    
})->group('update');

test('Nothing to do (both not filled)', function()
{
    $test = prepareStorage(ParentReference::class, $this);
    pretendLoaded($test, 19, ParentReference::class);
    prepareObjectDataset($test, 'ParentReference');
    
    $test->commit();
    $this->assertDatabaseHas('parentreferences',['id'=>19,'parent_int'=>3333,'parent_reference'=>null]);
    $this->assertDatabaseMissing('parentreferences_parent_rarray',['container_id'=>19]);
})->group('update');

test('Removed reference (reference and array filled)', function()
{
    $test = prepareStorage(ParentReference::class, $this);
    pretendLoaded($test, 17, ParentReference::class);
    prepareObjectDataset($test, 'ParentReference');
    $test->setValue('parent_reference',null);
    
    $test->commit();
    $this->assertDatabaseHas('parentreferences',['id'=>17,'parent_int'=>1111,'parent_reference'=>null]);
    $this->assertDatabaseHas('parentreferences_parent_rarray',['container_id'=>17,'index'=>0,'element'=>2]);
    $this->assertDatabaseHas('parentreferences_parent_rarray',['container_id'=>17,'index'=>1,'element'=>3]);
    $this->assertDatabaseHas('parentreferences_parent_rarray',['container_id'=>17,'index'=>2,'element'=>4]);
    
})->group('update');

test('Cleared array (reference and array filled)', function()
{
    $test = prepareStorage(ParentReference::class, $this);
    pretendLoaded($test, 17, ParentReference::class);
    prepareObjectDataset($test, 'ParentReference');
    $test->setValue('parent_rarray',[]);
    
    $test->commit();
    $this->assertDatabaseHas('parentreferences',['id'=>17,'parent_int'=>1111,'parent_reference'=>1]);
    $this->assertDatabaseMissing('parentreferences_parent_rarray',['container_id'=>17]);    
})->group('update');

test('Fill reference (only array filled)', function()
{
    $test = prepareStorage(ParentReference::class, $this);
    pretendLoaded($test, 18, ParentReference::class);
    prepareObjectDataset($test, 'ParentReference');
    $test->setValue('parent_reference',1);
    $test->commit();
    $this->assertDatabaseHas('parentreferences',['id'=>18,'parent_int'=>2222,'parent_reference'=>1]);
    $this->assertDatabaseHas('parentreferences_parent_rarray',['container_id'=>18,'index'=>0,'element'=>3]);
})->group('update');


<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\ParentReference;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Append a parentreference with reference and array', function()
{    
    $test = prepareStorage(ParentReference::class, $this);
    prepareObjectDataset($test, ParentReference::class);
    
    $test->setValue('parent_int',1213);
    $test->setValue('parent_reference',1);
    $test->setValue('parent_rarray',[1,2,3]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentreferences',['id'=>$test->getID(),'parent_int'=>1213,'parent_reference'=>1]);
    $this->assertDatabaseHas('parentreferences_parent_rarray',['container_id'=>$test->getID(),'element'=>3]);
})->group('append');

test('Append a parentreference only with array', function()
{
    $test = prepareStorage(ParentReference::class, $this);
    prepareObjectDataset($test, ParentReference::class);
    
    $test->setValue('parent_int',1213);
    $test->setValue('parent_reference',null);
    $test->setValue('parent_rarray',[1,2,3]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentreferences',['id'=>$test->getID(),'parent_int'=>1213,'parent_reference'=>null]);
    $this->assertDatabaseHas('parentreferences_parent_rarray',['container_id'=>$test->getID(),'element'=>3]);
})->group('append');

test('Append a parentreference with no references', function()
{
    $test = prepareStorage(ParentReference::class, $this);
    prepareObjectDataset($test, ParentReference::class);
    
    $test->setValue('parent_int',1213);
    $test->setValue('parent_reference',null);
    $test->setValue('parent_rarray',[]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentreferences',['id'=>$test->getID(),'parent_int'=>1213,'parent_reference'=>null]);
    $this->assertDatabaseMissing('parentreferences_parent_rarray',['container_id'=>$test->getID()]);
})->group('append');

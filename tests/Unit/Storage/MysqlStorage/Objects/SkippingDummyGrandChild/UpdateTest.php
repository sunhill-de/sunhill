<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Update (nothing to do)', function()
{
    $test = prepareStorage(SkippingDummyGrandChild::class, $this);
    pretendLoaded($test, 16, SkippingDummyGrandChild::class);
    
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>16,'dummyint'=>976]);
    $this->assertDatabaseHas('skippingdummygrandchildren',['id'=>16,'dummygrandchildint'=>9111]);
})->group('update');

test('Update dummyint (change dummyint)', function()
{
    $test = prepareStorage(SkippingDummyGrandChild::class, $this);
    pretendLoaded($test, 16, SkippingDummyGrandChild::class);
    $test->setValue('dummyint', 999);
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>16,'dummyint'=>999]);
    $this->assertDatabaseHas('skippingdummygrandchildren',['id'=>16,'dummygrandchildint'=>9111]);
})->group('update');

test('Update dummyint (change dummygrandchildint)', function()
{
    $test = prepareStorage(SkippingDummyGrandChild::class, $this);
    pretendLoaded($test, 16, SkippingDummyGrandChild::class);
    $test->setValue('dummygrandchildint', 999);
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>16,'dummyint'=>976]);
    $this->assertDatabaseHas('skippingdummygrandchildren',['id'=>16,'dummygrandchildint'=>999]);
})->group('update');
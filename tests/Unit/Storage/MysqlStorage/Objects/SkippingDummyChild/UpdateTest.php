<?php
use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Update dummyint (nothing to do)', function()
{
    $test = prepareStorage(SkippingDummyChild::class, $this);
    pretendLoaded($test, 14, SkippingDummyChild::class);
    
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>14,'dummyint'=>987]);
})->group('update');

test('Update dummyint (change dummyint)', function()
{
    $test = prepareStorage(SkippingDummyChild::class, $this);
    pretendLoaded($test, 14, SkippingDummyChild::class);
    $test->setValue('dummyint', 999);
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>14,'dummyint'=>999]);
})->group('update');
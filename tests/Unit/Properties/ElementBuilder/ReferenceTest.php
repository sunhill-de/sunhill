<?php
/**
 * @file ReferenceTest.php
 * tests: /src/Properties/ElementBuilder.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Properties\ReferenceProperty;
use Sunhill\Tests\TestSupport\Properties\DummyRecordProperty;

uses(SunhillSimpleTestCase::class);

test('Reference works ', function()
{
    $test = new ElementBuilder();
    expect(is_a($test->referRecord(DummyRecordProperty::class, 'test_reference'),ReferenceProperty::class))->toBe(true);
    expect(is_a($test->getElements()['test_reference'],ReferenceProperty::class))->toBe(true);
});
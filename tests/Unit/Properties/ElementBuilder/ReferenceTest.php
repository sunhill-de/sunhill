<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Properties\ReferenceProperty;
use Sunhill\Tests\TestSupport\Properties\DummyRecordProperty;

uses(SimpleTestCase::class);

test('Reference works ', function()
{
    $test = new ElementBuilder();
    expect(is_a($test->referRecord(DummyRecordProperty::class, 'test_reference'),ReferenceProperty::class))->toBe(true);
    expect(is_a($test->getElements()['test_reference'],ReferenceProperty::class))->toBe(true);
});
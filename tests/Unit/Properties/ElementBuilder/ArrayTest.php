<?php

/**
 * @file ArrayTest.php
 * tests: /src/Properties/ElementBuilder.php
 * free of dependent units: yes
 */

use Sunhill\Properties\ArrayProperty;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Properties\ReferenceArrayProperty;
use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);

test('Simple array', function () {
    $test = new ElementBuilder;
    expect(is_a($test->array('test_array'), ArrayProperty::class))->toBe(true);
    expect(is_a($test->getElements()['test_array'], ArrayProperty::class))->toBe(true);
});

test('Array of references', function () {
    $test = new ElementBuilder;
    expect(is_a($test->arrayOfReferences('test_array'), ReferenceArrayProperty::class))->toBe(true);
    expect(is_a($test->getElements()['test_array'], ReferenceArrayProperty::class))->toBe(true);
});

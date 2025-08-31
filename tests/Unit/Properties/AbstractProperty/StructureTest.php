<?php

/**
 * @file StructureTest.php
 * tests: /src/Properties/AbstractProperty.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillLaravelTestCase;
use Sunhill\Tests\TestSupport\Properties\NonAbstractProperty;

uses(SunhillLaravelTestCase::class);

test('Get standard structure', function () {
    $test = new NonAbstractProperty;
    $expect = makeStdclass(['name' => 'test_int', 'type' => 'integer']);
    expect($test->getStructure())->toEqual($expect);
});

test('Get structure with default', function () {
    $test = new NonAbstractProperty;
    $test->setDefault(123);
    $expect = makeStdclass(['name' => 'test_int', 'type' => 'integer', 'default' => 123]);
    expect($test->getStructure())->toEqual($expect);
});

test('Get structure with nullable', function () {
    $test = new NonAbstractProperty;
    $test->nullable();
    $expect = makeStdclass(['name' => 'test_int', 'type' => 'integer', 'nullable' => true, 'default' => null]);
    expect($test->getStructure())->toEqual($expect);
});

test('Get structure with nullable and default', function () {
    $test = new NonAbstractProperty;
    $test->nullable()->default(123);
    $expect = makeStdclass(['name' => 'test_int', 'type' => 'integer', 'nullable' => true, 'default' => 123]);
    expect($test->getStructure())->toEqual($expect);
});

test('Get structure with default and nullable', function () {
    $test = new NonAbstractProperty;
    $test->default(123)->nullable();
    $expect = makeStdclass(['name' => 'test_int', 'type' => 'integer', 'nullable' => true, 'default' => 123]);
    expect($test->getStructure())->toEqual($expect);
});

test('Get structure with default null', function () {
    $test = new NonAbstractProperty;
    $test->default(null);
    $expect = makeStdclass(['name' => 'test_int', 'type' => 'integer', 'nullable' => true, 'default' => null]);
    expect($test->getStructure())->toEqual($expect);
});

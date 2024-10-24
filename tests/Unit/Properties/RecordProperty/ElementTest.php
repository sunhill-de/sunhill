<?php

use Sunhill\Properties\RecordProperty;
use Sunhill\Tests\SunhillTestCase;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Properties\NonAbstractProperty;

uses(SunhillTestCase::class);

test('appendElement() with only an element object', function()
{
    $test = new RecordProperty();
    $element = new NonAbstractProperty();
    expect($test->appendElement($element))->toBe($element);
});

test('appendElement() with only a fully qualified class name', function()
{
    $test = new RecordProperty();
    expect(is_a($test->appendElement(NonAbstractProperty::class),NonAbstractProperty::class))->toBe(true);
});

test('appendElement() with only a property name', function()
{
    Properties::shouldReceive('getNamespaceOfProperty')->once()->with('test')->andReturn(NonAbstractProperty::class);
    $test = new RecordProperty();
    expect(is_a($test->appendElement('test'),NonAbstractProperty::class))->toBe(true);
});


<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Tests\TestSupport\Properties\NonAbstractProperty;
use Sunhill\Tests\TestSupport\Properties\DummyRecordProperty;
use Sunhill\Properties\Exceptions\InvalidInclusionException;

uses(SimpleTestCase::class);

test('inclusion includes elements', function()
{
    $test = new ElementBuilder();
    $element1 = new NonAbstractProperty();
    $element2 = new NonAbstractProperty();
    
    $record = new DummyRecordProperty();
    $record->appendElement($element2,'included_property');
    
    $test->addProperty($element1, 'own_property');
    $test->includeRecord($record);
    
    $elements = $test->getElements();
    expect(array_key_exists('own_property',$elements))->toBe(true);
    expect(array_key_exists('included_property',$elements))->toBe(true);
});

test('inclusion fills inclusion record', function()
{
    $test = new ElementBuilder();
    $element1 = new NonAbstractProperty();
    $element2 = new NonAbstractProperty();
    
    $record = new DummyRecordProperty();
    $record->appendElement($element2,'included_property');
    
    $test->addProperty($element1, 'own_property');
    $test->includeRecord($record);
    
    $elements = $test->getIncludes();
    expect(in_array(DummyRecordProperty::class, $elements))->toBe(true);
});

it('fails when including not a record property', function() 
{
    $test = new ElementBuilder();
    $test->includeRecord(NonAbstractProperty::class);    
})->throws(InvalidInclusionException::class);
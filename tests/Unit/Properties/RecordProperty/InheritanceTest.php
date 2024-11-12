<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Properties\ChildRecordProperty;

uses(SimpleTestCase::class);

test('inheriteted embedded properties', function()
{
    ChildRecordProperty::setInclusion('embed');
    
    $test = new ChildRecordProperty();
    expect($test->hasElement('child_int'))->toBe(true);
    expect($test->hasElement('parent_int'))->toBe(true);
});

test('inhertited embedded structure', function()
{
    ChildRecordProperty::setInclusion('embed');
    
    $test = new ChildRecordProperty();
    $structure = $test->getStructure();
    expect(is_array($structure->elements))->toBe(true);
    expect($structure->elements['parent_int']->name)->toBe('parent_int');
    expect($structure->elements['parent_int']->type)->toBe('integer');
    expect($structure->elements['parent_int']->storage_id)->toBe('parent');
    expect($structure->elements['child_int']->name)->toBe('child_int');
    expect($structure->elements['child_int']->type)->toBe('integer');
    expect($structure->elements['child_int']->storage_id)->toBe('child');
});

test('inherited included properties', function()
{
    ChildRecordProperty::setInclusion('include');    

    $test = new ChildRecordProperty();
    expect($test->hasElement('child_int'))->toBe(true);
    expect($test->hasElement('parent_int'))->toBe(true);    
});

test('inhertited included structure', function()
{
    ChildRecordProperty::setInclusion('embed');
    
    $test = new ChildRecordProperty();
    $structure = $test->getStructure();
    expect(is_array($structure->elements))->toBe(true);
    expect($structure->elements['parent_int']->name)->toBe('parent_int');
    expect($structure->elements['parent_int']->type)->toBe('integer');
    expect($structure->elements['parent_int']->storage_id)->toBe('child');
    expect($structure->elements['child_int']->name)->toBe('child_int');
    expect($structure->elements['child_int']->type)->toBe('integer');
    expect($structure->elements['child_int']->storage_id)->toBe('child');
});


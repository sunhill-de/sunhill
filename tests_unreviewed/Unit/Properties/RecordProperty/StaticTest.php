<?php

use Sunhill\Properties\RecordProperty;
use Sunhill\Storage\StaticStorage;
use Sunhill\Types\TypeVarchar;
use Sunhill\Types\TypeInteger;

uses(\Sunhill\Tests\TestCase::class);

test('static() creates a static storage', function () {
    $test = new RecordProperty();
    $test->setName('test');
    $test->static();

    expect(is_a($test->getStorage(), StaticStorage::class))->toBeTrue();
});

test('A static record can read and write a property', function() {
   $test = new RecordProperty();
   $test->setName('test');
   $test->static();
   
   $test->appendElement('string_element', new TypeVarchar());
   $test->appendElement('int_element', new TypeInteger());
   
   $test->string_element = 'ABC';
   $test->int_element = 123;
   
   expect($test->string_element)->toBe('ABC');
   expect($test->int_element)->toBe(123);
});
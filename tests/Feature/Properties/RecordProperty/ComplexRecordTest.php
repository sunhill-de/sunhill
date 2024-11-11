<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tests\Feature\Properties\RecordProperty\Examples\ComplexRecordProperty;

uses(SunhillTestCase::class);

test('usage of a ComplexRecord with included', function()
{
   $test = new ComplexRecordProperty();
   
   $test->complex_int = 10;
   $test->complex_str = 'ABC';
   $test->test_int = 20;
   $test->test_str = 'DEF';
   $test->reference_record->test_int = 30;
   $test->complex_array_of_int[] = 10;
   $test->complex_array_of_int[] = 20;
   
   expect($test->complex_int)->toBe(10);
   expect($test->complex_str)->toBe('ABC');
   expect($test->test_int)->toBe(20);
   expect($test->test_str)->toBe('DEF');
   expect($test->reference_record->test_int)->toBe(30);
   expect($test->complex_array_of_int[1])->toBe(20);
});
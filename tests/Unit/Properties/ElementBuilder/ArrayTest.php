<?php

use Sunhill\Tests\TestCase;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Properties\ArrayProperty;
use Sunhill\Properties\ReferenceArrayProperty;

uses(TestCase::class);

test('Simple array', function()
{
   $test = new ElementBuilder();
   expect(is_a($test->array('test_array'),ArrayProperty::class))->toBe(true);
   expect(is_a($test->getElements()['test_array'],ArrayProperty::class))->toBe(true);
});

test('Array of references', function()
{
   $test = new ElementBuilder(); 
   expect(is_a($test->arrayOfReferences('test_array'),ReferenceArrayProperty::class))->toBe(true);
   expect(is_a($test->getElements()['test_array'],ReferenceArrayProperty::class))->toBe(true);
});
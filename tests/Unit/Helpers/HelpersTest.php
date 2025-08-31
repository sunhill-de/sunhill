<?php
use Sunhill\Tests\SunhillSimpleTestCase;

/**
 * @file HelpersTest.php
 * tests: /src/Helpers/sunhill_helpers.php
 * free of dependent units: yes
 */

uses(SunhillSimpleTestCase::class);

/**
 * Because we use a SimpleTestCase the boot process is not performed
 */
require_once(dirname(__FILE__).'/../../../src/Helpers/sunhill_helpers.php');

test('makeStdClass() works as expected (simple)', function()
{
   $result = makeStdclass(['keyA'=>'valueA','keyB'=>'valueB']);
   expect($result->keyA)->toBe('valueA');
   expect($result->keyB)->toBe('valueB');   
});

test('makeStdClass() works as expected (nested)', function()
{
    $result = makeStdclass(['keyA'=>'valueA','keyB'=>['subkeyA'=>'subvalueA','subkeyB'=>'subvalueB']]);
    expect($result->keyA)->toBe('valueA');
    expect($result->keyB->subkeyA)->toBe('subvalueA');
    expect($result->keyB->subkeyB)->toBe('subvalueB');
});

test('getScalarMessage works with scalar', function()
{
    $variable = 'scalar';
    expect(getScalarMessage("This is :variable or not", $variable))->toBe("This is 'scalar' or not");
});

test('getScalarMessage works with non-scalar', function()
{
    $variable = new \stdClass();
    expect(getScalarMessage("This is :variable or not", $variable))->toBe("This is  or not");    
});

test('getScalarMessage works with non-scalar and replacement', function()
{
    $variable = new \stdClass();
    expect(getScalarMessage("This is :variable or not", $variable,"non-scalar"))->toBe("This is non-scalar or not");
});


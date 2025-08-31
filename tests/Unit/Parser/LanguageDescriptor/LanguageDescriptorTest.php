<?php
/**
 * @file LanguageDesciptorTest.php
 * tests: /src/Parser/LanguageDescriptor/LanguageDescriptor.php
 * free of dependent units: no (addOperator creates an instance of OperatorDescriptor
 */

use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Parser\LanguageDescriptor\LanguageDescriptor;
use Sunhill\Parser\Exceptions\LanguageDescriptorException;

uses(SunhillSimpleTestCase::class);

test('addDefaultTerminal', function()
{
    $test = new LanguageDescriptor();
    $test->addDefaultTerminal('integer');
    expect($test->getDefaultTerminals()[0])->toBe('INTEGER');
});

test('addDefaultTerminal fails', function()
{
    $test = new LanguageDescriptor();
    $test->addDefaultTerminal('unknown');
})->throws(LanguageDescriptorException::class);

test('addUnaryperator() and getUnaryOperator()', function()
{
    $test = new LanguageDescriptor();
    $operator = $test->addUnaryOperator('+');
    expect($test->getUnaryOperator('+'))->toBe($operator);
});

test('getUnaryOperator() with unknown operator', function()
{
    $test = new LanguageDescriptor();
    $test->addUnaryOperator('+');
    expect($test->getUnaryOperator('-'))->toBe(null);
});

test('addBinaryOperator() and getBinaryOperator()', function()
{
    $test = new LanguageDescriptor();
    $operator = $test->addBinaryOperator('+');
    expect($test->getBinaryOperator('+'))->toBe($operator);
});

test('getBinaryOperator() with unknown operator', function()
{
    $test = new LanguageDescriptor();
    $test->addBinaryOperator('+');
    expect($test->getBinaryOperator('-'))->toBe(null);
});
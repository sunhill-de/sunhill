<?php
/**
 * @file LanguageDesciptorTest.php
 * tests: /src/Parser/LanguageDescriptor/LanguageDescriptor.php
 * free of dependent units: no (addOperator creates an instance of OperatorDescriptor
 */

use Sunhill\Parser\Exceptions\LanguageDescriptorException;
use Sunhill\Parser\LanguageDescriptor\LanguageDescriptor;
use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Parser\LanguageDescriptor\TerminalDescriptor;

uses(SunhillSimpleTestCase::class);

test('addAcceptedFinal() with string and getAcceptedFinals()', function()
{
    $test = new LanguageDescriptor();
    $test->addAcceptedFinal('SOMETHING');
    expect($test->getAcceptedFinals())->toBe(['SOMETHING']);
});

test('addAcceptedFinal() with array and getAcceptedFinals()', function()
{
    $test = new LanguageDescriptor();
    $test->addAcceptedFinal(['SOMETHING','ELSE']);
    expect($test->getAcceptedFinals())->toBe(['SOMETHING','ELSE']);
});

test('addAcceptedFinal() with array and merge and getAcceptedFinals()', function()
{
    $test = new LanguageDescriptor();
    $test->addAcceptedFinal(['SOMETHING','ELSE']);
    $test->addAcceptedFinal(['SOME','MORE']);
    expect($test->getAcceptedFinals())->toBe(['SOMETHING','ELSE','SOME','MORE']);
});
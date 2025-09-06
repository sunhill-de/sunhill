<?php
/**
 * @file LanguageDesciptorTest.php
 * Type: Feature test
 * Tests: Interaction between LanguageDescriptor and the other descriptors
 * Created: 2025-09-06
 * Reviewd: 2025-09-06
 */

use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Parser\LanguageDescriptor\LanguageDescriptor;
use Sunhill\Parser\LanguageDescriptor\TerminalDescriptor;
use Sunhill\Parser\Exceptions\LanguageDescriptorException;
use Sunhill\Parser\ParserRule;
use Sunhill\Parser\LanguageDescriptor\FunctionDescriptor;

uses(SunhillSimpleTestCase::class);

// ************************* addTerminal() ************************************
test('add default terminal', function ()
{
    $test = new LanguageDescriptor();
    $test->addTerminal(TerminalDescriptor::INTEGER_TERMINAL);
    expect($test->getTerminals()[0])->getTerminal()->toBe('INTEGER');
    expect($test->getTerminals()[0])->getType()->toBe('default');
});

test('add other terminal', function()
{
    $test = new LanguageDescriptor;
    $test->addTerminal('&&');
    expect($test->getTerminals()[0])->getTerminal()->toBe('&&');
    expect($test->getTerminals()[0])->getType()->toBe('symbol');
});

test('addTerminal fails', function () {
    $test = new LanguageDescriptor;
    $test->addTerminal(9999);
})->throws(LanguageDescriptorException::class);

// ************************ addRule() ****************************
test('addRule()', function () 
{
    $test = new LanguageDescriptor;
    $rule = $test->addRule('SUM',['SUM','+','FACTOR']);
    expect(is_a($rule, ParserRule::class))->toBe(true);
    expect($test->getParserRules()[0]->getLeftHand())->toBe('SUM');
});

// ********************** addFunction() ***************************
test('addFunction()', function ()
{
    $test = new LanguageDescriptor;
    $rule = $test->addFunction('testfunct');
    expect(is_a($rule, FunctionDescriptor::class))->toBe(true);
    expect($test->getFunctions()[0]->getName())->toBe('testfunct');
});


                    
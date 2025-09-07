<?php
/**
 * @file ParserRulerTest.php
 * tests: /src/Parser/ParserRule.php
 * free of dependent units: yes
 */
use Sunhill\Parser\ParserRule;
use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);

test('Constructor sets the left and right hand with array as right hand', function()
{
   $test = new ParserRule('result', ['ruleA', 'ruleB']);
   expect($test->getLeftHand())->toBe('result');
   expect($test->getRightHand())->toBe(['ruleA','ruleB']);
   expect($test->getRightHandRuleCount())->toBe(2);
});

test('Constructor sets the left and right hand with string as right hand', function()
{
    $test = new ParserRule('result', 'rule');
    expect($test->getLeftHand())->toBe('result');
    expect($test->getRightHand())->toBe(['rule']);
    expect($test->getRightHandRuleCount())->toBe(1);
});

test('set/getPriority() works', function()
{
    $test = new ParserRule('result', 'rule');
    $test->setPriority(10);
    expect($test->getPriority())->toBe(10);
});

test('set/getASTCallback() works with string', function()
{
    $test = new ParserRule('result', 'rule');
    $test->setASTCallback('something');
    expect($test->getASTCallback())->toBe('something');
});

test('set/getASTCallback() works with callback', function()
{
    $test = new ParserRule('result', 'rule');
    $function = function() { return 'OK'; };
    $test->setASTCallback($function);
   
    expect($test->getASTCallback())->toBe($function);
});

test('set/getTypes works with rule', function()
{
    $test = new ParserRule('result', 'rule');
    $test->setTypes([
        ['typeA','result']
    ]);
    expect($test->getTypes())->toBe([
        ['typeA','result']
    ]);
});

test('set/getTypes works with nothing', function()
{
    $test = new ParserRule('result', 'rule');
    expect($test->getTypes())->toBe(null);
});

<?php
/**
 * @file TerminalDesciptorTest.php
 * tests: /src/Parser/LanguageDescriptor/TerminalDescriptor.php
 * free of dependent units: yes
 */

use Sunhill\Parser\Exceptions\LanguageDescriptorException;
use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Parser\LanguageDescriptor\TerminalDescriptor;

uses(SunhillSimpleTestCase::class);

test('symbol terminator ', function () {
    $test = new TerminalDescriptor('+');
    expect($test->getTerminal())->toBe('+');
    expect($test->getType())->toBe('symbol');
});

test('default terminator', function($id, $symbol)
{
    $test = new TerminalDescriptor($id);
    expect($test->getTerminal())->toBe($symbol);
    expect($test->getType())->toBe('default');
})->with([
    [TerminalDescriptor::INTEGER_TERMINAL, 'INTEGER'],
    [TerminalDescriptor::BOOLEAN_TERMINAL, 'BOOLEAN'],
    [TerminalDescriptor::FLOAT_TERMINAL, 'FLOAT'],
    [TerminalDescriptor::DATETIME_TERMINAL, 'DATETIME'],
    [TerminalDescriptor::TIME_TERMINAL, 'TIME'],
    [TerminalDescriptor::DATE_TERMINAL, 'DATE'],
    [TerminalDescriptor::IDENTIFIER_TERMINAL, 'IDENTIFIER'],
    [TerminalDescriptor::STRING_TERMINAL, 'STRING'],
    ]);

test('setLAPrecedence() and getLAPrecedence', function () {
    $test = new TerminalDescriptor('+');
    $test->setLAPrecedence(10);
    expect($test->getLAPrecedence())->toBe(10);
});

it('fails when setting a wrong ID', function () {
    $test = new TerminalDescriptor(-1);
})->throws(LanguageDescriptorException::class);

test('aliasFor() works', function()
{
    $test = new TerminalDescriptor('&&');
    expect($test->getReturnTerminal())->toBe('&&');
    $test->aliasFor('and');
    expect($test->getReturnTerminal())->toBe('and');
});

test('set/getCaseSensitive works', function()
{
    $test = new TerminalDescriptor('and');
    expect($test->getCaseSensititve())->toBe(false);
    $test->setCaseSesitive();
    expect($test->getCaseSensititve())->toBe(true);    
});
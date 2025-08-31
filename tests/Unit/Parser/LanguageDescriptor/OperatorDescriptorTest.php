<?php
/**
 * @file OperatorDesciptorTest.php
 * tests: /src/Parser/LanguageDescriptor/OperatorDescriptor.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Parser\LanguageDescriptor\OperatorDescriptor;
use Sunhill\Parser\Exceptions\LanguageDescriptorException;

uses(SunhillSimpleTestCase::class);

test('getOperator()', function()
{
   $test = new OperatorDescriptor('+');
   expect($test->getOperator())->toBe('+');
});

test('setType() and getType()', function()
{
    $test = new OperatorDescriptor('+');
    expect($test->setType('binary'))->toBe($test);
    expect($test->getType())->toBe('binary');    
});

it('fails when setting wrong type', function()
{
    $test = new OperatorDescriptor('+');
    $test->setType('unknown');
})->throws(LanguageDescriptorException::class);

test('setPrecedence() and getPrecedence()', function()
{
    $test = new OperatorDescriptor('+');
    $test->setType('binary');
    expect($test->setPrecedence(20))->toBe($test);
    expect($test->getPrecedence())->toBe(20);
});

test('addTypes() for binary and getAcceptedTypes', function()
{
    $test = new OperatorDescriptor('+');
    $test->setType('binary');
    expect($test->addTypes('integer','integer','integer'))->toBe($test);
    $test->addTypes('float','float','float');
    
    expect($test->getAcceptedTypes())->toBe(
        [['integer','integer','integer'],['float','float','float']]
        );    
});

test('addTypes() for unary and getAcceptedTypes', function()
{
    $test = new OperatorDescriptor('+');
    $test->setType('unary');
    expect($test->addTypes('integer','integer'))->toBe($test);
    $test->addTypes('float','float');
    
    expect($test->getAcceptedTypes())->toBe(
        [['integer','integer'],['float','float']]
        );
});

test('addTypes() fails for unary operator and three parameters', function()
{
    $test = new OperatorDescriptor('+');
    $test->setType('unary');
    $test->addTypes('integer','integer','integer');
})->throws(LanguageDescriptorException::class);


test('addTypes() fails for binary operator and two parameters', function()
{
    $test = new OperatorDescriptor('+');
    $test->setType('binary');
    $test->addTypes('integer','integer');
})->throws(LanguageDescriptorException::class);


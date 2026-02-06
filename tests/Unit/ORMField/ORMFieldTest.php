<?php

use Sunhill\Tests\TestCase;
use Sunhill\ORMField\ORMField;
use Sunhill\ORMField\ORMFieldException;

uses(TestCase::class);

test('constructor and getName() works', function()
{
   $test = new ORMField('test', 'string');
   expect($test->getName())->toBe('test');
});

test('constructor and getType() works', function()
{
    $test = new ORMField('test', 'string');
    expect($test->getType())->toBe('string');
});

it('fails when name is empty', function()
{
    $test = new ORMField('', 'string'); 
})->throws(ORMFieldException::class);

it('fails when type is empty', function()
{
    $test = new ORMField('test', '');
})->throws(ORMFieldException::class);

test('setAttribute() and getAttribute() work', function()
{
   $test = new ORMField('test', 'string');
   $test->setAttribute('testattribute','testvalue');
   expect($test->getAttribute('testattribute'))->toBe('testvalue');
});

test('hasAttribute() works', function()
{
    $test = new ORMField('test', 'string');
    expect($test->hasAttribute('testattribute'))->toBe(false);
    $test->setAttribute('testattribute','testvalue');
    expect($test->hasAttribute('testattribute'))->toBe(true);
});

test('setAttribute() and getAttribute() work with null', function()
{
    $test = new ORMField('test', 'string');
    $test->setAttribute('testattribute',null);
    expect($test->hasAttribute('testattribute'))->toBe(true);
    expect($test->getAttribute('testattribute'))->toBe(null);
});

test('unsetAttribute() works', function()
{
    $test = new ORMField('test', 'string');
    $test->setAttribute('testattribute', 'abc');
    $test->unsetAttribute('testattribute');
    expect($test->hasAttribute('attribute'))->toBe(false);
});

it('fails when trying to get an unset attribute', function()
{
    $test = new ORMField('test', 'string');
    expect($test->getAttribute('testattribute'))->toBe(null);    
})->throws(ORMFieldException::class);

<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Modules\Module;
use Sunhill\Modules\Exceptions\InvalidModuleNameException;

uses(SunhillTestCase::class);

test('name', function()
{
   $test = new Module();
   
   expect($test->setName('testmodule'))->toBe($test);   
   expect($test->getName())->toBe('testmodule');
});

it('fails with invalid name', function()
{
    $test = new Module();
    
    $test->setName('testmodüle');
})->throws(InvalidModuleNameException::class);

test('visiblename not set', function()
{
    $test = new Module();
    
    expect($test->setName('testmodule'))->toBe($test);
    expect($test->getVisibleName())->toBe('testmodule');
});

test('visiblename set', function()
{
    $test = new Module();
    
    expect($test->setName('testmodule'))->toBe($test);
    expect($test->setVisibleName('test module'))->toBe($test);
    expect($test->getVisibleName())->toBe('test module');
});

test('name empty', function()
{
    $test = new Module();
    
    expect($test->getName())->toBe('');
});

test('parent', function()
{
    $parent = new Module();
    $test = new Module();
    
    expect($test->setParent($parent))->toBe($parent);
    expect($test->getParent())->toBe($parent);
});

test('getParents()', function()
{
    $grandparent = new Module();
    $parent = new Module();
    $test = new Module();
    
    $parent->setParent($grandparent);
    $test->setParent($parent);
    
    expect($test->getParents())->toBe([$grandparent, $parent]);    
    expect($test->getParents(true))->toBe([$grandparent, $parent, $test]);
});

test('parent empty', function()
{
    $test = new Module();
    
    expect($test->getParent())->toBe(null);
    expect($test->getParents())->toBe([]);
    expect($test->getParents(true))->toBe([$test]);
});

test('getParentNames()', function()
{
    $grandparent = new Module();
    $grandparent->setName('grandparent');
    $parent = new Module();
    $parent->setName('parent');
    $test = new Module();
    $test->setName('child');
    
    $parent->setParent($grandparent);
    $test->setParent($parent);
    
    expect($test->getParentNames())->toBe(['grandparent','parent','child']);
    expect($test->getParentNames('.'))->toBe('grandparent.parent.child');
});


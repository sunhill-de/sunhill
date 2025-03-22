<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Modules\Module;
use Sunhill\Modules\Exceptions\InvalidModuleNameException;
use Sunhill\Modules\Exceptions\ChildNotFoundException;

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

test('Description setter and getter works', function() 
{
    $test = new Module();
    $test->setDescription('test');
    expect($test->getDescription())->toBe('test');
});
    
test('Description setter and getter works with empty description', function()
{
    $test = new Module();
    expect($test->getDescription())->toBe(null);
});

test('parent', function()
{
    $parent = new Module();
    $test = new Module();
    expect($test->hasParent())->toBe(false);
    expect($test->setParent($parent))->toBe($parent);
    expect($test->hasParent())->toBe(true);
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

test('Add child works with name', function() 
{
    $parent = new Module();
    $parent->setName('parent');
    $child = new Module();
    $child->setName('child');
    
    expect($parent->hasChildren())->toBe(false);
    $parent->addChild($child, 'child');
    expect($parent->hasChildren())->toBe(true);
});
    
test('Add child works with name to overwrite', function() 
{
    $parent = new Module();
    $parent->setName('parent');
    $child = new Module();
    $child->setName('child');
    
    $parent->addChild($child, 'justachild');
    expect($child->getName())->toBe('justachild');
});
        
test('Add child works with default name ', function()
{
    $parent = new Module();
    $parent->setName('parent');
    $child = new Module();
    $child->setName('child');
    
    $parent->addChild($child);
    expect($parent->hasChild('child'))->toBe(true);
});

test('parent is added as owner', function()
{
    $parent = new Module();
    $parent->setName('parent');
    $child = new Module();
    $child->setName('child');
    
    $parent->addChild($child,'child');
    
    expect($child->getParent())->toBe($parent);
});

test('Flush children works', function() 
{
    $parent = new Module();
    $parent->setName('parent');
    $child1 = new Module();
    $child1->setName('child1');
    $child2 = new Module();
    $child2->setName('child2');
    
    $parent->addChild($child1,'child1');
    $parent->addChild($child2,'child2');
    $parent->flushChildren();
    
    expect($parent->hasChildren())->toBe(false);
});
    
test('Delete child works', function()
{
    $parent = new Module();
    $parent->setName('parent');
    $child1 = new Module();
    $child1->setName('child1');
    $child2 = new Module();
    $child2->setName('child2');
    
    $parent->addChild($child1,'child1');
    $parent->addChild($child2,'child2');
    
    expect($parent->hasChild('child1'))->toBe(true);
    $parent->deleteChild('child1');
    expect($parent->hasChild('child1'))->toBe(false);
});

test('Get child works', function()
{
    $parent = new Module();
    $parent->setName('parent');
    $child1 = new Module();
    $child1->setName('child1');
    $child2 = new Module();
    $child2->setName('child2');
    
    $parent->addChild($child1,'child1');
    $parent->addChild($child2,'child2');
    
    expect($parent->getChild('child1'))->toBe($child1);
});

it('Fails when wrong child name is used in getChild()', function()
{
    $parent = new Module();
    $parent->setName('parent');
    $child1 = new Module();
    $child1->setName('child1');
    $child2 = new Module();
    $child2->setName('child2');
    
    $parent->addChild($child1,'child1');
    $parent->addChild($child2,'child2');
    
    $parent->getChild('nonexisting');
})->throws(ChildNotFoundException::class);

it('Fails when wrong child name is used in deleteChild()', function()
{
    $parent = new Module();
    $parent->setName('parent');
    $child1 = new Module();
    $child1->setName('child1');
    $child2 = new Module();
    $child2->setName('child2');
    
    $parent->addChild($child1,'child1');
    $parent->addChild($child2,'child2');
    
    $parent->deleteChild('nonexisting');
})->throws(ChildNotFoundException::class);

test('Breadcrumbs works', function()
{
    $parent = new Module();
    $parent->setName('parent');
    $parent->setVisibleName('parent module');
    
    $child = new Module();
    $child->setName('child');
    $child->setVisibleName('child module');
    $child->setParent($parent);
    
    $breadcrumbs = $child->getBreadcrumbs();
    expect(array_keys($breadcrumbs))->toBe(['/parent/','/parent/child/']);
    expect(array_values($breadcrumbs))->toBe(['parent module','child module']);
});

test('addSubmodule works', function()
{
    $parent = new Module();
    $parent->setName('parent');
    $child = new Module();
    $child->setName('child');
    $parent->addSubmodule($child);
    
    expect($child->getParent())->toBe($parent);
});

test('addSubmodule works with overwriting name', function()
{
    $parent = new Module();
    $parent->setName('parent');
    $child = new Module();
    $child->setName('child');
    $parent->addSubmodule($child, 'submodule');
    
    expect($child->getName())->toBe('submodule');
});

test('addSubmodule works with classname', function()
{
    $parent = new Module();
    $parent->setName('parent');
    $parent->addSubmodule(Module::class, 'child');
    
    expect($parent->hasChild('child'))->toBe(true);
});

test('addSubmodule works with classname and callback', function()
{
    $parent = new Module();
    $parent->setName('parent');
    $parent->addSubmodule(Module::class, 'child', function($child) 
    {
        $child->addSubmodule(Module::class, 'subchild');
    });
        
    expect($parent->getChild('child')->hasChild('subchild'))->toBe(true);
});


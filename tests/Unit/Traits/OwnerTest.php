<?php

use Sunhill\Framework\Traits\Owner;

uses(\Sunhill\Framework\Tests\TestCase::class);

class DummyOwnerObject {
    
    use Owner;
    
    public $name;
    
    public function getName()
    {
        return $this->name;
    }
    
}

test('Setter and getter works', function() {
   $parent = new DummyOwnerObject();
   $parent->name = 'parent';
   $child = new DummyOwnerObject();
   $child->name = 'child';
   
   $child->setOwner($parent);
   expect($child->getOwner())->toBe($parent);
   expect($child->hasOwner())->toBe(true);
   expect($parent->hasOwner())->toBe(false);
});

test('Get hirarchy works', function() {
    $parent = new DummyOwnerObject();
    $parent->name = 'parent';
    $child = new DummyOwnerObject();
    $child->name = 'child';
    
    $child->setOwner($parent);
    
    $hirarchy = $child->getHirachy();
    $keys = array_keys($hirarchy);
    expect($keys)->toBe(['parent','child']);
    expect($hirarchy['parent'])->toBe($parent);
});

test('Get hirarchy works with empty name', function() {
    $parent = new DummyOwnerObject();
    $parent->name = 'parent';
    $child = new DummyOwnerObject();
    $child->name = '';
    
    $child->setOwner($parent);
    
    $hirarchy = $child->getHirachy();
    $keys = array_keys($hirarchy);
    expect($keys)->toBe(['parent','']);
    expect($hirarchy['parent'])->toBe($parent);
});
        
test('Get path works', function() {
    $parent = new DummyOwnerObject();
    $parent->name = 'parent';
    $child = new DummyOwnerObject();
    $child->name = 'child';
    
    $child->setOwner($parent);
    
    expect($child->getPath())->toBe('/parent/child/');
});

test('Get path works with empty name', function() {
    $parent = new DummyOwnerObject();
    $parent->name = 'parent';
    $child = new DummyOwnerObject();
    $child->name = '';
    
    $child->setOwner($parent);
    
    expect($child->getPath())->toBe('/parent/');
});
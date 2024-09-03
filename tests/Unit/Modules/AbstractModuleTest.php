<?php

use Sunhill\Framework\Modules\AbstractModule;
use Sunhill\Framework\Tests\TestCase;

uses(TestCase::class);

test('name works', function () {
    $test = new AbstractModule();
    $test->setName('test');
    expect($test->getName())->toBe('test');
});

test('owner works', function() {
    $parent = new AbstractModule();
    $parent->setName('parent');
    $child = new AbstractModule();
    $child->setName('child');
    $child->setOwner($parent);
    expect($child->getOwner())->toBe($parent);
    expect($child->hasOwner())->toBe(true);
});

test('hirarchy works', function() {
        $parent = new AbstractModule();
        $parent->setName('parent');
        $child = new AbstractModule();
        $child->setName('child');
        $child->setOwner($parent);
        $hirarchy = $child->getHirachy();
        $keys = array_keys($hirarchy);
        expect($keys)->toBe(['parent','child']);
        expect($hirarchy['parent'])->toBe($parent);
});


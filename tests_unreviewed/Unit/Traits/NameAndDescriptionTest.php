<?php

use Sunhill\Traits\NameAndDescription;

uses(\Sunhill\Tests\TestCase::class);

class DummyNameAndDescriptionObject {
    
    use NameAndDescription;
    
}

test('Name setter and getter works', function() {
    $test = new DummyNameAndDescriptionObject();
    $test->setName('test');
    expect($test->getName())->toBe('test');
});

test('Description setter and getter works', function() {
    $test = new DummyNameAndDescriptionObject();
    $test->setDescription('test');
    expect($test->getDescription())->toBe('test');
});
        
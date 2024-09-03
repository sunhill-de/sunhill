<?php

use Sunhill\Framework\Traits\NameAndDescription;

uses(\Sunhill\Framework\Tests\TestCase::class);

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
        
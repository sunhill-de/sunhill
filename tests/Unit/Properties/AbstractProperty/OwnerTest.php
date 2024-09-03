<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Tests\TestSupport\Properties\NonAbstractProperty;
test('set owner', function () {
    $test1 = new NonAbstractProperty();
    $test1->setName('parent');

    $test2 = new NonAbstractProperty();
    $test2->setName('child');

    $test2->setOwner($test1);

    expect($test2->getOwner())->toEqual($test1);
});
test('get path', function () {
    $test1 = new NonAbstractProperty();
    $test1->setName('parent');

    $test2 = new NonAbstractProperty();
    $test2->setName('child');

    $test2->setOwner($test1);

    expect($test2->getPath())->toEqual('parent.child');
});
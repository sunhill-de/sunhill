<?php

/**
 * @file OwnerTest.php
 * tests: /src/Properties/AbstractProperty.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Tests\TestSupport\Properties\NonAbstractProperty;

uses(SunhillSimpleTestCase::class);

test('set owner', function () {
    $test1 = new NonAbstractProperty;
    $test1->setName('parent');

    $test2 = new NonAbstractProperty;
    $test2->setName('child');

    $test2->setOwner($test1);

    expect($test2->getOwner())->toEqual($test1);
});
test('get path', function () {
    $test1 = new NonAbstractProperty;
    $test1->setName('parent');

    $test2 = new NonAbstractProperty;
    $test2->setName('child');

    $test2->setOwner($test1);

    expect($test2->getPath())->toEqual('parent.child');
});

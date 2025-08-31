<?php

/**
 * @file StructureTest.php
 * tests: /src/Storage/AbstractPersistantStorage
 * free of dependent units: yes
 */
use Sunhill\Storage\Exceptions\StructureNeededException;
use Sunhill\Tests\SunhillLaravelTestCase;
use Sunhill\Tests\TestSupport\Storages\DummyAbstractPersistentStorage;

uses(SunhillLaravelTestCase::class);

it('fails when structure is needed', function () {
    $test = new DummyAbstractPersistentStorage;
    $test->pub_structureNeeded();
})->throws(StructureNeededException::class);

it('passes when structure is needed', function () {
    $test = new DummyAbstractPersistentStorage;
    $test->setStructure(makeStdclass(['elements' => [1, 2, 3]]));
    $test->pub_structureNeeded();
    expect(true)->toBe(true);
});

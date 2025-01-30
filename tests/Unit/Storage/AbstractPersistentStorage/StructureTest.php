<?php
use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Storages\DummyAbstractPersistentStorage;
use Sunhill\Storage\Exceptions\StructureNeededException;

uses(SimpleTestCase::class);

it('fails when structure is needed', function()
{
   $test = new DummyAbstractPersistentStorage();
   $test->pub_structureNeeded();
})->throws(StructureNeededException::class);

it('passes when structure is needed', function()
{
    $test = new DummyAbstractPersistentStorage();
    $test->setStructure(makeStdclass(['elements'=>[1,2,3]]));
    $test->pub_structureNeeded();
    expect(true)->toBe(true);
});
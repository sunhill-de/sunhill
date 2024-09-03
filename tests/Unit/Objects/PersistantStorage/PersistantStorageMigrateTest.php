<?php

use Sunhill\Properties\Tests\TestCase;
use Sunhill\Properties\Tests\Unit\Objects\PersistantStorage\Samples\DummyPersistantStorage;
use Sunhill\Properties\Objects\AbstractPersistantRecord;
use Sunhill\Properties\Types\TypeInteger;
use Sunhill\Properties\Objects\AbstractStorageAtom;
use Sunhill\Properties\Properties\ArrayProperty;

uses(TestCase::class);

test('simple migrate test', function()
{
    $record = \Mockery::mock(AbstractPersistantRecord::class);
    $record->shouldReceive('exportElements')->atLeast(1)->andReturn([
        'simplestorage'=>['simplefield'=>new TypeInteger()]
    ]);
    $atom = \Mockery::mock(AbstractStorageAtom::class);
    $atom->shouldReceive('migrateRecord')->with('simplestorage',['simplefield'=>new TypeInteger()]);
    
    $test = new DummyPersistantStorage($record);
    $test->atom = $atom;
    $test->migrate();
});

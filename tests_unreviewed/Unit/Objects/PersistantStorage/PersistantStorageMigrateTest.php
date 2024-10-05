<?php

use Sunhill\Tests\TestCase;
use Sunhill\Tests\Unit\Objects\PersistantStorage\Samples\DummyPersistantStorage;
use Sunhill\Objects\AbstractPersistantRecord;
use Sunhill\Types\TypeInteger;
use Sunhill\Objects\AbstractStorageAtom;
use Sunhill\Properties\ArrayProperty;

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

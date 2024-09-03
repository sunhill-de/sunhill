<?php

use Sunhill\Properties\Tests\TestCase;
use Sunhill\Properties\Objects\Exceptions\InvalidPrefixCalledException;
use Sunhill\Properties\Objects\Exceptions\InvalidPostfixCalledException;
use Sunhill\Properties\Tests\Unit\Objects\PersistantStorage\Samples\DummyStorageAtom;

uses(TestCase::class);

test('Handle call pass', function()
{
    $test = new DummyStorageAtom();
    $test->storeRecord('test',[]);
    expect(true)->toBe(true);
});

test('Handle call fails with invalid prefix', function()
{
    $test = new DummyStorageAtom();
    $test->dosomethingRecord('test',[]);    
})->throws(InvalidPrefixCalledException::class);

test('Handle call fails with invalid postfix', function()
{
    $test = new DummyStorageAtom();
    $test->storenknown('test',[]);    
})->throws(InvalidPostfixCalledException::class);
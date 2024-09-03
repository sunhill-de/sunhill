<?php

use Sunhill\Tests\TestCase;
use Sunhill\Objects\Exceptions\InvalidPrefixCalledException;
use Sunhill\Objects\Exceptions\InvalidPostfixCalledException;
use Sunhill\Tests\Unit\Objects\PersistantStorage\Samples\DummyStorageAtom;

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
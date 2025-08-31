<?php
/**
 * @file AbstractTagStorageTest.php
 * tests: /src/Tags/AbstractTagStorage.php
 * free of dependent units: yes
 */

use Sunhill\Tests\Unit\Tags\Examples\DummyTagStorage;
use Sunhill\Tags\Exceptions\TagNameNotFoundException;
use Sunhill\Tags\Exceptions\TagNameAmbiguousException;
use Sunhill\Tags\Exceptions\TagIDNotFoundException;
use Sunhill\Tests\SunhillLaravelTestCase;

uses(SunhillLaravelTestCase::class);

test('IDExists() pass', function()
{
    $test = new DummyTagStorage();
    expect($test->IDexists(1))->toBe(true);
});

test('IDExists() fail', function()
{
    $test = new DummyTagStorage();
    expect($test->IDExists(9999))->toBe(false);
});

test('searchName() is unique', function()
{
    $test = new DummyTagStorage();
    expect($test->searchName('TagB'))->toBe(2);
});

test('searchName() is ambigious', function()
{
    $test = new DummyTagStorage();
    $test->searchName('TagA');
})->throws(TagNameAmbiguousException::class);

test('searchName() not found', function()
{
    $test = new DummyTagStorage();
    $test->searchName('unknown');
})->throws(TagNameNotFoundException::class);

test('loadTag() with unique tag', function()
{
   $test = new DummyTagStorage();
   $tag = $test->load(1);
   expect($tag->name)->toBe('TagA');
});

test('loadTag() fails', function()
{
    $test = new DummyTagStorage();
    $tag = $test->load(999);
})->throws(TagIDNotFoundException::class);


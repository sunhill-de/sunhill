<?php

use Sunhill\Tags\Exceptions\TagNameNotFoundException;
use Sunhill\Tags\Exceptions\TagNameAmbiguousException;
use Sunhill\Tags\Exceptions\TagIDNotFoundException;
use Sunhill\Storage\MysqlStorage\MysqlTagStorage;
use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\Database\Seeds\TagsSeeder;
use Sunhill\Tests\Database\Seeds\TagCacheSeeder;

uses(SunhillDatabaseTestCase::class);

function prepareTags($test)
{
    $test->seed([
        TagsSeeder::class,
        TagCacheSeeder::class,
    ]);
   
}

test('IDExists() pass', function()
{
    prepareTags($this);
    $test = new MysqlTagStorage();
    expect($test->IDexists(1))->toBe(true);
});

test('IDExists() fail', function()
{
    prepareTags($this);
    $test = new MysqlTagStorage();
    expect($test->IDExists(9999))->toBe(false);
});

test('searchName() is unique', function()
{
    prepareTags($this);
    $test = new MysqlTagStorage();
    expect($test->searchName('TagB'))->toBe(2);
});

test('searchName() is ambigious', function()
{
    prepareTags($this);
    $test = new MysqlTagStorage();
    $test->searchName('TagA');
})->throws(TagNameAmbiguousException::class);

test('searchName() not found', function()
{
    prepareTags($this);
    $test = new MysqlTagStorage();
    $test->searchName('unknown');
})->throws(TagNameNotFoundException::class);

test('loadTag() with unique tag', function()
{
    prepareTags($this);
    $test = new MysqlTagStorage();
   $tag = $test->load(1);
   expect($tag->name)->toBe('TagA');
});

test('loadTag() fails', function()
{
    prepareTags($this);
    $test = new MysqlTagStorage();
    $tag = $test->load(999);
})->throws(TagIDNotFoundException::class);

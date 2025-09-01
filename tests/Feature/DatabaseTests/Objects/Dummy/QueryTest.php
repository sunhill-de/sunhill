<?php

use Sunhill\Tests\TestSupport\Objects\Dummy;
use Illuminate\Support\Facades\DB;
use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Query\Exceptions\UnexpectedResultCountException;

uses(SunhillDatabaseTestCase::class);

test('first() works', function()
{
   Dummy::prepareDatabase($this);
   DB::table('dummies')->where('id','<>',1)->delete();
   DB::table('objects')->where('id','<>',1)->delete();
   
   $result = Dummy::query()->first();
   expect($result)->toBeClass();
   expect($result->getID())->toBe(1);
});

test('firstID() works', function()
{
    Dummy::prepareDatabase($this);
    DB::table('dummies')->where('id','<>',1)->delete();
    DB::table('objects')->where('id','<>',1)->delete();
    
    $result = Dummy::query()->firstID();
    expect($result)->toBe(1);
});

test('firstOrFail() passes', function()
{
    Dummy::prepareDatabase($this);
    DB::table('dummies')->where('id','<>',1)->delete();
    DB::table('objects')->where('id','<>',1)->delete();
    
    $result = Dummy::query()->firstOrFail();
    expect($result)->toBeClass();
    expect($result->getID())->toBe(1);
});

test('firstOrFail() fails', function()
{
    Dummy::prepareDatabase($this);
    DB::table('dummies')->delete();
    DB::table('objects')->delete();
    
    $result = Dummy::query()->firstOrFail();
})->throws(UnexpectedResultCountException::class);

test('firstIDorFail() fails', function()
{
    Dummy::prepareDatabase($this);
    DB::table('dummies')->delete();
    DB::table('objects')->delete();
    
    $result = Dummy::query()->firstIDorFail();
})->throws(UnexpectedResultCountException::class);

test('exists() works', function()
{
    Dummy::prepareDatabase($this);
    DB::table('dummies')->where('id','<>',1)->delete();
    DB::table('objects')->where('id','<>',1)->delete();
    
    $result = Dummy::query()->exists();
    expect($result)->toBe(true);
});

test('exists() fails', function()
{
    Dummy::prepareDatabase($this);
    DB::table('dummies')->delete();
    DB::table('objects')->delete();
    
    $result = Dummy::query()->exists();
    expect($result)->toBe(false);
});

test('doesntExists() fails', function()
{
    Dummy::prepareDatabase($this);
    DB::table('dummies')->where('id','<>',1)->delete();
    DB::table('objects')->where('id','<>',1)->delete();
    
    $result = Dummy::query()->doesntExists();
    expect($result)->toBe(false);
});

test('doesntExists() passes', function()
{
    Dummy::prepareDatabase($this);
    DB::table('dummies')->delete();
    DB::table('objects')->delete();
    
    $result = Dummy::query()->doesntExists();
    expect($result)->toBe(true);
});


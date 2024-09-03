<?php

namespace Sunhill\Properties\Tests\Unit\Objects\MysqlStorage;

use Sunhill\Properties\Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Sunhill\Properties\Objects\Mysql\MysqlStorageAtom;

uses(TestCase::class);

test('Read with a simple table works', function() 
{
    $query = \Mockery::mock(\stdClass::class);
    $query->shouldReceive('where')->with('id',1)->andReturn($query);
    $query->shouldReceive('first')->andReturn(['id'=>1,'dummyint'=>123]);
    DB::shouldReceive('table')->with('dummycollections')->once()->andReturn($query);
    
    $test = new MysqlStorageAtom();
    $test->setSource('dummycollections','record');
    $items = $test->readItems(1);
    expect($items['dummyint'])->toBe(123);    
});

test('Read with a more complex table works', function() 
{
   $query = \Mockery::mock(\stdClass::class);
   $query->shouldReceive('where')->with('id',1)->andReturn($query);
   $query->shouldReceive('first')->andReturn(['id'=>1,'parentint'=>111,'parentchar'=>'AAA','parentfloat'=>1.11]);
   DB::shouldReceive('table')->with('testparents')->once()->andReturn($query);
   
   $test = new MysqlStorageAtom();
   $test->setSource('testparents','record');
   $items = $test->readItems(1);
   expect($items['parentint'])->toBe(111);
});

test('Read array', function()
{
    $query = \Mockery::mock(\stdClass::class);
    $query->shouldReceive('where')->with('container_id',1)->andReturn($query);
    $query->shouldReceive('get')->andReturn([123,234,345,456]);
    DB::shouldReceive('table')->with('testparent_intarray')->once()->andReturn($query);
    
    $test = new MysqlStorageAtom();
    $test->setSource('testparent_intarray','array');
    $items = $test->readItems(1);
    expect($items[1])->toBe(234);
});

test('Read object works', function()
{
    $query = \Mockery::mock(\stdClass::class);
    $query->shouldReceive('where')->with('id',1)->andReturn($query);
    $query->shouldReceive('first')->andReturn(['id'=>1,'class'=>'dummy','created_at'=>'2ß24-05-11- 20:42:00']);
    DB::shouldReceive('table')->with('objects')->once()->andReturn($query);
    
    $test = new MysqlStorageAtom();
    $test->setSource('objects','object');
    $items = $test->readItems(1);
    expect($items['class'])->toBe('dummy');    
});

test('Read uuid reference works', function()
{
    $query = \Mockery::mock(\stdClass::class);
    $query->shouldReceive('where')->with('uuid','eeb68004-ff2a-4382-8e45-6162e73120da')->andReturn($query);
    $query->shouldReceive('first')->andReturn(['uuid'=>'eeb68004-ff2a-4382-8e45-6162e73120da','property'=>'dummy','id'=>1]);
    DB::shouldReceive('table')->with('uuidreferences')->once()->andReturn($query);
    
    $test = new MysqlStorageAtom();
    $test->setSource('uuidreferences','uuid');
    $items = $test->readItems('eeb68004-ff2a-4382-8e45-6162e73120da');
    expect($items['property'])->toBe('dummy');
});
<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\PoolMysqlStorage\PoolMysqlStorage;
use Sunhill\Storage\Exceptions\StorageTableMissingException;
use Illuminate\Support\Facades\Schema;
use Sunhill\Storage\Exceptions\IDNotFoundException;
use Illuminate\Support\Facades\DB;

require_once('PrepareStorage.php');

uses(SunhillDatabaseTestCase::class);

test('Query a dummy', function($callback, $expect, $manipulator = null)
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'dummy', false));

    DB::table('objects')->insert([
        [
            'id'=>1,
            '_classname'=>'Dummy',
            '_uuid'=>'11b47be8-05f1-4f7b-8a97-e1e6488dbd44',            
        ],
        [
            'id'=>2,
            '_classname'=>'Dummy',
            '_uuid'=>'130003d9-e923-4408-9b37-be2dcf64b3cf',
        ],
        [
            'id'=>3,
            '_classname'=>'Dummy',
            '_uuid'=>'bb94149e-3181-45db-8892-97f6e2ccc468',
        ],
        [
            'id'=>4,
            '_classname'=>'Dummy',
            '_uuid'=>'79df362d-fea5-44fc-a6fc-4e47187220f2',
        ],
        [
            'id'=>5,
            '_classname'=>'Dummy',
            '_uuid'=>'082e7059-3bda-4825-bea1-040f5b29ae33',
        ],        
    ]);
    DB::table('dummies')->insert([
        ['id'=>1,'dummyint'=>111], 
        ['id'=>2,'dummyint'=>222],
        ['id'=>3,'dummyint'=>111],
        ['id'=>4,'dummyint'=>444],
        ['id'=>5,'dummyint'=>555],
    ]);
    $test->setTargetSubid('dummies');
    $query = $test->query();
    $result = $callback($query);
    if (is_callable($manipulator)) {
        $result = $manipulator($result);
    }
    expect($result)->toBe($expect);
})->with([
    'simple count()'=>[function($query) { return $query->count(); }, 5],
    'simple get()'=>[function($query) { return $query->get(); }, 111, function($result) { return $result[2]->dummyint; }],
    'simple first()'=>[function($query) { return $query->first(); }, 111, function($result) { return $result->dummyint; }],
    'count() with where 1'=>[function($query) { return $query->where('dummyint',111)->count(); },2],
    'count() with where 2'=>[function($query) { return $query->where('dummyint','>',111)->count(); },3],
    'count() with 2xwhere'=>[function($query) { return $query->where('dummyint','>',111)->where('dummyint','<',555)->count(); },2],
    ]);


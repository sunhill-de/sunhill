<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Illuminate\Support\Facades\DB;

uses(SunhillDatabaseTestCase::class);

test('create a dummy', function()
{
    Dummy::prepareDatabase($this);
    $test = new Dummy();
    $test->create();
    $test->dummyint = 10;
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>$test->getID(),'dummyint'=>10]);
    $query = DB::table('objects')->where('id',$test->getID())->first();
    expect(is_null($query->_created_at))->toBe(false);
    expect(($query->_created_at == $query->_updated_at))->toBe(true);
});


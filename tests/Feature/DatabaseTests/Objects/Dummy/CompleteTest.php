<?php

use Sunhill\Tests\SunhillKeepingDatabaseTestCase;
use Illuminate\Support\Facades\DB;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Illuminate\Support\Facades\Schema;

uses(SunhillKeepingDatabaseTestCase::class);

test('migrate dummy', function()
{
    Schema::dropIfExists('dummies');
    DB::table('objects')->truncate();
    DB::table('attributeobjectassigns')->truncate();
    DB::table('tagobjectassigns')->truncate();
    
    Dummy::migrate();
    
    $this->assertDatabaseHasTable('dummies');
});

test('create dummies', function()
{
    $data = [894,298,409,890,123,492,583];
    
    foreach ($data as $entry) {
        $test = new Dummy();
        $test->create();
        $test->dummyint = $entry;
        $test->commit();
    }
    
    $this->assertDatabaseHas('dummies',['dummyint'=>890]);
});

test('read dummy', function()
{
    $count = DB::table('dummies')->count();
    expect($count)->toBe(7);
    
    $test = new Dummy();
    $test->load(1);
    
    expect($test->dummyint)->toBe(894);
});
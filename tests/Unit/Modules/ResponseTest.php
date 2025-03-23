<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Modules\Module;
use Sunhill\Tests\Unit\Modules\Examples\DummyResponse;
use Illuminate\Support\Facades\Route;

uses(SunhillTestCase::class);

test('Sample homepage route', function()
{
    $module = new Module();

    $test = new DummyResponse();
    $test->setParent($module);
    
    $test->addRoute();

    $success = true;
    try {
        route('mainpage');
    } catch (\Exception $e) {
        $success = false;
    }
    expect($success)->toBe(true);
});

test('Sample root action route', function()
{
    $module = new Module();
    
    $test = new DummyResponse();
    $test->setParent($module);
    $test->setName('action');
    
    $test->addRoute();
    
    $success = true;
    try {
        route('action');
    } catch (\Exception $e) {
        $success = false;
    }
    expect($success)->toBe(true);
});

test('Sample root action route with alias', function()
{
    $module = new Module();
    
    $test = new DummyResponse();
    $test->setParent($module);
    $test->setName('action');
    
    $test->addRoute('aliasaction');
    
    $success = true;
    try {
        route('aliasaction');
    } catch (\Exception $e) {
        $success = false;
    }
    expect($success)->toBe(true);
});


test('Deep action route', function()
{
    $first = new Module();
    $first->setName('first');
    $second = new Module();
    $second->setName('second');
    $second->setParent($first);
    
    $test = new DummyResponse();
    $test->setParent($second);
    $test->setName('action');
    $test->addRoute();

    $success = true;
    try {
        route('first.second.action');
    } catch (\Exception $e) {
        $success = false;
    }
    expect($success)->toBe(true);
    
});

test('Deep action route with arguments', function()
{
    $first = new Module();
    $first->setName('first');
    $second = new Module();
    $second->setName('second');
    $second->setParent($first);
    
    $test = new DummyResponse();
    $test->setParent($second);
    $test->setName('action');
    $test->addRoute();
    $test->setArguments('{id}/{optional?}');
    $success = true;
    try {
        route('first.second.action');
    } catch (\Exception $e) {
        $success = false;
    }
    expect($success)->toBe(true);
    
});

test('Response is called', function()
{
    $first = new Module();
    $first->setName('first');
    $second = new Module();
    $second->setName('second');
    $second->setParent($first);
    
    $test = new DummyResponse();
    $test->setParent($second);
    $test->setName('action');
    $test->addRoute();
        
    $response = $this->get('/first/second/action');
    $response->assertStatus(200);
    $response->assertSee('ABC10');
});

test('Response is called with id and optional not given', function()
{
    $first = new Module();
    $first->setName('first');
    $second = new Module();
    $second->setName('second');
    $second->setParent($first);
    
    $test = new DummyResponse();
    $test->setParent($second);
    $test->setName('action');
    $test->setArguments('{id}/{optional?}');
    $test->addRoute();
    
    $response = $this->get('/first/second/action/20');
    $response->assertStatus(200);
    $response->assertSee('ABC20');
});

test('Response is called with id and optional', function()
{
    $first = new Module();
    $first->setName('first');
    $second = new Module();
    $second->setName('second');
    $second->setParent($first);
    
    $test = new DummyResponse();
    $test->setParent($second);
    $test->setName('action');
    $test->setArguments('{id}/{optional?}');
    $test->addRoute();
    
    $response = $this->get('/first/second/action/20/DEF');
    $response->assertStatus(200);
    $response->assertSee('DEF20');
});

test('Response throws an user exception', function()
{
    $first = new Module();
    $first->setName('first');
    $second = new Module();
    $second->setName('second');
    $second->setParent($first);
    
    $test = new DummyResponse();
    $test->setParent($second);
    $test->setName('action');
    $test->setArguments('{id}/{optional?}');
    $test->addRoute();
    $test->error = true;
    
    $response = $this->get('/first/second/action/20/DEF');
    $response->assertStatus(200);
});

test('Response is called with too many options', function()
{
    $first = new Module();
    $first->setName('first');
    $second = new Module();
    $second->setName('second');
    $second->setParent($first);
    
    $test = new DummyResponse();
    $test->setParent($second);
    $test->setName('action');
    $test->setArguments('{id}/{optional?}');
    $test->addRoute();
    
    $response = $this->get('/first/second/action/20/DEF/something');
    $response->assertStatus(404);
});
<?php

namespace Sunhill\Framework\Tests\Unit\Plugins;

use Sunhill\Framework\Tests\TestCase;
use IsaEken\PluginSystem\Enums\PluginState;
use Sunhill\Framework\Plugins\PluginQuery;
use Sunhill\Framework\Tests\Unit\Plugins\TestPlugins\TestPluginA;

uses(TestCase::class);

function getModule($name, $author, $version, $features, $state)
{
    $all_features = ['featureA','featureB','featureC'];
    $return = \Mockery::mock(TestPluginA::class);
    $return->shouldReceive('getName')->andReturn($name);
    $return->shouldReceive('getAuthor')->andReturn($author);
    $return->shouldReceive('getVersion')->andReturn($version);
    foreach ($all_features as $global_feature) {
        if (in_array($global_feature, $features)) {
            $return->shouldReceive('hasFeature')->with($global_feature)->andReturn(true);
        } else {
            $return->shouldReceive('hasFeature')->with($global_feature)->andReturn(false);            
        }
    }
    $return->shouldReceive('getState')->andReturn($state);
    
    return $return;
}

function getTestModules(): array
{
    $result = [];
    $result[] = getModule('testModuleA','Debby Debugger','0.0.1',['featureA','featureB'],'disabled');
    $result[] = getModule('testModuleB','Anton Author','0.1.1',['featureA'],'enabled');
    $result[] = getModule('Another Module','Carl Coder','1.0.1',['featureC'],'enabled');
    $result[] = getModule('And just another','Debby Debugger','1.0.1',['featureA','featureB'],'outdated');
    
    return $result;
}

function getTestQuery(): PluginQuery
{
    return new PluginQuery(getTestModules());    
}

test('Query returns right count', function()
{
    $query = getTestQuery();
    expect($query->count())->toBe(4);    
});

test('Query get returns all', function() 
{
    $query = getTestQuery();
    $list = $query->get();
    expect(count($list))->toBe(4);
    expect($list->get(1)->getName())->toBe('testModuleB');
});

test('Query first return first', function()
{
   $query = getTestQuery();
   expect($query->first()->getName())->toBe('testModuleA');
});

test('Order works', function($order, $expect)
{
    $query = getTestQuery();
    expect($query->orderBy($order)->first()->getName())->toBe($expect);    
})->with([
    ['name','And just another'],
    ['author','testModuleB'],
    ['version','testModuleA'],
    ['state', 'testModuleA']    
]);

test('where works', function($key, $relation, $value, $expect)
{
    $query = getTestQuery();
    $list = $query->where($key, $relation, $value)->get();
    expect($list[0]->getName())->toBe($expect);
})->with([
    ['name','=','testModuleB','testModuleB'],
    ['name','<','testModuleB','testModuleA'],
    ['name','>','testModuleA','testModuleB'],
    ['author','=','Debby Debugger','testModuleA'],
    ['version','=','0.1.1','testModuleB'],
    ['state','=','enabled','testModuleB']    
]);
<?php

use Sunhill\Framework\Response\Exceptions\MissingTemplateException;
use Sunhill\Framework\Modules\AbstractModule;
use Illuminate\Support\Facades\Route;
use Sunhill\Framework\Tests\Responses\SampleViewResponse;
use Sunhill\Framework\Tests\Responses\SampleAbstractResponse;

uses(\Sunhill\Framework\Tests\TestCase::class);

it('throws exception when no template is set', function() {
    $test = new SampleViewResponse();
    $test->getResponse();
})->throws(MissingTemplateException::class);

test('Sample parsing works', function() {
    $test = new SampleViewResponse();
    $test->setTemplate('framework::test.viewresponse');
    $test->setParameters(['sitename'=>'test']);
    expect($test->getResponse()->render())->toContain('TEST:abc');
});

    function getRouteSlugs()
    {
        $slugs  = [];
        $routes = Route::getRoutes();
        foreach ($routes as $route) {
            $slugs[] = $route->uri();
        }
            
        return array_unique($slugs);
    }
    
test('Routing works with', function($name, $hirachy, $arguments, $url, $expect_url, $aliasoverwrite, $alias) 
{
    $module = Mockery::mock(AbstractModule::class);
    $module->shouldReceive('getHirachy')->andReturn($hirachy);
    $module->shouldReceive('getPath')->once()->andReturn($url);
    $test = new SampleAbstractResponse();
    $test->setName($name);
    $test->setOwner($module);
    if (!empty($arguments)) {
        $test->setArguments($arguments);
    }
    $test->addRoute($aliasoverwrite);
    $slugs = getRouteSlugs();
    expect(in_array($expect_url,$slugs))->toBe(true);
    $success = true;
    try {
        route($alias, $arguments);
    } catch (\Exception $e) {
        $success = false;
    }
    expect($success)->toBe(true);
})->with(
[
    'simple route, no args, no alias'=>['action',['first'=>'dummy','second'=>'dummy'],'','/first/second/','first/second/action','','first.second.action'],   
    'simple route, no args, alias'=>['action',['first'=>'dummy','second'=>'dummy'],'','/first/second/','first/second/action','action','action'],
    'simple route, args, alias'=>['action',['first'=>'dummy','second'=>'dummy'],'{id}/{optional?}','/first/second/','first/second/action/{id}/{optional?}','action','action'],
    'root route, no args, alias'=>['',[],'/','/','/','main','main'],
    'root route, no args, no alias'=>['',[],'/','/','/','','mainpage'],
]);

test('Response is called', function() 
{
    $module = Mockery::mock(AbstractModule::class);
    $module->shouldReceive('getHirachy')->andReturn(['first'=>'dummy','second'=>'dummy']);
    $module->shouldReceive('getPath')->once()->andReturn('/first/second/');
    $test = new SampleAbstractResponse();
    $test->setName('action');
    $test->setOwner($module);
    $test->addRoute();

    $response = $this->get('/first/second/action');
    $response->assertStatus(200);
    $response->assertSee('ABC');
});
<?php

use Sunhill\Framework\Tests\TestCase;
use Sunhill\Framework\Modules\FeatureModules\FeatureModule;
use Sunhill\Framework\Response\ViewResponses\ViewResponse;
use Sunhill\Framework\Modules\Exceptions\CantProcessResponseException;

uses(TestCase::class);

test('Breadcrumbs works', function() {
   $parent = new FeatureModule();
   $parent->setName('parent');
   $parent->setDescription('parent module');

   $child = new FeatureModule();
   $child->setName('child');
   $child->setDescription('child module');
   $child->setOwner($parent);
   
   $breadcrumbs = $child->getBreadcrumbs();
   expect(array_keys($breadcrumbs))->toBe(['/parent/','/parent/child/']);
   expect(array_values($breadcrumbs))->toBe(['parent module','child module']);
});

test('addSubmodule works', function() {
    $parent = new FeatureModule();
    $parent->setName('parent');
    $child = new FeatureModule();
    $child->setName('child');
    $parent->addSubmodule($child);
    
    expect($child->getOwner())->toBe($parent);
});
        
test('addSubmodule works with overwriting name', function() {
    $parent = new FeatureModule();
    $parent->setName('parent');
    $child = new FeatureModule();
    $child->setName('child');
    $parent->addSubmodule($child, 'submodule');
    
    expect($child->getName())->toBe('submodule');
});
            
test('addSubmodule works with classname', function() {
    $parent = new FeatureModule();
    $parent->setName('parent');
    $parent->addSubmodule(FeatureModule::class, 'child');
    
    expect($parent->hasChild('child'))->toBe(true);
});
                
test('addSubmodule works with classname and callback', function() 
{
    $parent = new FeatureModule();
    $parent->setName('parent');
    $parent->addSubmodule(FeatureModule::class, 'child', function($child) {
        $child->addSubmodule(FeatureModule::class, 'subchild');
    });
    
    expect($parent->getChild('child')->hasChild('subchild'))->toBe(true);
});
        
test('addResponse works', function() 
{
    $parent = new FeatureModule();
    $parent->setName('parent');
    $response = Mockery::mock(ViewResponse::class);
    $response->shouldReceive('setOwner')->with($parent);
    $response->shouldReceive('setName')->with('testresponse');
    $parent->addResponse($response, 'testresponse');
    expect($parent->hasChild('testresponse'))->toBe(true);
});        

it('fails when wrong items are passed to addResponse', function($param)
{
   $parent = new FeatureModule();
   $parent->setName('parent');
   if (is_callable($param)) {
       $param = $param();
   }
   $parent->addResponse($param);
})->throws(CantProcessResponseException::class)->with(
  [
      'nonexisting',
      function() { return new \StdClass(); }
  ]);

test('addIndex works', function() 
{
        
});

test('defaultIndex works', function() 
{
    
});


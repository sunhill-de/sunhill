<?php

use Sunhill\Tests\TestCase;
use Sunhill\Facades\Site;
use Sunhill\Modules\FeatureModules\FeatureModule;
use Sunhill\Tests\Responses\SampleViewResponse;
use Sunhill\Tests\Responses\SampleIndividualIndexResponse;
use Sunhill\Tests\Responses\SampleParameterResponse;

uses(TestCase::class);

beforeEach(function() {
   Site::flushMainmodule();
   $main = Site::installMainmodule(FeatureModule::class);
   $main->defaultIndex();
   $main->addSubmodule(FeatureModule::class, 'sub1', function($sub) {
       $sub->defaultIndex();
       $sub->addResponse(SampleViewResponse::class,'action1')->setVisible()->addRoute();
       $sub->addResponse(SampleViewResponse::class,'action2')->setVisible()->addRoute('sub1.anotheraction');
       $sub->addResponse(SampleViewResponse::class,'action3')->setVisible(false)->addRoute('sub1.invisibleaction');
   });
   $main->addSubmodule(FeatureModule::class, 'sub2', function($sub) {
       $sub->addIndex(SampleIndividualIndexResponse::class);
       $sub->addSubmodule(FeatureModule::class, 'subsub', function($subsub) {
           $subsub->addResponse(SampleParameterResponse::class,'parameter')
                  ->setVisible(false)
                  ->setArguments('/{id}/{optional?}')
                  ->addRoute();
       });     
   });
});

test('Setup works', function() {
   $module = Site::getMainModule();
   expect($module->hasChildren())->toBe(true);
});
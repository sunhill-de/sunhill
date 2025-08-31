<?php
/**
 * @file InfomarketTest.php
 * tests: /src/Properties/AbstractProperty.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Tests\TestSupport\Properties\NonAbstractProperty;

uses(SunhillSimpleTestCase::class);

test('get metadata', function () 
{
    $test = new NonAbstractProperty();

    $metadata = $test->getMetadata();

    expect($metadata['update'])->toEqual('ASAP');
    expect($metadata['unit'])->toEqual('none');
    expect($metadata['semantic'])->toEqual('none');
});

test('requestItem with empty', function()
{
   $test = new NonAbstractProperty();
   
   expect($test->requestItem([]))->toBe($test);
});

test('requestItem with non empty', function()
{
   $test = new NonAbstractProperty();
   
   expect($test->requestItem(['test']))->toBe(null);
});
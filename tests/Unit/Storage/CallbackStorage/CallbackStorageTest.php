<?php
/**
 * @file CallbackStorageTest.php
 * tests: /src/CallbackStorage
 * free of dependent units: yes
 */
use Sunhill\Storage\Exceptions\FieldNotAvaiableException;
use Sunhill\Storage\CallbackStorage;
use Sunhill\Storage\Exceptions\CallbackMissingException;
use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);


test('read value', function () {
    $test = new CallbackStorage();
    $test->setCallback(function()
    {
        return ['keyA'=>'ValueA','keyB'=>'ValueB','keyC'=>['ABC','DEF']];
    });
    expect($test->getValue('keyA'))->toEqual('ValueA');
});

test('read unknown value', function () {
    $this->expectException(FieldNotAvaiableException::class);

    $test = new CallbackStorage();
    $test->setCallback(function()
    {
        return ['keyA'=>'ValueA','keyB'=>'ValueB','keyC'=>['ABC','DEF']];
    });
    $help = $test->getValue('NonExisting');
});

test('read array value', function () {
    $test = new CallbackStorage();
    $test->setCallback(function()
    {
        return ['keyA'=>'ValueA','keyB'=>'ValueB','keyC'=>['ABC','DEF']];
    });
    expect($test->getIndexedValue('keyC', 1))->toEqual('DEF');
});

test('array count', function () {
    $test = new CallbackStorage();
    $test->setCallback(function()
    {
        return ['keyA'=>'ValueA','keyB'=>'ValueB','keyC'=>['ABC','DEF']];
    });
    expect($test->getElementCount('keyC'))->toEqual(2);
});

test('an array count is returned', function() {
    $test = new CallbackStorage();
    $test->setCallback(function()
    {
        return ['keyA'=>'ValueA','keyB'=>'ValueB','keyC'=>['ABC','DEF']];
    });
    expect($test->getElementCount('keyC'))->toEqual(2);
});
        
test('an array element is returned', function() {
    $test = new CallbackStorage();
    $test->setCallback(function()
    {
        return ['keyA'=>'ValueA','keyB'=>'ValueB','keyC'=>['ABC','DEF']];
    });
    expect($test->getIndexedValue('keyC', 1))->toEqual('DEF');
});

it('fails when no storage is set', function()
{
    $test = new CallbackStorage();
    $test->getValue('keyA');
    
})->throws(CallbackMissingException::class);
            
<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Helpers\Matrix;

uses(SunhillTestCase::class);

test('Level 1 add of item works by string', function()
{
    $test = new Matrix();
    
    $test->setItem('keyA','valueA');
    expect($test->getItem('keyA'))->toBe('valueA');
});

test('Level 1 add of item works by array', function()
{
    $test = new Matrix();
    
    $test->setItem(['keyA'],'valueA');
    expect($test->getItem(['keyA']))->toBe('valueA');    
});

test('Level 2 add of item works by array', function()
{
    $test = new Matrix();
    
    $test->setItem(['keyA','subkeyA'],'subValueA');
    expect($test->getItem(['keyA','subkeyA']))->toBe('subValueA');    
});

test('Level 2 append of item works by array', function()
{
    $test = new Matrix();
    
    $test->setItem(['keyA','subkeyA'],'subValueA');
    $test->setItem(['keyA','subkeyB'],'subValueB');
    expect($test->getItem(['keyA','subkeyA']))->toBe('subValueA');
    expect($test->getItem(['keyA','subkeyB']))->toBe('subValueB');
});

test('Level 3 append of item works by array', function()
{
    $test = new Matrix();
    
    $test->setItem(['keyA','subkeyA'],'subValueA');
    $test->setItem(['keyA','subkeyB','deepKeyA'],'deepValueA');
    $test->setItem(['keyA','subkeyB','deepKeyB'],'deepValueB');
    expect($test->getItem(['keyA','subkeyA']))->toBe('subValueA');
    expect($test->getItem(['keyA','subkeyB'])->deepKeyA)->toBe('deepValueA');
    expect($test->getItem(['keyA','subkeyB','deepKeyB']))->toBe('deepValueB');
});

// ========================== Diff ====================================================
test('Level 1 diff (no change)', function()
{
   $test1 = new Matrix();
   $test1->setItem('keyA','valueA');
   $test1->setItem('keyB','valueB');
   
   $test2 = new Matrix();
   $test2->setItem('keyA','valueA');
   $test2->setItem('keyB','valueB');
   
   $diff = $test1->diff($test2);
   expect($diff)->toBe(null);
});

test('Level 1 diff (dropped key)', function()
{
    $test1 = new Matrix();
    $test1->setItem('keyA','valueA');
    $test1->setItem('keyB','valueB');
    
    $test2 = new Matrix();
    $test2->setItem('keyA','valueA');
    $test2->setItem('keyB','valueB');
    $test2->setItem('keyC','valueC');
    
    $diff = $test1->diff($test2);
    expect(isset($diff->dropped))->toBe(true);
    expect($diff->dropped->keyC)->toBe('valueC');
});

test('Level 1 diff (new key)', function()
{
    $test1 = new Matrix();
    $test1->setItem('keyA','valueA');
    $test1->setItem('keyB','valueB');
    
    $test2 = new Matrix();
    $test2->setItem('keyA','valueA');
    
    $diff = $test1->diff($test2);
    expect(isset($diff->new))->toBe(true);
    expect($diff->new->keyB)->toBe('valueB');
});

test('Level 1 diff (changed key)', function()
{
    $test1 = new Matrix();
    $test1->setItem('keyA','newvalueA');
    $test1->setItem('keyB','valueB');
    
    $test2 = new Matrix();
    $test2->setItem('keyA','valueA');
    $test2->setItem('keyB','valueB');
    
    $diff = $test1->diff($test2);
    expect(isset($diff->changed))->toBe(true);
    expect($diff->changed->keyA)->toBe('newvalueA');
});

test('Level 2 diff (no change)', function()
{
    $test1 = new Matrix();
    $test1->setItem(['keyA','subKeyAA'],'subvalueA');
    $test1->setItem(['keyA','subKeyAB'],'subvalueB');
    
    $test2 = new Matrix();
    $test2->setItem(['keyA','subKeyAA'],'subvalueA');
    $test2->setItem(['keyA','subKeyAB'],'subvalueB');
    
    $diff = $test1->diff($test2);
    expect($diff)->toBe(null);
});

test('Level 2 diff (new key)', function()
{
    $test1 = new Matrix();
    $test1->setItem(['keyA','subKeyAA'],'subvalueA');
    $test1->setItem(['keyA','subKeyAB'],'subvalueB');

    $test2 = new Matrix();
    $test2->setItem(['keyA','subKeyAA'],'subvalueA');

    $diff = $test1->diff($test2);
    expect(isset($diff->changed->keyA));
    expect($diff->changed->keyA->new->subKeyAB)->toBe('subvalueB');
});


test('Level 2 diff (dropped key)', function()
{
    $test1 = new Matrix();
    $test1->setItem(['keyA','subKeyAA'],'subvalueA');
    
    $test2 = new Matrix();
    $test2->setItem(['keyA','subKeyAA'],'subvalueA');
    $test2->setItem(['keyA','subKeyAB'],'subvalueB');
    
    $diff = $test1->diff($test2);
    expect(isset($diff->changed->keyA));
    expect($diff->changed->keyA->dropped->subKeyAB)->toBe('subvalueB');
});

test('Complex level 3 change', function()
{
    $test1 = new Matrix();
    $test1->setItem(['keyA','subKeyAA','deepKeyAAA'],'deepvalueAAA');
    $test1->setItem(['keyA','subKeyAA','deepKeyAAB'],'deepvalueAAB');
    $test1->setItem(['keyA','subKeyAB','deepKeyABA'],'deepvalueABA');
    $test1->setItem(['keyA','subKeyAB','deepKeyABB'],'deepvalueABB');
    $test1->setItem(['keyA','subKeyAC','deepKeyACA'],'deepvalueACA');
    $test1->setItem(['keyB','subKeyBA','deepKeyBAA'],'deepvalueBAA');
    
    $test2 = new Matrix();
    $test2->setItem(['keyA','subKeyAA','deepKeyAAA'],'deepvalueAAA');
    $test2->setItem(['keyA','subKeyAA','deepKeyAAB'],'deepvalueXXX');
    $test2->setItem(['keyA','subKeyAB','deepKeyABA'],'deepvalueABA');
    $test2->setItem(['keyA','subKeyAC','deepKeyACA'],'deepvalueACA');
    $test2->setItem(['keyA','subKeyAC','deepKeyACB'],'deepvalueACB');
    $test2->setItem(['keyB','subKeyBA','deepKeyBAA'],'deepvalueBAA');
    
    $diff = $test1->diff($test2);
    expect(isset($diff->changed->keyB))->toBe(false);
    expect($diff->changed->keyA->changed->subKeyAA->changed->deepKeyAAB)->toBe('deepvalueAAB');    
    expect($diff->changed->keyA->changed->subKeyAB->new->deepKeyABB)->toBe('deepvalueABB');
    expect($diff->changed->keyA->changed->subKeyAC->dropped->deepKeyACB)->toBe('deepvalueACB');
});
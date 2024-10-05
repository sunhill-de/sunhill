<?php

/**
 tests /tests/Pest.php
 */
use Sunhill\Tests\SimpleTestCase;

uses(SimpleTestCase::class);

class TestClass
{
    protected $protected_member = 10;
    
    protected function protectedMethod($param)
    {
        $old = $this->protected_member;
        $this->protected_member = $param;
        return $old;
    }
    
    public function getProtectedMember(): int
    {
        return $this->protected_member;
    }
}

test('getField() works', function($callback, $field, $expect)
{
    $test = $callback();
    expect(getField($test, $field))->toBe($expect);
})->with([
    [function() { return 10; }, null, 10],
    [function() 
    { 
        $return = new \StdClass();
        $return->subfield = 'ABC';
        return $return; 
    }, 'subfield', 'ABC'],
    [function()
    {
        $return = new \StdClass();
        $subfield = new \StdClass();
        $subfield->member = 'ABC';
        $return->subfield = $subfield;
        return $return;
    }, 'subfield->member', 'ABC'],
    [function()
    {
        return [1,2,3];
    }, '[1]', 2],
    [function()
    {
        return [[1,2,3],[4,5,6],[7.8,9]];
    }, '[1][1]', 5],
    [function()
    {
        $return = new \StdClass();
        $return->subfield = [1,2,3];
        return $return;
    }, 'subfield[1]', 2],
    [function()
    {
        $return = new \StdClass();
        $return->subfield =[[1,2,3],[4,5,6],[7.8,9]];
        return $return;
    }, 'subfield[1][1]', 5],
    ]);


test('callProtectedMethod() works', function()
{
    $test = new TestClass();
    $result = callProtectedMethod($test, 'protectedMethod',[20]);
    
    expect($result)->toBe(10);
    expect($test->getProtectedMember())->toBe(20);
});

test('invokeMethod() works', function()
{
    $test = new TestClass();
    $result = invokeMethod($test, 'protectedMethod',[20]);
    
    expect($result)->toBe(10);
    expect($test->getProtectedMember())->toBe(20);
});

test('getProtectedProperty() works', function()
{
    $test = new TestClass();
    
    expect(getProtectedProperty($test, 'protected_member'))->toBe(10);
});


test('setProtectedProperty() works', function()
{
    $test = new TestClass();
    
    setProtectedProperty($test, 'protected_member', 20);

    expect($test->getProtectedMember())->toBe(20);    
});

test('checkArrays works', function($first, $second, $expect)
{
    expect(checkArrays($first, $second))->toBe($expect);
})->with([
    [
        ['A','B','C'],['A','B','C'],true
    ],
    [
        ['A','B'],['A','B','C'],true,        
    ],
    [
        ['A','B','C'],['A','B'],false
    ],
]);

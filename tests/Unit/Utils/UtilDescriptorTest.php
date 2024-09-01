<?php

use Sunhill\Utils\Descriptor;
use Sunhill\Utils\DescriptorException;
use Sunhill\Tests\Unit\Utils\TestDescriptor;

test('Get() and Set()', function()
{
    $test = new Descriptor();
    $test->test = 'ABC';
    expect($test->test)->toBe('ABC');
});    

test('Not set', function()
{
    $test = new Descriptor();
    
    expect($test->notset->empty())->toBe(true);
});

test('empty', function()
{
    $test = new Descriptor();
    expect($test->empty())->toBe(true);
    expect($test->hasError())->toBe(false);
});

test('error', function()
{
    $test = new Descriptor();
    $test->set_error('There was an error');
    expect($test->hasError())->toBe('There was an error');
});

test('Double descriptor', function()
{
    $test = new Descriptor();
    $test->test1 = 'ABC';
    $test->test2->test = 'ABC';
    expect($test->test2->test)->toBe($test->test1);
});

/**
 *
 * @group double
 */
test('foreach', function()
{
    $test = new Descriptor();
    $test->test1 = 'ABC';
    $test->test2 = 'BCE';
    $test->anothertest = 123;
    $result = '';
    foreach ($test as $key => $value) {
        $result .= $key . '=>' . $value;
    }
    expect($result)->toBe('test1=>ABCtest2=>BCEanothertest=>123');
});

test('get()', function()
{
    $test = new Descriptor();
    $test->test1 = 'ABC';
    expect($test->get_test1())->toBe('ABC');
});

test('set()', function()
{
    $test = new Descriptor();
    $test->set_test1('ABC');
    expect($test->test1)->toBe('ABC');
});

test('Cascading set', function()
{
    $test = new Descriptor();
    $test->set_test1('ABC')->set_test2('DEF');
    expect($test->test2)->toBe('DEF');
});

test('Raise exception', function()
{
    $test = new Descriptor();
    $test->not_existing_function();
})->throws(DescriptorException::class);

test('No autoadd', function()
{
    $test = new TestDescriptor();
    $test->test3 = 'ABC';
})->throws(DescriptorException::class);

test('Pass change', function()
{
    $test = new TestDescriptor();
    $test->test = 'CBA';
    expect($test->test)->toBe('CBA');
    return $test;
});

/**
 * @depends testPassChange
 */
test('fail change', function()
{
    $test = new TestDescriptor();
    $test->test = 'CBA';
    $test->test = 'ZZZ';
})->throws(DescriptorException::class);

/**
 * @depends testPassChange
 */
test('Post trigger', function()
{
    $test = new TestDescriptor();
    $test->test = 'CBA';
    expect($test->flag)->toBe('ABC=>CBA');
});

test('Has key', function()
{
    $test = new Descriptor();
    $test->abc = 'abc';
    $this->assertTrue($test->isDefined('abc'));
    $this->assertFalse($test->isDefined('notdefined'));
});

test('Assert Key has', function()
{
    $test = new Descriptor();
    $test->abc = 'abc';
    $this->assertTrue($test->assertHasKey('abc'));
    $this->assertFalse($test->assertHasKey('notdefined'));
});

test('Asser Key is', function()
{
    $test = new Descriptor();
    $test->abc = 'abc';
    $this->assertTrue($test->assertKeyIs('abc','abc'));
    $this->assertFalse($test->assertKeyIs('notdefined','abc'));
    $this->assertFalse($test->assertKeyIs('abc','def'));
});

test('Assert Key Has 2', function()
{
    $test = new Descriptor();
    $test->abc = ['abc','def','ghi'];
    $this->assertTrue($test->assertKeyHas('abc','abc'));
    $this->assertFalse($test->assertKeyHas('notdefined','abc'));
    $this->assertFalse($test->assertKeyHas('abc','xyz'));
});


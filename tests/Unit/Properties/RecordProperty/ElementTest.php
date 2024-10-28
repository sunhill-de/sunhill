<?php

use Sunhill\Properties\RecordProperty;
use Sunhill\Tests\SunhillTestCase;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Properties\NonAbstractProperty;
use Sunhill\Properties\Exceptions\NotAPropertyException;
use Sunhill\Properties\Exceptions\PropertyNameAlreadyGivenException;
use Sunhill\Properties\Exceptions\PropertyHasNoNameException;
use Sunhill\Properties\Exceptions\PropertyAlreadyInListException;
use Sunhill\Properties\Exceptions\InvalidInclusionException;
use Sunhill\Properties\Exceptions\NotAllowedInclusionException;
use Sunhill\Tests\TestSupport\Properties\ChildRecordProperty;
use Sunhill\Tests\TestSupport\Properties\ParentRecordProperty;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Properties\Exceptions\PropertyNotFoundException;
use Sunhill\Properties\ArrayProperty;

uses(SunhillTestCase::class);

test('appendElement() with only an element object', function()
{
    $test = new RecordProperty();
    $element = new NonAbstractProperty();
    expect($test->appendElement($element))->toBe($element);
});

test('appendElement() with only a fully qualified class name', function()
{
    $test = new RecordProperty();
    expect(is_a($test->appendElement(NonAbstractProperty::class),NonAbstractProperty::class))->toBe(true);
});

test('appendElement() with only a property name', function()
{
    Properties::shouldReceive('getNamespaceOfProperty')->once()->with('test')->andReturn(NonAbstractProperty::class);
    $test = new RecordProperty();
    expect(is_a($test->appendElement('test'),NonAbstractProperty::class))->toBe(true);
});

it('fails when passing a non property', function()
{
    $test = new RecordProperty();
    $element = new stdClass();
    $test->appendElement($element);    
})->throws(NotAPropertyException::class);

test('Passing a name works', function() 
{
    $test = new RecordProperty();
    $element = new NonAbstractProperty();
    expect($test->appendElement($element,'test')->getName())->toBe('test');
    
});

it('Fails when property name was already given', function()
{
    $test = new RecordProperty();
    $element1 = new NonAbstractProperty();
    $element2 = new NonAbstractProperty();
    $test->appendElement($element1,'test');
    $test->appendElement($element2,'test');
})->throws(PropertyNameAlreadyGivenException::class);
    
it('Fails when property was already appended', function()
{
    $test = new RecordProperty();
    $element1 = new NonAbstractProperty();
    $test->appendElement($element1,'test1');
    $test->appendElement($element1,'test2');
})->throws(PropertyAlreadyInListException::class);


it('Fails when property has no name', function()
{
    $test = new RecordProperty();
    $element1 = new NonAbstractProperty();
    setProtectedProperty($element1, '_name', null);
    $test->appendElement($element1);
})->throws(PropertyHasNoNameException::class);

it('Fails when calling with invalid inclusion', function()
{
    $test = new RecordProperty();
    $element1 = new NonAbstractProperty();
    $test->appendElement($element1,'test','something');
})->throws(InvalidInclusionException::class);

// **************** Exploring members **********************
test('hasElement()', function()
{
    $test = new RecordProperty();
    $element = new NonAbstractProperty();
    $test->appendElement($element,'test');
    
    expect($test->hasElement('test'))->toBe(true);
    expect($test->hasElement('notexisting'))->toBe(false);
});

test('elementCount()', function()
{
    $test = new RecordProperty();
    $element1 = new NonAbstractProperty();
    $element2 = new NonAbstractProperty();
    
    expect($test->elementCount())->toBe(0);
    $test->appendElement($element1,'test1');
    expect($test->elementCount())->toBe(1);
    $test->appendElement($element2,'test2');
    expect($test->elementCount())->toBe(2);
    expect(count($test))->toBe(2);
});    

test('traversing elements', function()
{
    $test = new RecordProperty();
    $element1 = new NonAbstractProperty();
    $element2 = new NonAbstractProperty();
    
    $test->appendElement($element1,'test1');
    $test->appendElement($element2,'test2');

    $result = '';
    foreach ($test as $key => $value) {
        $result .= $key.'='.$value->getAccessType();
    }
    expect($result)->toBe('test1=integertest2=integer');    
});

// *************** Back to appendElement() ****************************
test('including a record included the elements', function()
{
    $container = new RecordProperty();
    $element1 = new NonAbstractProperty();
    $element2 = new NonAbstractProperty();
    $container->appendElement($element1,'test1')->getName();
    $container->appendElement($element2,'test2')->getName();
    
    $subrecord = new RecordProperty();
    $element3 = new NonAbstractProperty();
    $element4 = new NonAbstractProperty();
    $subrecord->appendElement($element3,'test3')->getName();
    $subrecord->appendElement($element4,'test4')->getName();

    $container->appendElement($subrecord,'','include');
    $result = '';
    foreach ($container as $key => $value) {
        $result .= $key;
    }
    expect($result)->toBe('test1test2test3test4');    
});

test('embedding a record included the elements', function()
{
    $container = new ChildRecordProperty();
    $element1 = new NonAbstractProperty();
    $element2 = new NonAbstractProperty();
    $container->appendElement($element1,'test1')->getName();
    $container->appendElement($element2,'test2')->getName();
    
    $subrecord = new ParentRecordProperty();
    $element3 = new NonAbstractProperty();
    $element4 = new NonAbstractProperty();
    $subrecord->appendElement($element3,'test3')->getName();
    $subrecord->appendElement($element4,'test4')->getName();
    
    $container->appendElement($subrecord,'','embed');
    $result = '';
    foreach ($container as $key => $value) {
        $result .= $key;
    }
    expect($result)->toBe('test1test2test3test4');
});

it('fails embedding a record when not an ancestor', function()
{
    $container = new RecordProperty();
    $element1 = new NonAbstractProperty();
    $element2 = new NonAbstractProperty();
    $container->appendElement($element1,'test1')->getName();
    $container->appendElement($element2,'test2')->getName();
    
    $subrecord = new RecordProperty();
    $element3 = new NonAbstractProperty();
    $element4 = new NonAbstractProperty();
    $subrecord->appendElement($element3,'test3')->getName();
    $subrecord->appendElement($element4,'test4')->getName();
    
    $container->appendElement($subrecord,'','embed');
})->throws(NotAllowedInclusionException::class);

it('fails including a record when same element names', function()
{
    $container = new RecordProperty();
    $element1 = new NonAbstractProperty();
    $element2 = new NonAbstractProperty();
    $container->appendElement($element1,'test1')->getName();
    $container->appendElement($element2,'test2')->getName();
    
    $subrecord = new RecordProperty();
    $element3 = new NonAbstractProperty();
    $element4 = new NonAbstractProperty();
    $subrecord->appendElement($element3,'test1')->getName();
    $subrecord->appendElement($element4,'test4')->getName();
    
    $container->appendElement($subrecord,'','include');
})->throws(PropertyNameAlreadyGivenException::class);

test('Reading a property', function()
{
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('getValue')->once()->with('test1')->andReturn(2);
    $container = new RecordProperty();
    $container->setStorage($storage);
    $element1 = new NonAbstractProperty();
    $element2 = new NonAbstractProperty();
    $container->appendElement($element1,'test1')->getName();
    $container->appendElement($element2,'test2')->getName();
    
    expect($container->test1)->toBe(2);
});

it('Fails when reading unknown property', function()
{
    $container = new RecordProperty();
    $element1 = new NonAbstractProperty();
    $element2 = new NonAbstractProperty();
    $container->appendElement($element1,'test1')->getName();
    $container->appendElement($element2,'test2')->getName();
    
    $container->nonexisting;    
})->throws(PropertyNotFoundException::class);

test('Reading an array property', function()
{
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('getIndexedValue')->once()->with('test1',1)->andReturn(2);
    $container = new RecordProperty();
    $container->setStorage($storage);
    $element1 = new ArrayProperty();
    $element2 = new NonAbstractProperty();
    $container->appendElement($element1,'test1')->getName();
    $container->appendElement($element2,'test2')->getName();
    
    expect($container->test1[1])->toBe(2);
});

it('Fails when reading unknown array property', function()
{
    $container = new RecordProperty();
    $element1 = new ArrayProperty();
    $element2 = new NonAbstractProperty();
    $container->appendElement($element1,'test1')->getName();
    $container->appendElement($element2,'test2')->getName();
    
    $container->nonexisting[1];
})->throws(PropertyNotFoundException::class);

test('Writing a property', function()
{
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setValue')->once()->with('test1',2);
    $container = new RecordProperty();
    $container->setStorage($storage);
    $element1 = new NonAbstractProperty();
    $element2 = new NonAbstractProperty();
    $container->appendElement($element1,'test1')->getName();
    $container->appendElement($element2,'test2')->getName();
    
    $container->test1 = 2;
});

it('Fails when writing unknown property', function()
{
    $container = new RecordProperty();
    $element1 = new NonAbstractProperty();
    $element2 = new NonAbstractProperty();
    $container->appendElement($element1,'test1')->getName();
    $container->appendElement($element2,'test2')->getName();
    
    $container->nonexisting = 2;
})->throws(PropertyNotFoundException::class);

test('Writing an array property', function()
{
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setIndexedValue')->once()->with('test1',1,2);
    $container = new RecordProperty();
    $container->setStorage($storage);
    $element1 = new ArrayProperty();
    $element2 = new NonAbstractProperty();
    $container->appendElement($element1,'test1')->getName();
    $container->appendElement($element2,'test2')->getName();
    
    $container->test1[1] = 2;
});

it('Fails when writing unknown array property', function()
{
    $container = new RecordProperty();
    $element1 = new NonAbstractProperty();
    $element2 = new NonAbstractProperty();
    $container->appendElement($element1,'test1')->getName();
    $container->appendElement($element2,'test2')->getName();
    
    $container->nonexisting[1] = 2;
})->throws(PropertyNotFoundException::class);

test('Appending an array property', function()
{
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setIndexedValue')->once()->with('test1',null,2);
    $container = new RecordProperty();
    $container->setStorage($storage);
    $element1 = new ArrayProperty();
    $element2 = new NonAbstractProperty();
    $container->appendElement($element1,'test1')->getName();
    $container->appendElement($element2,'test2')->getName();
    
    $container->test1[] = 2;
});

it('Fails when appending unknown array property', function()
{
    $container = new RecordProperty();
    $element1 = new NonAbstractProperty();
    $element2 = new NonAbstractProperty();
    $container->appendElement($element1,'test1')->getName();
    $container->appendElement($element2,'test2')->getName();
    
    $container->nonexisting[] = 2;
})->throws(PropertyNotFoundException::class);
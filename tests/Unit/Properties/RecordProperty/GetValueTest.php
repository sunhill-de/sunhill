<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Properties\Exceptions\PropertyDoesntExistException;
use Sunhill\Properties\Properties\AbstractSimpleProperty;
use Sunhill\Properties\Properties\AbstractRecordProperty;
use Sunhill\Properties\Properties\RecordProperty;

class GetValueProperty extends AbstractSimpleProperty {

    public $value = 5;
    
    function isValid($value) : bool
    {
        return true;
    }
    
    function getAccessType() : string
    {
        return 'integer';
    }
    
    function getValue()
    {
        return $this->value;
    }
    
    function setValue($value)
    {
        $this->value = $value;
    }
}

class GetValueRecordProperty extends RecordProperty
{
    function isValid($test) : bool
    {
        return false;
    }

    function initializeElements()
    {
        $element1 = new GetValueProperty();
        $element1->setName('elementA');
        $element1->setOwner($this);
        
        $element2 = new GetValueProperty();
        $element2->setName('elementB');
        $element2->setOwner($this);
        
        $this->elements['elementA'] = $element1;
        $this->elements['elementB'] = $element2;
    }
}

test('get value', function () {
    $test = new GetValueRecordProperty();

    expect($test->elementA)->toEqual(5);
});
test('set value', function () {
    $test = new GetValueRecordProperty();
    $test->elementA = 55;

    expect($test->elementA)->toEqual(55);
    expect($test->elementB)->toEqual(5);
});
test('get unkown value', function () {
    $test = new GetValueRecordProperty();
    $this->expectException(PropertyDoesntExistException::class);

    $a =  $test->elementZ;
});
test('set unkown value', function () {
    $test = new GetValueRecordProperty();
    $this->expectException(PropertyDoesntExistException::class);

    $test->elementZ = 10;
});
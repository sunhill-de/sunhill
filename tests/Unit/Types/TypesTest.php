<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Types\TypeVarchar;
use Sunhill\Properties\Properties\Exceptions\InvalidValueException;
use Sunhill\Properties\Types\TypeInteger;
use Sunhill\Properties\Types\TypeFloat;
use Sunhill\Properties\Types\TypeBoolean;
use Sunhill\Properties\Types\TypeDateTime;
use Sunhill\Properties\Types\TypeDate;
use Sunhill\Properties\Types\TypeTime;
use Sunhill\Properties\Types\TypeText;
use Sunhill\Properties\Types\TypeEnum;
use Sunhill\Properties\Tests\TestSupport\Storages\TestAbstractStorage;
use Sunhill\Properties\Types\TypeCalculated;
use Sunhill\Properties\Storage\AbstractStorage;
function getTestType($type, $setters)
{
    $test = new $type();
    foreach ($setters as $name => $value) {
        $method = 'set'.$name;
        $test->$method($value);
    }

    return $test;
}
test('validate type', function ($type, $setters, $test_input, $expect) {
    $test = getTestType($type, $setters);

    if (is_callable($test_input)) {
        expect($test->isValid($test_input()))->toEqual($expect);            
    } else {
        expect($test->isValid($test_input))->toEqual($expect);            
    }
})->with('validateProvider');
dataset('validateProvider', function () {
    return [
        [TypeBoolean::class, [], 'Y', true],
        [TypeBoolean::class, [], 'N', true],
        [TypeBoolean::class, [], '+', true],
        [TypeBoolean::class, [], '-', true],
        [TypeBoolean::class, [], 'true', true],
        [TypeBoolean::class, [], 'false', true],
        [TypeBoolean::class, [], true, true],
        [TypeBoolean::class, [], true, true],
        [TypeBoolean::class, [], 'ABC', true],
        [TypeBoolean::class, [], 1, true],
        [TypeBoolean::class, [], 0, true],
        [TypeBoolean::class, [], 10, true],
            
        [TypeDatetime::class, [], '2018-02-01 11:11:11', true],
        [TypeDatetime::class, [], '2018-02-32 11:11:11', false],
        [TypeDatetime::class, [], '01.02.2018 11:11:11', true],
        [TypeDateTime::class, [], 1686778521, true],
        [TypeDateTime::class, [], "1686778521", true],
        [TypeDateTime::class, [], "@1686778521", true],
        [TypeDateTime::class, [], 'ABC', false],
        [TypeDateTime::class, [], 1686778521.123, true],            
                    
        [TypeDate::class, [], '01.02.2018', true],
        [TypeDate::class, [], '2018-02-02', true],
        [TypeDate::class, [], '1.2.2018', true],
        [TypeDate::class, [], '2018-2-1', true],
        [TypeDate::class, [], 1686778521.3, true],
        [TypeDate::class, [], '2018-2', true],
        [TypeDate::class, [], '2.3.', true],
        [TypeDate::class, [], '2018', true],
        [TypeDate::class, [], false, false],
        [TypeDate::class, [], 'ABC', false],
        [TypeDate::class, [], '', false],
        [TypeDate::class, [], 1686778521, true],
        
        [TypeEnum::class, ['EnumValues'=>['TestA','TestB']], 'TestA', true],
        [TypeEnum::class, ['EnumValues'=>['TestA','TestB']], 'NonExisting', false],
        
        [TypeFloat::class, [], 1, true],
        [TypeFloat::class, [], 1.1, true],
        [TypeFloat::class, [], "1", true],
        [TypeFloat::class, [], "1.1", true],
        [TypeFloat::class, [], "A", false],
        [TypeFloat::class, [], "1.1.1", false],
        [TypeFloat::class,['Minimum'=>5,'Maximum'=>10],6,true],
        [TypeFloat::class,['Minimum'=>5,'Maximum'=>10],5,true],
        [TypeFloat::class,['Minimum'=>5,'Maximum'=>10],10,true],
        [TypeFloat::class,['Minimum'=>5,'Maximum'=>10],11,false],
        [TypeFloat::class,['Minimum'=>5,'Maximum'=>10],1,false],            
        [TypeFloat::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],6,true],
        [TypeFloat::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],5,true],
        [TypeFloat::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],10,true],
        [TypeFloat::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],11,true],
        [TypeFloat::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],1,true],
        
        [TypeInteger::class,[],1,true],
        [TypeInteger::class,[],"1",true],
        [TypeInteger::class,[],'A',false],
        [TypeInteger::class,[],1.1,false],
        [TypeInteger::class,['Minimum'=>5,'Maximum'=>10],6,true],
        [TypeInteger::class,['Minimum'=>5,'Maximum'=>10],5,true],
        [TypeInteger::class,['Minimum'=>5,'Maximum'=>10],10,true],
        [TypeInteger::class,['Minimum'=>5,'Maximum'=>10],11,false],
        [TypeInteger::class,['Minimum'=>5,'Maximum'=>10],1,false],
        [TypeInteger::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],6,true],
        [TypeInteger::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],5,true],
        [TypeInteger::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],10,true],
        [TypeInteger::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],11,true],
        [TypeInteger::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],1,true],
        
        [TypeText::class, [], 'Lorem ipsum', true],
        [TypeText::class, [], function() { return new \StdClass(); }, false],
        
        [TypeTime::class, [], '11:11:11', true],
        [TypeTime::class, [], '11:11', true],
        [TypeTime::class, [], '1:1', true],
        
        [TypeVarchar::class,[],'Teststring',true],
        [TypeVarchar::class,['MaxLen'=>2],'Teststring',true],
        [TypeVarchar::class,['MaxLen'=>2,'LengthExceedPolicy'=>'invalid'],'Teststring',false],
        [TypeVarchar::class, [], function() { return new \StdClass(); }, false],
        
    ];
});
test('read storage', function ($type, $setters, $test_value, $expect) {
    $test = getTestType($type, $setters);
    $test->setName('test');

    $storage = Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('getIsReadable')->with('test')->andReturn(true);
    $storage->shouldReceive('getReadCapability')->with('test')->andReturn(null);
    $storage->shouldReceive('getIsInitialized')->with('test')->andReturn(true);
    $storage->shouldReceive('getValue')->with('test')->andReturn($test_value);
    
    $test->setStorage($storage);

    if (is_callable($expect)) {
        expect($expect($test->getValue()))->toBeTrue();
    } else {
        expect($test->getValue())->toEqual($expect);
    }
})->with('readStorageProvider')->group('read');
dataset('readStorageProvider', function () {
    return [
        [TypeBoolean::class, [], 1, 1],
        [TypeBoolean::class, [], 0, 0],
        
        [TypeDate::class, [], '2023-12-24', function($date) { return $date->format('Y-m-d') == '2023-12-24'; }],

        [TypeDateTime::class, [], '2023-12-24 12:13:14', function($date) { return $date->format('Y-m-d H:i:s') == '2023-12-24 12:13:14'; }],
        
        [TypeEnum::class, ['EnumValues'=>['TestA','TestB']], 'TestA', 'TestA' ],
        
        [TypeFloat::class, [], 3.14, 3.14],            
        [TypeFloat::class, ['Precision'=>1], 3.14, 3.14],
        
        [TypeInteger::class, [], 3, 3],
        
        [TypeText::class, [], 'Lorem ipsum','Lorem ipsum'],
        
        [TypeTime::class, [], '11:12:13', function($date) { return $date->format('H:i:s') == '11:12:13'; }],

        [TypeVarchar::class, [], 'Lorem ipsum', 'Lorem ipsum'],
    ];
});
test('read storage human', function ($type, $setters, $test_value, $expect) {
    $test = getTestType($type, $setters);
    $test->setName('test');

    $storage = Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('getIsReadable')->with('test')->andReturn(true);
    $storage->shouldReceive('getReadCapability')->with('test')->andReturn(null);
    $storage->shouldReceive('getIsInitialized')->with('test')->andReturn(true);
    $storage->shouldReceive('getValue')->with('test')->andReturn($test_value);
    $test->setStorage($storage);

    if (is_callable($expect)) {
        expect($expect($test->getHumanValue()))->toBeTrue();
    } else {
        expect($test->getHumanValue())->toEqual($expect);
    }
})->with('readStorageHumanProvider')->group('readHuman');
dataset('readStorageHumanProvider', function () {
    return [
        [TypeBoolean::class, [], 1, 'true'],
        [TypeBoolean::class, [], 0, 'false'],

        [TypeDate::class, [], '2023-01-02', '2.1.2023'],
        [TypeDate::class, [], '2023-01-02', '2.1.2023'],
        
        [TypeDateTime::class, [], '2023-01-02 11:12:13', '2.1.2023 11:12:13'],
        
        [TypeEnum::class, ['EnumValues'=>['TestA','TestB']], 'TestA', 'TestA' ],

        [TypeFloat::class, [], 3.14, 3.14],
        [TypeFloat::class, ['Precision'=>1], 3.14, 3.1],
        [TypeFloat::class, ['Precision'=>1], 3.15, 3.2],

        [TypeInteger::class, [], 3, 3],
        
        [TypeText::class, [], 'Lorem ipsum','Lorem ipsum'],

        [TypeTime::class, [], '11:12:13', '11:12:13'],

        [TypeVarchar::class, [], 'Lorem ipsum', 'Lorem ipsum'],
        
    ];
});

test('write storage succeeds', function ($type, $setters, $test_input, $expect, $expect_mod = null) {
    $test = getTestType($type, $setters);
    $test->setName('test');

    $storage = Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('getIsWriteable')->with('test')->andReturn(true);
    $storage->shouldReceive('getModifyCapability')->with('test')->andReturn(null);
    $storage->shouldReceive('getIsInitialized')->with('test')->andReturn(true);
    $storage->shouldReceive('setValue')->with('test',$expect)->once();
    $test->setStorage($storage);

    if (is_callable($test_input)) {
        $test->setValue($test_input());
    } else {
        $test->setValue($test_input);
    }
})->with('writeStorageProvider')->group('write');
dataset('writeStorageProvider', function () {
    return [
        'Boolean with "Y"'=>[TypeBoolean::class, [], 'Y', 1],
        [TypeBoolean::class, [], 'N', 0],
        [TypeBoolean::class, [], '+', 1],
        [TypeBoolean::class, [], '-', 0],
        [TypeBoolean::class, [], 'true', 1],
        [TypeBoolean::class, [], 'false', 0],
        [TypeBoolean::class, [], true, 1],
        [TypeBoolean::class, [], false, 0],
        [TypeBoolean::class, [], 'ABC', 0],
        [TypeBoolean::class, [], 1, 1],
        [TypeBoolean::class, [], 0, 0],
        [TypeBoolean::class, [], 10, 1],
        
        [TypeDate::class, [], '01.02.2018', '2018-02-01'],
        [TypeDate::class, [], '2018-02-02', '2018-02-02'],
        [TypeDate::class, [], '1.2.2018', '2018-02-01'],
        [TypeDate::class, [], '2018-2-1', '2018-02-01'],
        [TypeDate::class, [], 1686778521.3, '2023-06-14'],
        [TypeDate::class, [], '2018-2', '2018-02-01'],
        [TypeDate::class, [], 1686778521, '2023-06-14'],
       
        [TypeDatetime::class, [], '2018-02-01 11:11:11', '2018-02-01 11:11:11'],
        [TypeDatetime::class, [], '1.2.2018 11:11:11', '2018-02-01 11:11:11'],
        [TypeDateTime::class, [], 1686778521, '2023-06-14 21:35:21'],
        
        [TypeEnum::class, ['EnumValues'=>['TestA','TestB']], 'TestA', 'TestA'],
        
        [TypeFloat::class, [], 1, 1.0],
        [TypeFloat::class, [], 1.1, 1.1],
        [TypeFloat::class, [], "1", 1],
        [TypeFloat::class, [], "1.1", 1.1],
        
        [TypeInteger::class, [], 1, 1],
        [TypeInteger::class, [], '1', 1],
       
        [TypeText::class, [], 'Lorem ipsum', 'Lorem ipsum'],
        
        [TypeTime::class, [], '11:11:11', '11:11:11'],
        [TypeTime::class, [], '11:11', '11:11:00'],
        [TypeTime::class, [], '1:1', '01:01:00'],
        
        [TypeVarchar::class,[],'Teststring','Teststring'],
        [TypeVarchar::class,['MaxLen'=>2],'Teststring','Te'],            
    ];
});

    it('fails when writing to storage', function ($type, $setters, $test_input) {
        $test = getTestType($type, $setters);
        $test->setName('test');
        
        $storage = Mockery::mock(AbstractStorage::class);
        $storage->shouldReceive('getIsWriteable')->with('test')->andReturn(true);
        $storage->shouldReceive('getModifyCapability')->with('test')->andReturn(null);
        $storage->shouldReceive('getIsInitialized')->with('test')->andReturn(true);
        $test->setStorage($storage);
        
        if (is_callable($test_input)) {
            $test->setValue($test_input());
        } else {
            $test->setValue($test_input);
        }
    })->with('writeStorageFailsProvider')->group('write')->throws(InvalidValueException::class);
    dataset('writeStorageFailsProvider', function () {
        return [
            'A boolean as date'=>[TypeDate::class, [], false],
            'A string as date'=>[TypeDate::class, [], 'ABC'],
            'An empty string as date'=>[TypeDate::class, [], ''],
            'A string as float'=>[TypeFloat::class, [], "A"],
            'A invalid float as float'=>[TypeFloat::class, [], "1.1.1"],
            'A float as integer'=>[TypeInteger::class, [], 1.1],
            'A string as integer'=>[TypeInteger::class, [], 'A'],
            'A class as text'=>[TypeText::class, [], function() { return new \StdClass(); }],
            'A too long string for varchar'=>[TypeVarchar::class,['MaxLen'=>2,'LengthExceedPolicy'=>'invalid'],'Teststring']
            ];
    });
    
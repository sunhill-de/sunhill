<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Semantic\Name;
use Sunhill\Properties\Properties\Property;
use Sunhill\Properties\Properties\Exceptions\PropertyException;
use Sunhill\Properties\Units\None;
use Sunhill\Properties\Objects\ORMObject;
use Sunhill\Properties\Properties\Exceptions\InvalidNameException;
use Sunhill\Properties\Properties\AbstractProperty;
use Sunhill\Properties\Properties\ValidatorBase;
use Sunhill\Properties\Properties\Exceptions\InvalidValueException;
use Sunhill\Properties\Facades\Properties;
function getUnits()
{
    return include(dirname(__FILE__).'/../../../src/Units.php');
}
function calculate($item, $direction, $value)
{
    $field = 'calculate'.$direction.'Basic';
    return Properties::$field($item, $value);
}
test('unit', function ($name, $base, $unit, $base_unit) {
    expect(round(calculate($name, 'To', $unit),2))->toEqual($base_unit);
    expect(round(calculate($name, 'From', $base_unit),2))->toEqual($unit);
})->with('unitProvider');
dataset('unitProvider', function () {
    return [
        ['meter','meter',1,1],
        ['centimeter','meter',100,1],
        ['kilometer','meter',1,1000],
        ['millimeter','meter',1000,1],
        ['kilogramm','kilogramm',1,1],
        ['gramm','kilogramm',1000,1],
        ['degreecelsius','degreecelsius',1,1],
        ['degreekelvin','degreecelsius',300,26.85],
        ['degreefahrenheit','degreecelsius',99,37.22],
        ['meterpersecond','meterpersecond',1,1],
        ['kilometerperhour','meterpersecond',100.01,27.78]
    ];
});
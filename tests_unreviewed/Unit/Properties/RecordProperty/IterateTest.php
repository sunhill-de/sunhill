<?php

uses(\Sunhill\Tests\TestCase::class);
use Sunhill\Tests\TestSupport\Properties\NonAbstractRecordProperty;

test('iterate', function () {
    $test = new NonAbstractRecordProperty();

    $key_str = '';
    $value_str = '';

    foreach ($test as $key => $value) {
        $key_str .= $key;
        $value_str .= $value->getName();
    }

    expect($key_str)->toEqual('elementAelementB');
    expect($value_str)->toEqual('elementAelementB');
});
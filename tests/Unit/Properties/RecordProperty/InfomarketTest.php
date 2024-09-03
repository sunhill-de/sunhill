<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Tests\TestSupport\Properties\NonAbstractRecordProperty;


test('iterate', function () {
    $test = new NonAbstractRecordProperty();

    $element = $test->requestItem(['elementB']);

    expect($element->getName())->toEqual('elementB');
});

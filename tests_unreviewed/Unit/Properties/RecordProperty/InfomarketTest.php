<?php

uses(\Sunhill\Tests\TestCase::class);
use Sunhill\Tests\TestSupport\Properties\NonAbstractRecordProperty;


test('iterate', function () {
    $test = new NonAbstractRecordProperty();

    $element = $test->requestItem(['elementB']);

    expect($element->getName())->toEqual('elementB');
});

<?php

uses(\Sunhill\Tests\TestCase::class);
use Sunhill\Tests\TestSupport\Properties\NonAbstractProperty;

test('get metadata', function () {
    $test = new NonAbstractProperty();

    $metadata = $test->getMetadata();

    expect($metadata['update'])->toEqual('ASAP');
    expect($metadata['unit'])->toEqual('none');
    expect($metadata['semantic'])->toEqual('none');
});
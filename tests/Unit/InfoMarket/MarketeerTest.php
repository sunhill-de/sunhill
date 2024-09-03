<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Tests\TestSupport\Marketeers\TestMarketeer1;
use Sunhill\Properties\Tests\TestSupport\Marketeers\TestMarketeer2;


test('simple read', function () {
    $test = new TestMarketeer1();

    $result = $test->requestItem(['element1']);

    expect($result->getValue())->toEqual('ValueA');
});

test('read unknown', function () {
    $test = new TestMarketeer1();

    $result = $test->requestItem(['unknown']);

    expect($result)->toBeNull();
});

test('get offer', function () {
    $test = new TestMarketeer1();

    $result = $test->getElementNames();

    expect($result[0])->toEqual('element1');
});

test('nested call', function () {
    $test = new TestMarketeer2();

    $result = $test->requestItem(['key3','element2']);

    expect($result->getValue())->toEqual('valueB');
});

test('nested unknown call', function () {
    $test = new TestMarketeer2();

    $result = $test->requestItem(['key3','unknown']);

    expect($result)->toBeNull();
});

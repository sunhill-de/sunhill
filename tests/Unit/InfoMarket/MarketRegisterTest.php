<?php

uses(\Sunhill\Tests\TestCase::class);
use Sunhill\InfoMarket\Market;
use Sunhill\InfoMarket\Exceptions\MarketeerHasNoNameException;
use Sunhill\InfoMarket\Exceptions\CantProcessMarketeerException;
use Sunhill\Tests\TestSupport\Marketeers\TestMarketeer1;
use Sunhill\Tests\TestSupport\Marketeers\TestMarketeer2;


test('register marketeer', function () {
    $test = new Market();
    expect($test->hasMarketeer('marketeer1'))->toBeFalse();
    $test->registerMarketeer(new TestMarketeer1());
    expect($test->hasMarketeer('marketeer1'))->toBeTrue();
});

test('register marketeer as classname', function () {
    $test = new Market();
    $test->registerMarketeer(TestMarketeer1::class);
    expect($test->hasMarketeer('marketeer1'))->toBeTrue();
});

test('register marketeer with name', function () {
    $test = new Market();
    $test->registerMarketeer(TestMarketeer2::class,'marketeer2');
    expect($test->hasMarketeer('marketeer2'))->toBeTrue();
});

test('register marketeer with no marketeer', function () {
    $this->expectException(CantProcessMarketeerException::class);

    $test = new Market();
    $test->registerMarketeer('noclass','marketeer2');
});

test('register marketeer with no name', function () {
    $this->expectException(MarketeerHasNoNameException::class);

    $test = new Market();
    $test->registerMarketeer(TestMarketeer2::class);
});

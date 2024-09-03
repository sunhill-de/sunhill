<?php

use Sunhill\Properties\Tests\Feature\Properties\SampleCallbackProperty;
use Sunhill\Properties\Tests\Feature\Marketeers\StaticMarketeer;
use Sunhill\Properties\Facades\InfoMarket;

uses(\Sunhill\Properties\Tests\TestCase::class);

test('Access static marketeer via market', function() {
   InfoMarket::registerMarketeer(StaticMarketeer::class);
   expect(InfoMarket::requestValue('static.string_element'))->toBe('ABCD');
});

test('Access callback marketeer via market', function() {
   InfoMarket::registerMarketeer(SampleCallbackProperty::class, 'callback');
   expect(InfoMarket::requestValue('callback.sample_string'))->toBe('ABC');
});

test('Write to callback marketeer via market', function() {
   InfoMarket::registerMarketeer(SampleCallbackProperty::class, 'callback');
   InfoMarket::putValue('callback.sample_integer', 123);
   expect(InfoMarket::requestValue('callback.sample_integer'))->toBe(123);
});
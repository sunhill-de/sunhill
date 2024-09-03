<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\InfoMarket\Exceptions\PathNotFoundException;
use Sunhill\Properties\InfoMarket\Exceptions\CantProcessMarketeerException;
use Sunhill\Properties\InfoMarket\Market;
use Sunhill\Properties\Tests\TestSupport\Marketeers\TestMarketeer1;

uses(\Sunhill\Properties\Tests\TestSupport\Markets\GetMarket::class);

it('fails with invalid arguments for registerMarketeer', function($marketeer) {
    $test = new Market();
    if (is_callable($marketeer)) {
        $marketeer = $marketeer();
    }
    $test->registerMarketeer($marketeer, 'test');
})->with([
    12,'ABC',function() { return new \StdClass(); }
])->throws(CantProcessMarketeerException::class);

it('passes with right arguments for registerMarketeer', function($marketeer) {
    $test = new Market();
    if (is_callable($marketeer)) {
        $marketeer = $marketeer();
    }
    $test->registerMarketeer($marketeer, 'test');
    expect(true)->toBeTrue();
})->with([TestMarketeer1::class, function() { return new TestMarketeer1(); }]);

test('path exists', function () {
    $test = $this->getMarket();

    expect($test->pathExists('marketeer1.element1'))->toBeTrue();
    expect($test->pathExists('marketeer2.key3.element2'))->toBeTrue();
    expect($test->pathExists('marketeer1.nonexisting'))->toBeFalse();
    expect($test->pathExists('nonexisting.nonexisting'))->toBeFalse();
    expect($test->pathExists('marketeer2.key3.nonexisting'))->toBeFalse();
});

test('simple request value', function () {
    $test = $this->getMarket();

    expect($test->requestValue('marketeer1.element1'))->toEqual('ValueA');
});

test('simple request value as json', function () {
    $test = $this->getMarket();

    expect($test->requestValue('marketeer1.element1','json'))->toEqual('"ValueA"');
});

test('complex request value', function () {
    $test = $this->getMarket();

    expect($test->requestValue('marketeer2.key3.element2'))->toEqual('valueB');
});

test('request values', function () {
    $test = $this->getMarket();

    $values = $test->requestValues(['marketeer1.element1','marketeer2.key3.element1']);

    expect($values['marketeer1.element1'])->toEqual('ValueA');
    expect($values['marketeer2.key3.element1'])->toEqual('ValueA');
});

test('request values as json', function () {
    $test = $this->getMarket();

    $values = $test->requestValues(['marketeer1.element1','marketeer2.key3.element1'],'json');

    expect($values)->toEqual('{"marketeer1.element1":"ValueA","marketeer2.key3.element1":"ValueA"}');
});

test('request unknown value', function () {
    $test = $this->getMarket();

    $this->expectException(PathNotFoundException::class);
    $test->requestValue('marketeer1.unknown');
});

test('request unknown values', function () {
    $test = $this->getMarket();
    $this->expectException(PathNotFoundException::class);
    $values = $test->requestValues(['marketeer1.element1','marketeer2.unknown.element1']);
});

test('request metadata as array', function () {
    $test = $this->getMarket();

    $metadata = $test->requestMetadata('marketeer1.element2','array');

    expect($metadata['type'])->toEqual('string');
});

test('request metadata as stdclass', function () {
    $test = $this->getMarket();

    $metadata = $test->requestMetadata('marketeer1.element2');

    expect($metadata->type)->toEqual('string');
});

test('request metadata as json', function () {
    $test = $this->getMarket();

    $metadata = $test->requestMetadata('marketeer1.element2','json');

    expect(strpos($metadata, '"type":"string"') > 0)->toBeTrue();
});

test('request metadatas as array', function () {
    $test = $this->getMarket();

    $metadatas = $test->requestMetadatas(['marketeer1.element1','marketeer2.key3.element1'], 'array');

    expect($metadatas['marketeer1.element1']['type'])->toEqual('string');
});

test('request metadatas as stdclass', function () {
    $test = $this->getMarket();

    $metadatas = $test->requestMetadatas(['marketeer1.element1','marketeer2.key3.element1'], 'stdclass');

    expect($metadatas->{"marketeer1.element1"}->type)->toEqual('string');
});

test('request metadatas as json', function () {
    $test = $this->getMarket();

    $metadatas = $test->requestMetadatas(['marketeer1.element1','marketeer2.key3.element1'], 'json');

    expect(strpos($metadatas, '"type":"string"') > 0)->toBeTrue();
});

test('request data as std class', function () {
    $test = $this->getMarket();

    $metadata = $test->requestData('marketeer1.element2','stdclass');

    expect($metadata->value)->toEqual('valueB');
});

test('request data as array', function () {
    $test = $this->getMarket();

    $metadata = $test->requestData('marketeer1.element2','array');

    expect($metadata['value'])->toEqual('valueB');
});

test('request data as json', function () {
    $test = $this->getMarket();

    $metadata = $test->requestData('marketeer1.element2','json');

    expect(strpos($metadata, '"value":"valueB"') > 0)->toBeTrue();
});

test('request datas as array', function () {
    $test = $this->getMarket();

    $metadatas = $test->requestDatas(['marketeer1.element1','marketeer2.key3.element1'], 'array');

    expect($metadatas['marketeer1.element1']['human_value'])->toEqual('ValueA');
    expect($metadatas['marketeer1.element1']['value'])->toEqual('ValueA');
    expect($metadatas['marketeer1.element1']['type'])->toEqual('string');
});

test('request datas as stdclass', function () {
    $test = $this->getMarket();

    $metadatas = $test->requestDatas(['marketeer1.element1','marketeer2.key3.element1'], 'stdclass');

    expect($metadatas->{"marketeer1.element1"}->human_value)->toEqual('ValueA');
    expect($metadatas->{"marketeer1.element1"}->value)->toEqual('ValueA');
    expect($metadatas->{"marketeer1.element1"}->type)->toEqual('string');
});

test('request datas as json', function () {
    $test = $this->getMarket();

    $metadatas = $test->requestDatas(['marketeer1.element1','marketeer2.key3.element1'], 'json');

    expect(strpos($metadatas, '"human_value":"ValueA"') > 0)->toBeTrue();
    expect(strpos($metadatas, '"value":"ValueA"') > 0)->toBeTrue();
    expect(strpos($metadatas, '"type":"string"') > 0)->toBeTrue();
});

test('put value', function () {
    $test = $this->getMarket();

    $test->putValue('marketeer3.stringkey', 'newvalue');

    expect($test->requestValue('marketeer3.stringkey'))->toEqual('newvalue');
});

test('put values', function () {
    $test = $this->getMarket();

    $test->putValues(['marketeer3.stringkey'=>'newvalue','marketeer3.intkey'=>123]);

    expect($test->requestValue('marketeer3.stringkey'))->toEqual('newvalue');
    expect($test->requestValue('marketeer3.intkey'))->toEqual(123);
});

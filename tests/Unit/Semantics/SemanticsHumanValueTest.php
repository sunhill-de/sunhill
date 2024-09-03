<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Types\TypeVarchar;
use Sunhill\Properties\Exceptions\InvalidValueException;
use Sunhill\Properties\Types\TypeInteger;
use Sunhill\Properties\Types\TypeFloat;
use Sunhill\Properties\Types\TypeBoolean;
use Sunhill\Properties\Types\TypeDateTime;
use Sunhill\Properties\Tests\ReadonlyDatabaseTestCase;
use Sunhill\Properties\Types\TypeDate;
use Sunhill\Properties\Types\TypeTime;
use Sunhill\Properties\Types\TypeText;
use Sunhill\Properties\Types\TypeEnum;
use Sunhill\Properties\Types\TypeCollection;
use Sunhill\Properties\Tests\Testobjects\DummyCollection;
use Sunhill\Properties\Tests\Testobjects\ComplexCollection;
use Sunhill\Properties\Tests\Testobjects\AnotherDummyCollection;

use Sunhill\Properties\Semantics\Duration;
use Sunhill\Properties\Semantics\Illuminance;
use Sunhill\Properties\Semantics\Speed;
use Sunhill\Properties\Semantics\IPv4Address;
use Sunhill\Properties\Semantics\MACAddress;
use Sunhill\Properties\Semantics\IPv6Address;
use Sunhill\Properties\Semantics\EMail;
use Sunhill\Properties\Semantics\Domain;
use Sunhill\Properties\Semantics\URL;
use Sunhill\Properties\Semantics\UUID4;
use Sunhill\Properties\Semantics\MD5;
use Sunhill\Properties\Semantics\SHA1;
use Sunhill\Properties\Semantics\Count;
use Sunhill\Properties\Semantics\Capacity;
use Sunhill\Properties\Semantics\Direction;
use Sunhill\Properties\Semantics\Age;
use Sunhill\Properties\Semantics\Airpressure;
use Sunhill\Properties\Semantics\Pressure;
use Sunhill\Properties\Semantics\Airtemperature;
use Sunhill\Properties\Semantics\Temperature;
use Sunhill\Properties\Semantics\Timestamp;

test('get human value', function ($type, $setters, $test_input, $expect, $expect_mod = null) {
    $test = new $type();
    
    if ($expect == 'except') {
        $this->expectException(InvalidValueException::class);
    }

    $format = callProtectedMethod($test, 'formatForHuman', [$test_input]);
    if (is_callable($expect_mod)) {
        expect($expect_mod($format))->toEqual($expect);
    } else {
        expect($format)->toEqual($expect);            
    }
})->with('convertProvider');
dataset('convertProvider', function () {
    return [
        [Age::class, [], 10, '10 s'],
        [Airpressure::class, [], 1024, '1024 hPa'],
        [Airtemperature::class, [], 10, '10 °C'],
        
        [Capacity::class, [], 1, '1 Byte'],
        [Capacity::class, [], 1001, '1 kB'],
        [Capacity::class, [], 1101, '1.1 kB'],
        [Capacity::class, [], 1000*1000, '1 MB'],
        [Capacity::class, [], 1100*1000, '1.1 MB'],
        [Capacity::class, [], 1000*1000*1000, '1 GB'],
        [Capacity::class, [], 1100*1000*1000, '1.1 GB'],
        [Capacity::class, [], 1000*1000*1000*1000, '1 TB'],
        [Capacity::class, [], 1100*1000*1000*1000, '1.1 TB'],
        
        [Direction::class, [], 10, 'N'],
        
        [Duration::class, [], 1, '1 seconds'],
        [Duration::class, [], 60, '1 minute 0 seconds'],
        [Duration::class, [], 61, '1 minute 1 second'],
        [Duration::class, [], 62, '1 minute 2 seconds'],
        [Duration::class, [], 121, '2 minutes 1 second'],
        [Duration::class, [], 122, '2 minutes 2 seconds'],
        [Duration::class, [], 3600, '1 hour 0 minutes'],
        [Duration::class, [], 3601, '1 hour 0 minutes'],
        [Duration::class, [], 3660, '1 hour 1 minute'],
        [Duration::class, [], 3720, '1 hour 2 minutes'],
        [Duration::class, [], 7200, '2 hours 0 minutes'],
        [Duration::class, [], 7260, '2 hours 1 minute'],
        [Duration::class, [], 7320, '2 hours 2 minutes'],
        [Duration::class, [], 86400, '1 day 0 hours'],
        [Duration::class, [], 90000, '1 day 1 hour'],
        [Duration::class, [], 93600, '1 day 2 hours'],
        [Duration::class, [], 172800, '2 days 0 hours'],
        [Duration::class, [], 176400, '2 days 1 hour'],
        [Duration::class, [], 180000, '2 days 2 hours'],
        [Duration::class, [], 31536000, '1 year 0 days'],
        [Duration::class, [], 31622400, '1 year 1 day'],
        [Duration::class, [], 31708800, '1 year 2 days'],
        [Duration::class, [], 63072000, '2 years 0 days'],
    ];
});
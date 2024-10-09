<?php

uses(\Sunhill\Tests\TestCase::class);
use Sunhill\Types\TypeVarchar;
use Sunhill\Exceptions\InvalidValueException;
use Sunhill\Types\TypeInteger;
use Sunhill\Types\TypeFloat;
use Sunhill\Types\TypeBoolean;
use Sunhill\Types\TypeDateTime;
use Sunhill\Tests\ReadonlyDatabaseTestCase;
use Sunhill\Types\TypeDate;
use Sunhill\Types\TypeTime;
use Sunhill\Types\TypeText;
use Sunhill\Types\TypeEnum;
use Sunhill\Types\TypeCollection;
use Sunhill\Tests\Testobjects\DummyCollection;
use Sunhill\Tests\Testobjects\ComplexCollection;
use Sunhill\Tests\Testobjects\AnotherDummyCollection;

use Sunhill\Semantics\Duration;
use Sunhill\Semantics\Illuminance;
use Sunhill\Semantics\Speed;
use Sunhill\Semantics\IPv4Address;
use Sunhill\Semantics\MACAddress;
use Sunhill\Semantics\IPv6Address;
use Sunhill\Semantics\EMail;
use Sunhill\Semantics\Domain;
use Sunhill\Semantics\URL;
use Sunhill\Semantics\UUID4;
use Sunhill\Semantics\MD5;
use Sunhill\Semantics\SHA1;
use Sunhill\Semantics\Count;
use Sunhill\Semantics\Capacity;
use Sunhill\Semantics\Direction;
use Sunhill\Semantics\Age;
use Sunhill\Semantics\Airpressure;
use Sunhill\Semantics\Pressure;
use Sunhill\Semantics\Airtemperature;
use Sunhill\Semantics\Temperature;
use Sunhill\Semantics\Timestamp;

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
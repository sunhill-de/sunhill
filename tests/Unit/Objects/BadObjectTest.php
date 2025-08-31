<?php

/**
 * @file BAdObjectTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: yes
 */

use Sunhill\Properties\Exceptions\PropertyNameAlreadyGivenException;
use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\BadChildDuplicateName;

uses(SunhillSimpleTestCase::class);

it('Fails when using duplicate property name', function () {
    $test = new BadChildDuplicateName;
})->throws(PropertyNameAlreadyGivenException::class);

<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tests\TestSupport\Objects\BadChildDuplicateName;
use Sunhill\Properties\Exceptions\PropertyNameAlreadyGivenException;

uses(SunhillTestCase::class);

it('Fails when using duplicate property name', function()
{
    $test = new BadChildDuplicateName(); 
})->throws(PropertyNameAlreadyGivenException::class);
<?php

namespace Sunhill\Tests\Unit\Basic;

use Sunhill\Basic\SunhillException;
use Sunhill\Basic\Base;
use Sunhill\Basic\Tests\SunhillOrchestraTestCase;

class extension extends Base {

    private $test=0;
    
    public function setTest($value) {
        $this->test = $value;
    }
    
    public function getTest() {
        return $this->test;
    }
}

test("GetterSetter works", function() 
{
    $test = new extension();
    $test->test = 2;
    expect($test->test)->toBe(2);
});

test("Excpetion is raised when writing non existing", function()
{
    $test = new extension();
    $test->notexisting = 2;    
})->throws(SunhillException::class);

test("Excpetion is raised when reading non existing", function()
{
    $test = new extension();
    $a = $test->notexisting;
})->throws(SunhillException::class);


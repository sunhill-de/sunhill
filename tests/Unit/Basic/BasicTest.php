<?php

namespace Sunhill\Tests\Unit\Basic;

use Sunhill\Basic\Base;
use Sunhill\Exceptions\SunhillException;

class extension extends Base 
{

    private $test=0;
    
    public function setTest($value) {
        $this->test = $value;
    }
    
    public function getTest() {
        return $this->test;
    }
    
    protected function ownMethod()
    {
        
    }
    
    protected static function ownStaticMethod()
    {
        
    }
    
}

class extension2 extends extension 
{
    
    protected function ownMethod()
    {
        
    }
    
    protected static function ownStaticMethod()
    {
        
    }
}

class extension3 extends extension 
{
    
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

test('definesOwnMethod() works', function() 
{
    expect(extension2::definesOwnMethod('ownMethod'))->toBe(true);    
    expect(extension3::definesOwnMethod('ownMethod'))->toBe(false);
});

test('definesOwnStaticMethod() works', function()
{
    expect(extension2::definesOwnMethod('ownStaticMethod'))->toBe(true);
    expect(extension3::definesOwnMethod('ownStaticMethod'))->toBe(false);
});


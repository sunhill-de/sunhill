<?php
/**
 * A helper fake check class for testing 
 */
namespace Sunhill\Tests\Unit\Checker;

use Sunhill\Checker\Checker;

class DummyChecker extends Checker
{
    
    public function checkPass(bool $repair)
    {
        $this->pass();
    }
    
    public function checkFailure(bool $repair)
    {
        $this->fail("FAILED");
    }
    
    public function checkRepair(bool $repair)
    {
        $this->repair("REPAIRED");
    }
    
    public function checkUnrepairable(bool $repair)
    {
        $this->unrepairable("UNREPAIRABLE");
    }
    
    public function dummyMethod()
    {
        
    }
}
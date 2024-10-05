<?php
/**
 * @file checker.php
 * A Checker is a single entity. It contains at least one checkXXXX method. Each of which is
 * called by the Checks manager.
 * Lang en
 * Reviewstatus: 2024-09-01
 * Localization: nothing to translate
 * Documentation: complete
 * Tests: Unit/Checker/CheckerTest.php
 * Coverage: unknown
 * PSR-State: complete
 */

namespace Sunhill\Checker;

use Sunhill\Basic\Base;

class Checker extends Base
{
    
    /** 
     * Stores the group this check belongs to. This is needed for grouping checks in the
     * check command
     * @var string
     */
    protected static string $group = '';
    
    /**
     * Stores the result of the last check 
     * @var string
     */
    protected string $last_result = '';
    
    /**
     * Stores the message of the last failure
     * @var string
     */
    protected string $last_message = '';
    
    /**
     * The check was passed
     * @test Unit/Checker/CheckerTest::testPass()
     */
    protected function pass()
    {
        $this->last_result = 'passed';
    }
    
    /**
     * The check failed with the given error
     * @param string $message
     * @test Unit/Checker/CheckerTest::testFailure()
     */
    protected function fail(string $message)
    {
        $this->last_result = 'failed';
        $this->last_message = $message;
        throw new CheckException("Check failed");
    }
    
    /**
     * The check failed with the given error but was repaired
     * @param string $message
     * @test Unit/Checker/CheckerTest::testRepair()
     */
    protected function repair(string $message)
    {
        $this->last_result = 'repaired';
        $this->last_message = $message;
        throw new CheckException("Check failed and repaired");
    }
    
    /**
     * The check failed and was not repairable
     * @param string $message
     * @test Unit/Checker/CheckerTest::testUnrepairable()
     */
    protected function unrepairable(string $message)
    {
        $this->last_result = 'unrepairable';
        $this->last_message = $message;
        throw new CheckException("Check failed was not repairable");        
    }
    
    /**
     * Getter for $last_result
     * @return string
     * @test Unit/Checker/CheckerTest::testFailure()
     * @test Unit/Checker/CheckerTest::testRepair()
     * @test Unit/Checker/CheckerTest::testUnrepairable()
     */
    public function getLastResult(): string
    {
        return $this->last_result;
    }
    
    /**
     * Getter for $last_message
     * @return string
     * @test Unit/Checker/CheckerTest::testFailure()
     * @test Unit/Checker/CheckerTest::testRepair()
     * @test Unit/Checker/CheckerTest::testUnrepairable()
     */
    public function getLastMessage(): string
    {
        return $this->last_message;    
    }
    
}

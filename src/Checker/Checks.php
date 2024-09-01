<?php
/**
 * @file Checks.php
 * Provides a class that performs checks
 * Lang en
 * Reviewstatus: 2024-09-01
 * Localization: incomplete
 * Documentation: complete
 * Tests: BasicTest.php
 * Coverage: unknown
 */
namespace Sunhill\Checker;

use Sunhill\Basic\Loggable;
use Sunhill\Checker\CheckException;
use Sunhill\Checker\Checker;

/**
 The class for the check performer. This class is called via the Checks facade which is normally called via an command line. 
 The checks itself are performed by a checker class. This checker class has to be installed via the InstallChecker method 
 first. The checks are performed by calling the Checks method 
 */
class Checks extends Loggable 
{
    
    /**
     * Stores the installed checker classes
     * @var array
     */
    protected $checker_classes = [];
   
    /**
     * Stores the number of tests that are avaiable 
     * @var integer
     */
    protected int $total_tests = 0;
    
    /**
     * Stores the number of tests totally performed by this instance
     * @var integer
     */
    protected int $tests_performed = 0;
    
    protected int $tests_passed = 0;
    
    protected int $tests_failed = 0;
    
    protected int $tests_repaired = 0;
    
    protected int $tests_unrepairable = 0;
    
    protected array $messages = [];
    
    /**
     * This method cleans all checks so that after it there is no check installed
     */
    public function purge(): void 
    {
        $this->checker_classes = [];
    }
    
    /**
     * Every package of the sunhill framework can install one ore more checker classes. Normally this is done in the Service Routine of laravel
     * @param $class_name The fully qualified class name of the checker class.
     */
    public function installChecker(string $class_name): void 
    {
        $this->checker_classes[] = $class_name;    
    }
    
    /**
     * Runs all checks in all installed checker classes 
     * @throws CheckException if check() is called with no installed checker_class
     * @returns array of string The check resuls in an array.
     * Test: testRunChecks
     */
    public function check(bool $repair = false, string $group = '', $callback = null): array 
    {
        if (empty($this->checker_classes)) {
            throw new CheckException("No checkers installed");
        }
        $this->initializeChecks();
        $checks = $this->collectChecks($group);
        $this->walkChecks($checks, $repair, $callback);
        return $this->messages;
    }

    /**
     * Return the total number of tests
     * @return int
     */
    public function getTotalTests(): int
    {
        return $this->total_tests;     
    }
    
    /**
     * Returns the total number of tests that where performed
     * @return int
     */
    public function getTestsPerformed(): int
    {
        return $this->tests_performed;    
    }

    /**
     * Returns the total number of tests that passed
     * @return int
     */
    public function getTestsPassed(): int
    {
        return $this->tests_passed;    
    }
    
    /**
     * Returns the total number of tests that failed. This number is increased every time
     * when a test failed without bothering if the failure was repaired or not
     * @return int
     */
    public function getTestsFailed(): int
    {
        return $this->tests_failed;    
    }
    
    public function getTestsRepaired(): int
    {
        return $this->tests_repaired;        
    }
    
    public function getTestsUnrepairable(): int
    {
        return $this->tests_unrepairable;    
    }
    
    public function getMessages(): array
    {
        return $this->messages;    
    }
    
    /**
     * Resets all parameters
     */
    protected function initializeChecks()
    {
        $this->tests_performed = 0;
        $this->tests_passed = 0;
        $this->tests_failed = 0;
        $this->tests_repaired = 0;
        $this->tests_unrepairable = 0;
        $this->messages = [];        
    }
    
    /**
     * Creates a collection entry for the checker list
     * @param checker $checker
     * @param string $method
     * @return \StdClass
     * Test testCreateArrayEntry
     */
    protected function createArrayEntry(Checker $checker, string $method): \StdClass
    {
        $result = new \StdClass();
        $result->checker = $checker;
        $result->method = $method;
        return $result;
    }
    
    /**
     * Collects all check method from the given checker class
     * @param checker $checker
     * @return array
     * Test: testCollectChecksFromChecker
     */
    protected function collectChecksFromChecker(Checker $checker): array
    {
        $result = [];
        $methods = get_class_methods($checker);
        foreach ($methods as $method) {
            if (substr($method,0,5) == 'check') {
                $result[] = $this->createArrayEntry($checker, $method);
            }
        }
        return $result;
    }
    
    /**
     * Collect all check methods from all install checker classes
     * @return array
     */
    protected function collectChecks(string $group): array
    {
        $result = [];
        foreach ($this->checker_classes as $checker_class) {
            $checker = new $checker_class();
            if (empty($group) || ($checker::$group == $group)) {
                $result = array_merge($result, $this->collectChecksFromChecker($checker));
            }
        }
        $this->total_tests = count($result);
        return $result;
    }
    
    /**
     Runs through each installed checker class and calls perfOrmChecks()
     */
    protected function walkChecks(array $checks, bool $repair, $callback): array 
    {
        foreach ($checks as $check) {
            $this->performSingleCheck($check->checker, $check->method, $repair, $callback);
        }
        return $this->messages;
    }
    
     /**
     * Just calls the three single tasks
     * @param checker $checker
     * @param string $method
     * @param bool $repair
     * @param unknown $callback
     * Test: not tested because trivial
     */
    protected function performSingleCheck(Checker $checker, string $method, bool $repair, $callback)
    {
        $this->doPerformSingleCheck($checker, $method, $repair);
        $this->callCallback($checker, $callback);
        $this->processSingleCheckResult($checker, $callback);
    }
    
    /**
     * Just calls the check method and ignores exceptions
     * @param checker $checker
     * @param string $method
     * @param bool $repair
     * Test: testPerformSingleCheck
     */
    protected function doPerformSingleCheck(Checker $checker, string $method, bool $repair)
    {
        try {
            $result[] = $checker->$method($repair);
        } catch (CheckException $e) {
            // Ignore Error
        }        
    }
    
    /**
     * Calles the given callback (if it is a callback) with the given parameters
     * Test: testCallCallback
     */
    protected function callCallback(Checker $checker, $callback)
    {
        if (is_callable($callback)) {
            $callback($checker, $this);
        }        
    }
    
    /**
     * Increases the according counter depending on the result of the last check
     * @param checker $checker
     * @throws CheckException
     * Test: testProcessSingleCheckResult
     */
    protected function processSingleCheckResult(Checker $checker)
    {
        switch ($checker->getLastResult()) {
            case 'passed':
                $this->lastCheckPassed();
                break;
            case 'failed':
                $this->lastCheckFailed($checker->getLastMessage());
                break;
            case 'repaired':
                $this->lastCheckRepaired($checker->getLastMessage());
                break;
            case 'unrepairable':
                $this->lastCheckUnrepairable($checker->getLastMessage());
                break;
            default:
                throw new CheckException("Unknown testresult: '".$checker->getLastResult()."'");
        }        
    }
    
    /**
     * Marks the last check as passed and increases the according counters
     * @param string $message A descriptive message
     * Test: testLastCheck
     */
    protected function lastCheckPassed()
    {
        $this->tests_performed++;
        $this->tests_passed++;
    }
    
    /**
     * Marks the last check as failed and not repaired and increases the according counters
     * @param string $message A descriptive message
     * Test: testLastCheck
     */
    protected function lastCheckFailed(string $message)
    {
        $this->tests_performed++;
        $this->tests_failed++;
        $this->messages[] = $message;
    }

    /**
     * Marks the last check as repaired and increases the according counters
     * @param string $message A descriptive message
     * Test: testLastCheck
     */
    protected function lastCheckRepaired(string $message)
    {
        $this->tests_performed++;
        $this->tests_failed++;
        $this->tests_repaired++;
        $this->messages[] = $message;
    }
    
    /**
     * Marks the last check as unrepairable and increases the according counters
     * @param string $message A descriptive message 
     * Test: testLastCheck
     */
    protected function lastCheckUnrepairable(string $message)
    {
        $this->tests_performed++;
        $this->tests_failed++;
        $this->tests_unrepairable++;
        $this->messages[] = $message;
    }
    
}

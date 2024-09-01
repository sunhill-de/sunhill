<?php

/**
 * Tests: src/Checker/Checks
 */
namespace Sunhill\Tests\Unit\Checker;

use Sunhill\Checker\Checks;


/**
 * Tests: installChecker(), purge()
 */
test('installChecker() and purge() work as expected', function()
{
    $test = new Checks();
    expect(empty(getProtectedProperty($test,'checker_classes')))->toBe(true);
    $test->installChecker("Test");
    expect(getProtectedProperty($test,'checker_classes')[0])->toBe("Test");
    $test->purge();
    expect(empty(getProtectedProperty($test,'checker_classes')))->toBe(true);
});

            
/**
 * Tests: getTestsPerformed(), getTestsPassed(), getTestsFailed(), getTests
 */
test('Return tests performed', function($varname, $methodname)
{
    $test = new Checks();
    setProtectedProperty($test, $varname, 5);
    expect($test->$methodname())->toBe(5);
    callProtectedMethod($test, 'initializeChecks');
    expect($test->$methodname())->toBe(0);
})->with([
    ['tests_performed','getTestsPerformed'],
    ['tests_passed','getTestsPassed'],
    ['tests_failed','getTestsFailed'],
    ['tests_repaired','getTestsRepaired'],
    ['tests_unrepairable','getTestsUnrepairable'],
]);


/**
 * Tests: lastCheckPassed, lastCheckFailed, lastCheckRepaired, lastCheckUnrepairable
 */
test('LastCheck', function($method, $performed, $passed, $failed, $repaired, $unrepairable) 
{
    $test = new Checks();
    callProtectedMethod($test, $method,['']);
    expect($test->getTestsPerformed())->toBe($performed);
    expect($test->getTestsPassed())->toBe($passed);
    expect($test->getTestsFailed())->toBe($failed);
    expect($test->getTestsRepaired())->toBe($repaired);
    expect($test->getTestsUnrepairable())->toBe($unrepairable);    
})->with([
    ['lastCheckPassed',1,1,0,0,0],
    ['lastCheckFailed',1,0,1,0,0],
    ['lastCheckRepaired',1,0,1,1,0],
    ['lastCheckUnrepairable',1,0,1,0,1],
]);
    
/**
 * Tests: processSingleCheckResult
 */
test('process single check result', function($method, $performed, $passed, $failed, $repaired, $unrepairable)
{
    $test = new Checks();
    $checker = new DummyChecker();
    setProtectedProperty($checker, 'last_result', $method);
    
    callProtectedMethod($test, 'processSingleCheckResult', [$checker]);
    
    expect($test->getTestsPerformed())->toBe($performed);
    expect($test->getTestsPassed())->toBe($passed);
    expect($test->getTestsFailed())->toBe($failed);
    expect($test->getTestsRepaired())->toBe($repaired);
    expect($test->getTestsUnrepairable())->toBe($unrepairable);
})->with([
    ['passed',1,1,0,0,0],
    ['failed',1,0,1,0,0],
    ['repaired',1,0,1,1,0],
    ['unrepairable',1,0,1,0,1],
]);
 
/**
 * Tests: callCallback
 */
test('callCallback works', function()
{
    $test = new Checks();
    $checker = new DummyChecker();
    setProtectedProperty($checker, 'last_result', 'passed');
    $result = '';
    
    $callback = function($checker, $checks) use (&$result) {
        $result = $checker->getLastResult();
    };
    callProtectedMethod($test, 'callCallback', [$checker, $callback]);
    
    expect($result)->toBe('passed');
});

/**
 * Tests: doPerformSingleCheck
 */
test('Perform check', function()
{
    $test = new Checks();
    $checker = new DummyChecker();
    
    callProtectedMethod($test, 'doPerformSingleCheck', [$checker, 'checkFailure', false]);
    
    expect($checker->getLastMessage())->toBe('FAILED');
});
    
/**
 * Tests createArrayEntry
 */
test('Create array entry', function()
{
    $test = new Checks();
    $checker = new DummyChecker();
    $result = callProtectedMethod($test,'createArrayEntry',[$checker,'test']);
    expect($result->checker)->toBe($checker);
    expect($result->method)->toBe('test');
});
    
/**
 * Tests collectionChecksFromChecker
 */
test('Collect Checks From Checker',function()
{
    $test = new Checks();
    $checker = new DummyChecker();
    $result = callProtectedMethod($test,'collectChecksFromChecker',[$checker]);
    usort($result, function ($a, $b) {
        return ($a->method < $b->method) ? -1 : 1;
        // Note: an equal case isn't possible
    });
    expect(count($result))->toBe(4);
    expect($result[0]->checker)->toBe($checker);
    expect($result[0]->method)->toBe('checkFailure');
});

/**
 * @depends testInstallChecker
 * Tests: collectChecks
 */
test('Collect checks', function()
{
    $test = new Checks();
    $test->installChecker(DummyChecker::class);
    $test->installChecker(AnotherDummyChecker::class);
    $result = callProtectedMethod($test, 'collectChecks', ['']);
    usort($result, function ($a, $b) {
        return ($a->method < $b->method) ? -1 : 1;
        // Note: an equal case isn't possible
    });
    expect(count($result))->toBe(5);
    expect($result[0]->method)->toBe('checkFailure');
    expect($result[3]->method)->toBe('checkSomething');
});

/**
 * Tests: check()
 */
test('check() works', function()
{
    $test = new Checks();
    $test->installChecker(DummyChecker::class);
    $test->installChecker(AnotherDummyChecker::class);
    
    $test->check();
    
    expect($test->getTotalTests())->toBe(5);
    expect($test->getTestsPerformed())->toBe(5);
    expect($test->getTestsPassed())->toBe(2);
    expect($test->getTestsFailed())->toBe(3);
    expect($test->getTestsRepaired())->toBe(1);
    expect($test->getTestsUnrepairable())->toBe(1);
});    
    

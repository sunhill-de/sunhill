<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

    /**
     * A wrapper for receiving values from an objects
     * If $fieldname is a simple string, $loader->$fieldname is returned
     * If $fieldname is in the form something[index], $loader->something[index] is returned
     * If $fieldname is in the form something->subfield, $loader->something->subfield is returned
     * If $fieldname is in the form something[index]->subfield, $loader->something[index]->subfield is returned
     * @param unknown $loader
     * @param unknown $fieldname
     * @return unknown
     */
    function getField($loader,$fieldname) {
        $match = '';
        if (preg_match('/(?P<name>\w+)\[(?P<index>\w+)\]->(?P<subfield>\w+)/',$fieldname,$match)) {
            $name = $match['name'];
            $subfield = $match['subfield'];
            $index = $match['index'];
            return $loader->$name[intval($index)]->$subfield;
        } else if (preg_match('/(?P<name>\w+)\[(?P<index>\w+)\]\[(?P<index2>\w+)\]/',$fieldname,$match)) {
            $name = $match['name'];
            $index2 = $match['index2'];
            $index = $match['index'];
            return $loader->$name[$index][$index2];
        } else if (preg_match('/(?P<name>\w+)->(?P<subfield>\w+)/',$fieldname,$match)) {
            $name = $match['name'];
            $subfield = $match['subfield'];
            return $loader->$name->$subfield;
        } if (preg_match('/(?P<name>\w+)\[(?P<index>\w+)\]/',$fieldname,$match)){
            $name = $match['name'];
            $index = $match['index'];
            return $loader->$name[$index];
        }  else if (is_string($fieldname)){
            return $loader->$fieldname;
        } else {
            return $loader;
        }
    }
    
    /**
     * copied from https://jtreminio.com/blog/unit-testing-tutorial-part-iii-testing-protected-private-methods-coverage-reports-and-crap/
     * Calls the protected or private method "$methodName" of the object $object with the given parameters and
     * returns its result
     * @param unknown $object
     * @param unknown $methodName
     * @param array $parameters
     * @return unknown
     */
    function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        
        return $method->invokeArgs($object, $parameters);
    }
    
    /**
     * Alias for invokeMethod
     * @param unknown $object
     * @param unknown $methodName
     * @param array $parameters
     * @return \Sunhill\Basic\Tests\unknown
     */
    function callProtectedMethod(&$object, $methodName, array $parameters = array()) {
        return invokeMethod($object, $methodName, $parameters);
    }
    
    /**
     * copied and modified from https://stackoverflow.com/questions/18558183/phpunit-mockbuilder-set-mock-object-internal-property
     * Sets the value of the property "$property_name" of object "$object" to value "$value"
     * @param unknown $object
     * @param unknown $property_name
     * @param unknown $value
     */
    function setProtectedProperty(&$object,$property_name,$value) {
        $reflection = new \ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property_name);
        $reflection_property->setAccessible(true);
        
        $reflection_property->setValue($object, $value);
    }
    
    /**
     * copied and modified from https://stackoverflow.com/questions/18558183/phpunit-mockbuilder-set-mock-object-internal-property
     * Returns the value of the property "$property_name" of object "$object"
     * @param unknown $object
     * @param unknown $property_name
     */
    function getProtectedProperty(&$object,$property_name) {
        $reflection = new \ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property_name);
        $reflection_property->setAccessible(true);
        
        return $reflection_property->getValue($object);
    }
    
    /**
     * The following two methods are helpers to test if one array is contained in another
     * @param unknown $expect
     * @param unknown $test
     * @return boolean
     */
    function checkArrays($expect,$test) {
        foreach ($expect as $key => $value) {
            if (!array_key_exists($key, $test)) {
                return false;
            }
            if (is_array($value)) {
                if (!$this->checkArrays($value,$test[$key])) {
                    return false;
                }
            }
        }
        return true;
    }
    
    /**
     * Tests recursive if all entries of $expect are contained in $test.
     * @param unknown $expect
     * @param unknown $test
     */
    function assertArrayContains($expect,$test) {
        if (!$this->checkArrays($expect,$test)) {
            $this->fail("The expected array is not contained in the passed one");
            return;
        }
        $this->assertTrue(true);
    }
    

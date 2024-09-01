<?php

namespace Sunhill\Tests;

use Sunhill\Tests\Constraints\DatabaseHasTableConstraint;
use Sunhill\Tests\Constraints\StdClassContainedConstraint;

trait SunhillTrait {
    
    protected function assertValueEquals($expected, $actual, $message = '')
    {
        if (is_callable($expected)) {
            $this->assertEquals($expected(), $actual, $message);
        } else {
            $this->assertEquals($expected, $actual, $message);
        }
    }
    
    protected function assertDatabaseHasTable(string $table,$message='') 
    {
        self::assertThat($table, new DatabaseHasTableConstraint(),$message );
    }
    
    protected function assertDatabaseHasNotTable(string $table,$message='') 
    {
        $this->assertDatabaseMissingTable($table, $message);
    }
    
    protected function assertDatabaseMissingTable(string $table, $message = '')
    {
        self::assertThat($table,$this->logicalNot(
            new DatabaseHasTableConstraint()
            ),$message );        
    }
    
    protected function assertStdClassHasValues(array $expect, \StdClass $test, $message = '')
    {
        self::assertThat($expect, new StdClassContainedConstraint($test), $message);    
    }
    
    /**
     * Return the temporary dir that is used to store test data
     * Note: This is public because the scenarios have to access this
     * @return string
     */
    public function getTempDir(): string
    {
        if (!file_exists(storage_path('temp/'))) {
            exec("mkdir ".storage_path('temp/'));
        }
        return storage_path('temp/');
    }
    
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
    protected function getField($loader,$fieldname) {
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
    public function invokeMethod(&$object, $methodName, array $parameters = array())
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
    public function callProtectedMethod(&$object, $methodName, array $parameters = array()) {
        return $this->invokeMethod($object, $methodName, $parameters);
    }
    
    /**
     * copied and modified from https://stackoverflow.com/questions/18558183/phpunit-mockbuilder-set-mock-object-internal-property
     * Sets the value of the property "$property_name" of object "$object" to value "$value"
     * @param unknown $object
     * @param unknown $property_name
     * @param unknown $value
     */
    public function setProtectedProperty(&$object,$property_name,$value) {
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
    public function getProtectedProperty(&$object,$property_name) {
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
    protected function checkArrays($expect,$test) {
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
    protected function assertArrayContains($expect,$test) {
        if (!$this->checkArrays($expect,$test)) {
            $this->fail("The expected array is not contained in the passed one");
            return;
        }
        $this->assertTrue(true);
    }
    
    /**
     * Creates a StdClass and fills it with the given key value pairs
     * @param unknown $values
     * @return \StdClass
     */
    protected function makeStdClass($values): \StdClass
    {
        $result = new \StdClass();
        foreach ($values as $key => $value) {
            $result->$key = $value;
        }
        return $result;
    }
}
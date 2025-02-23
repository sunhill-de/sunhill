<?php
/**
 * @file Checker.php
 * A base class for query checker
 * Lang en
 * Reviewstatus: 2025-02-17
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Query/CheckerTest.php
 * Coverage: 
 */

namespace Sunhill\Query;

use Nette\InvalidStateException;
use Sunhill\Query\Exceptions\InvalidStatementException;

class Checker extends QueryHandler
{
    
    /**
     * In this array all implemented functions are stored. Each entry consists of three fields:
     * input _ An array of expected argument(s) for this function. An ... indicated that there might be 
     *         any count of arguments. There has to be another parameter after ... that indicates the expected
     *         type for any following argument
     * output - The type this function returns
     * location - Where this function may be used ('fields', 'where')        
     * @var array
     */
    protected $implemented_functions = [
        'upper'=>['input'=>['string'],'output'=>'string','location'=>['fields','where']],
        'lower'=>['input'=>['string'],'output'=>'string','location'=>['fields','where']],
        'length'=>['input'=>['string'],'output'=>'integer','location'=>['fields','where']],
        'concat'=>['input'=>['...','string'],'output'=>'string','location'=>['fields','where']],
        'sqrt'=>['input'=>['float'],'output'=>'float','location'=>['fields','where']],
    ];
    
    /**
     * Constructor (just sets the QueryObject)
     * @param QueryObject $query
     */
    public function __construct(QueryObject $query)
    {
        $this->setQueryObject($query);
    }

    /**
     * Checks if the given function exists (= is an entry in $implemented_functions)
     * @param string $function
     */
    private function checkFunctionExists(string $function)
    {
        if (!isset($this->implemented_functions[$function])) {
            throw new InvalidStatementException("The function named '$function' is not implemented");
        }        
    }

    /**
     * Helper routine for getTypeOf that checks string constants if they are perhaps a date, time or datetime
     * 
     * @param string $test
     * @return string
     */
    private function getStringTypeOf(string $test): string
    {
        if (preg_match("/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[01])$/",$test)) {
            return 'date';
        }
        if (preg_match("/^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/",$test)) {
            return 'time';
        }
        if (preg_match("/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[01]) (0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/",$test)) {
            return 'datetime';
        }
        return 'string';
    }
    
    /**
     * Returns the type of the given token. Possible results are 'string', 'int', 'float', 'date', 'datetime', 'time' or 'array'
     * 
     * @param \stdClass $test
     * @return string
     */
    public function getTypeOf(\stdClass $test): string
    {
        switch ($test->type) {
            case 'field':
                return $this->getQueryObject()->getFieldType($test->field);
                break;
            case 'const':
                if (is_float($test->value)) {
                    return 'float';
                } else if (is_int($test->value)) {
                    return 'int';
                } else if (is_string($test->value)) {
                    return $this->getStringTypeOf($test->value);
                }
                break;
            case 'function_of_field':
            case 'function_of_value':
                return $this->implemented_functions[$test->function]['output'];
                break;
            case 'array_of_fields':
            case 'array_of_consts':
            case 'subquery':    
                return 'array';
                break;
             
        }        
    }
    
    /**
     * Return true if the given type of $test is the expected type
     * 
     * @param string $expected_type
     * @param \stdClass $test
     * @return bool
     */
    public function checkType(string $expected_type, \stdClass $test): bool
    {
        $given_type = $this->getTypeof($test);
        
        switch ($expected_type) {
            case 'float':
                return ($given_type == 'int') || ($given_type == 'float');
            case 'string':
                return $given_type !== 'array'; 
            default:
                return $given_type == $expected_type;
        }
    }
    
    /**
     * When a ellipse was passed check the remaining parameters if the match the expected one
     * 
     * @param string $function
     * @param array $arguments
     * @param int $point_expect
     * @param int $point_given
     * @return boolean
     */
    private function checkEllipse(string $function, array $arguments, int $point_expect, int $point_given)
    {
        if ($point_expect == count($this->implemented_functions[$function])) {
            throw new InvalidStatementException("Missing type parameter after ellipse");
        }
        $expected_type = $this->implemented_functions[$function]["input"][$point_expect+1];
        while ($point_given < count($arguments)) {
            if (!$this->checkType($expected_type, $arguments[$point_given])) {
                throw new InvalidStatementException("The argument is not the expected type ".$expected_type);                
            }
            $point_given++;
        }
        return true;
    }
    
    /**
     * Check if the given arguments match the expected ones in a function
     * 
     * @param string $function
     * @param array $arguments
     * @return boolean
     */
    private function checkParametersMatch(string $function, array $arguments)
    {
        $point_expect = 0;
        $point_given = 0;
        while ($point_expect < count($this->implemented_functions[$function]['input'])) {
            if ($point_given >= count($arguments)) {
                throw new InvalidStatementException("Function '$function' was given to few parameters (".count($this->implemented_functions[$function]['input'])." expected)");
            }
            if ($this->implemented_functions[$function]['input'][$point_expect] == '...') {
                $this->checkEllipse($function, $arguments, $point_expect, $point_given);
                return true;
            }
            if (!$this->checkType($expect = $this->implemented_functions[$function]['input'][$point_expect++],$arguments[$point_given++])) {
                throw new InvalidStatementException("Given parameter is not of expected type ".$expect);                
            }
        }
        return true;
    }
    
    /**
     * Checks if the use of a function is valid
     * 
     * @param \stdClass $field
     */
    public function checkFunction(\stdClass $field): bool
    {
        $this->checkFunctionExists($field->function);
        $this->checkParametersMatch($field->function, $field->arguments);
        return true;
    }
    
    /**
     * Checks if a stored field parameter are valid
     * 
     * @param \stdClass $field
     * @return boolean
     */
    private function checkField(\stdClass $field)
    {
        switch ($field->type) {
            case 'const':
            case 'field':
                return true;
            case 'function_of_field':
            case 'function_of_const':    
                $this->checkFunction($field);
                return true;
                break;
            default:
                throw new InvalidStatementException("Token of type '".$field->type."' is not allowed in field statement");
        }
    }
    
    /**
     * Checks if all stored field parameters are valid
     * 
     * @param array $fields
     */
    private function checkFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->checkField($field);
        }
    }

    private function checkWhereCondition(\stdClass $condition)
    {
    
    }
    
    private function checkWhereStatements(array $where)
    {
        foreach ($where as $where_condition) {
            $this->checkWhereCondition($where_condition);
        }    
    }
    
    public function check()
    {
        if ($fields = $this->getQueryObject()->getFields()) {
            $this->checkFields($fields);
        }
        if ($where = $this->getQueryObject()->getWhereStatements()) {
            $this->checkWhereStatements($where);
        }
        return true;
    }
    
}

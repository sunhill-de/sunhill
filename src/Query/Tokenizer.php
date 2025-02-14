<?php

/**
 * @file Tokenizer.php
 * A helper class for parsing query parameters
 * Lang en
 * Reviewstatus: 2025-02-14
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Query/TokenizerTest.php
 * Coverage: unknown
 */

namespace Sunhill\Query;

use Sunhill\Basic\Base;
use Sunhill\Query\Exceptions\InvalidTokenClassException;
use Sunhill\Query\Exceptions\InvalidTokenException;
use Sunhill\Query\Exceptions\UnexpectedTokenException;

class Tokenizer extends Base
{
    
    protected $structure;
    
    protected $expectable_tokens = [
        'field',
        'const',
        'callback',
        'array_of_fields',
        'array_of_constants',
        'subquery',
        'function_of_field',
        'function_of_value',
    ];
    /**
     * Constructure, just sets the structure of the underlaying record
     * 
     * @param unknown $structure
     */
    public function __construct($structure)
    {
        $this->setStructure($structure);
    }
    
    /**
     * Sets the structure of the underlying record
     * 
     * @param unknown $structure
     * @return \Sunhill\Query\Tokenizer
     */
    public function setStructure($structure)
    {
        $this->structure = $structure;
        return $this;
    }
    
    /**
     * Checks if the given list of expected tokeens are in the list of allowed tokens
     * 
     * @param array $token
     */
    private function checkExpectedToken(array $token)
    {
        foreach ($token as $single_token) {
            if (!in_array($single_token,$this->expectable_tokens)) {
                throw new InvalidTokenClassException("The token class '$single_token' is invalid");
            }
        }
    }
    
    
    /**
     * Returns true if this record has the given property "$etst"
     *
     * @param string $test
     * @return bool
     */
    protected function hasProperty(string $test): bool
    {
        return isset($this->structure->elements[$test]);
    }
    
    /**
     * Helper function that parses the argument list of of function
     *
     * @param unknown $argument
     * @return unknown[]|\stdClass[]
     */
    private function getArgumentList($argument)
    {
        $result = [];
        foreach (explode(',',$argument) as $single_argument) {
            $result[] = $this->parseForToken($single_argument);
        }
        return $result;
    }
    
    private function parseFunction(string $name, string $arguments)
    {
        $result = new \stdClass();
        $result->function = $name;
        $result->argument = $this->parseForToken($arguments);
        switch ($result->argument->type) {
            case 'field':
            case 'array_of_fields':
                $result->type = 'function_of_field';
                break;
            default:
                $result->type = 'function_of_value';
                break;
        }
        return $result;        
    }
    
    private function testForArrayofFields($parameter)
    {
        $result = new \stdClass();
        $result->type = 'array_of_fields';
        $result->elements = [];
        foreach ($parameter as $field) {
            $field = trim($field);
            if (!$this->hasProperty($field)) {
                return false;
            }
            $result->elements[] = makeStdClass(['type'=>'field','field'=>$field]);
        }
        
        return $result;    
    }
    
    /**
     * Try to parse a string to a field
     *
     * @param string $field
     * @return \stdClass|StdClass
     */
    protected function parseString(string $parameter)
    {
        $result = new \stdClass();
        if (preg_match('/^([a-zA-Z_][_[:alnum:]]*)\((.*)\)$/',$parameter,$matches)) {
            return $this->parseFunction($matches[1],$matches[2]);
        }
        if (preg_match('/^([a-zA-Z_][_[:alnum:]]*)->(.*)$/',$parameter,$matches)) {
            $result->type = 'reference';
            $result->parent = $matches[1];
            $result->reference = $this->getField($matches[2]);
            return $result;
        }
        if (preg_match('/^\"(.*)\"$/',$parameter,$matches)) {
            return makeStdClass(['type'=>'const','value'=>$matches[1]]);
        }
        if (preg_match("/^\'(.*)\'$/",$parameter,$matches)) {
            return makeStdClass(['type'=>'const','value'=>$matches[1]]);
        }
        if ((strpos($parameter,',') !== false) && (preg_match("/^([a-zA-Z_0-9,\s]*)$/",$parameter))) {
            if ($result = $this->testForArrayOfFields(explode(",",$parameter))) {
                return $result;
            }
        }
        if ($this->hasProperty($parameter)) {
            // if it consist only of allowed characters for a field we assume a field. We have to decide later
            return makeStdClass(['type'=>'field','name'=>$parameter]);
        }
        return makeStdClass(['type'=>'const','value'=>$parameter]);
    }
    
    private function parseArray(array $parameter)
    {
        if ($result = $this->testForArrayofFields($parameter)) {
            return $result;
        } else {
            return makeStdClass(['type'=>'array_of_constants', 'value'=>$parameter]);
        }
    }
    
    /**
     * Try to parse the given parameter to something the query can process
     *
     * @param unknown $parameter
     * @return stdClass|\Sunhill\Query\StdClass|StdClass
     */
    private function parseForToken($parameter)
    {
        if (is_string($parameter)) {
            return $this->parseString($parameter);
        }
        if (is_scalar($parameter)) {
            return makeStdClass(['type'=>'const','value'=>$parameter]);
        }
        if (is_a($parameter, BasicQuery::class)) {
            return makeStdClass(['type'=>'subquery','value'=>$parameter]);
        }
        if (is_array($parameter) || is_a($parameter, \Traversable::class)) {
            return $this->parseArray($parameter);
        }
        if (is_callable($parameter)) {
            return makeStdClass(['type'=>'callback','value'=>$parameter]);
        }
        throw new InvalidTokenException("The given parameter was not parsable for a query");        
    }
    
    private function checkIfTokenWasExpected($token, array $expected_tokens)
    {
        if (!in_array($token->type,$expected_tokens)) {
            throw new UnexpectedTokenException("A token of class '".$token->type."' is not expected here.");
        }
    }
    
    /**
     * Parses the given parameter and checks if the parsed parameter is a expected one
     * 
     * @param unknown $parameter
     * @param array $expected_token
     */
    public function parseParameter($parameter, array $expected_token)
    {
        $this->checkExpectedToken($expected_token);
        $token = $this->parseForToken($parameter);
        $this->checkIfTokenWasExpected($token, $expected_token);
        return $token;
    }
}
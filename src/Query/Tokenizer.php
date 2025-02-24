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

use Sunhill\Query\Exceptions\InvalidTokenClassException;
use Sunhill\Query\Exceptions\InvalidTokenException;
use Sunhill\Query\Exceptions\UnexpectedTokenException;

class Tokenizer extends QueryHandler
{
    
    protected $early_call = true;
    
    protected $expectable_tokens = [
        'field',
        'reference',
        'const',
        'callback',
        'array_of_fields',
        'array_of_consts',
        'subquery',
        'function_of_field',
        'function_of_value',
    ];
    /**
     * Constructure, just sets the QueryObject
     * 
     * @param QueryObject $query
     */
    public function __construct(QueryObject $query)
    {
        $this->setQueryObject($query);
    }
    
    protected $additional = [];

    /**
     * Sometimes it is necessary to pass some additional parameters to the Tokenizer, these are added to the token information
     */
    protected function setAdditional(array $additional)
    {
        $this->additional = $additional;
        return $this;
    }

    /**
     * A private re-implementation of the makeStdClass() helper function. Merges the additional informations
     */
    private function makeStdClass(array $items)
    {
        return makeStdClass(array_merge($items, $this->additional));    
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
        $result = $this->makeStdClass(['function'=>$name]);
        
        $arguments = trim($arguments);
        if (empty($arguments)) {
            $result->type = 'function_of_value';
            $result->arguments = null;
            return $result;
        }
        $arguments = $this->parseForToken($arguments);
        switch ($arguments->type) {
            case 'reference':
            case 'field':
                $result->type = 'function_of_field';
                $result->arguments = [$arguments];
                break;
            case 'array_of_fields':
                $result->type = 'function_of_field';
                $result->arguments = $arguments->elements;
                break;
            case 'array_of_consts':
                $result->type = 'function_of_value';
                $result->arguments = $arguments->elements;
                break;
            case 'const':
            default:
                $result->type = 'function_of_value';
                $result->arguments = [$arguments];
                break;
        }
        return $result;        
    }
    
    private function testForArrayofFields($parameter)
    {
        $result = $this->makeStdClass(['type'=>'array_of_consts','elements'=>[]]);
        foreach ($parameter as $field) {
            $field = trim($field);
            if ($this->getQueryObject()->hasField($field)) {
                $result->type = 'array_of_fields';
                $result->elements[] = $this->makeStdClass(['type'=>'field','field'=>$field]);
            } else {
                $result->elements[] = $this->makeStdClass(['type'=>'const','value'=>$field]);                
            }
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
        if (preg_match('/^([a-zA-Z_][_[:alnum:]]*)\((.*)\)$/',$parameter,$matches)) {
            return $this->parseFunction($matches[1],$matches[2]);
        }
        if (preg_match('/^([a-zA-Z_][_[:alnum:]]*)->(.*)$/',$parameter,$matches)) {
            // Note: There is no check, if $name really is a field of the given structure (because of nested references). Th check has to be done later!
            return $this->makeStdClass(['type'=>'reference','name'=>$matches[1],'key'=>$this->getField($matches[2])]);
        }
        if (preg_match('/^\"(.*)\"$/',$parameter,$matches)) {
            return $this->makeStdClass(['type'=>'const','value'=>$matches[1]]);
        }
        if (preg_match("/^\'(.*)\'$/",$parameter,$matches)) {
            return $this->makeStdClass(['type'=>'const','value'=>$matches[1]]);
        }
        if ((strpos($parameter,',') !== false) && (preg_match("/^([a-zA-Z_0-9,\s]*)$/",$parameter))) {
            if ($result = $this->testForArrayOfFields(explode(",",$parameter))) {
                return $result;
            }
        }
        if ($this->getQueryObject()->hasField($parameter)) {
            // if it consist only of allowed characters for a field we assume a field. We have to decide later
            return $this->makeStdClass(['type'=>'field','name'=>$parameter]);
        }
        return $this->makeStdClass(['type'=>'const','value'=>$parameter]);
    }
    
    private function parseArray(array $parameter)
    {
        if ($result = $this->testForArrayofFields($parameter)) {
            return $result;
        } else {
            return $this->makeStdClass(['type'=>'array_of_constants', 'value'=>$parameter]);
        }
    }

    private function handleCallback(callable $callback)
    {
        if ($this->early_call) {
            return $this->parseForToken($callback());
        }
        return makeStdClass(['type'=>'callback','callback'=>$callback]);
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
            return $this->makeStdClass(['type'=>'const','value'=>$parameter]);
        }
        if (is_a($parameter, BasicQuery::class)) {
            return $this->makeStdClass(['type'=>'subquery','value'=>$parameter]);
        }
        if (is_array($parameter) || is_a($parameter, \Traversable::class)) {
            return $this->parseArray($parameter);
        }
        if (is_callable($parameter)) {
            return $this->handleCallback($parameter);
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
    public function parseParameter($parameter, array $expected_token, array $additional = [], bool $early_call = true)
    {
        $this->setAdditional($additional);
        $this->early_call = $early_call;
        $this->checkExpectedToken($expected_token);
        $token = $this->parseForToken($parameter);
        $this->checkIfTokenWasExpected($token, $expected_token);
        return $token;
    }
}

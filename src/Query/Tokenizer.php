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
    
    private function parseForToken($parameter)
    {
        
    }
    
    private function checkIfTokenWasExpected($token, array $expected_tokens)
    {
        
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
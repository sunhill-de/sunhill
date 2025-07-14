<?php
/**
 * @file QueryAnalyzer.php
 * The parser that analyzes query strings 
 * Lang en
 * Reviewstatus: 2025-07-14
 * Creation date: 2025-04-25
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage Unit:
 */

namespace Sunhill\Query\QueryParser;

use Sunhill\Parser\AbstractAnalyzer;
use Sunhill\Properties\RecordProperty;
use Sunhill\Parser\LanguageDescriptor\FunctionDescriptor;
use Sunhill\Parser\Nodes\Node;

class QueryAnalyzer extends AbstractAnalyzer
{
        
    protected ?RecordProperty $property = null;
    
    public function __construct(RecordProperty $property)
    {
        $this->property = $property;
        //$this->loadLanguageDescriptor(new QueryParserLanguage());    
    }
 
    protected function getProperty(): ?RecordProperty
    {
        return $this->property;        
    }
    
    /**
     * Returns the type of the given identifier. If the identifier is not found return null.
     *
     * @param string $name
     * @return string|NULL
     */
    protected function getTypeOfIdentifier(string $name): ?string
    {
        if (!($element = $this->getProperty()->getElement($name))) {
            return null;
        }
        if (is_a($element, RecordProperty::class)) {
            return 'record';
        }
        return $element->getAccessType(); 
    }
    
    /**
     * Returns the FunctionDescriptor for the given function. If the function is not found returns null
     *
     * @param string $name
     * @return FunctionDescriptor|NULL
     */
    protected function getProfileOfFunction(string $name): ?FunctionDescriptor
    {
        
    }
    
    /**
     * Returns the defined combination of types allowed for this operator (and only for this).
     * The result is an associative array with the keys "left", "right" and "resulting". The values have to be the
     * datatype that is accepted ("integer", "string", "float", "boolean", "date", "time", "datetime") or a pseudo
     * type ("numeric" or "pseudoboolean")
     *
     * @param string $operator
     * @return array|NULL
     */
    protected function getProfilesOfBinaryOperator(string $operator): ?array
    {
        
    }
    
    /**
     * Returns the defined combination of types allowed for this operator (and only for this).
     * The result is an associative array with the keys "child" and "resulting". The values have to be the
     * datatype that is accepted ("integer", "string", "float", "boolean", "date", "time", "datetime") or a pseudo
     * type ("numeric" or "pseudoboolean")
     *
     * @param string $operator
     * @return array|NULL
     */
    protected function getProfilesOfUnaryOperator(string $operator): ?array
    {
        
    }
    
    /**
     * This method checks the resulting datatype of the node and returns the result of the check as a boolean.
     *
     * @param string $type
     * @return bool
     */
    protected function checkAcceptedType(string $type): bool
    {
        return true;
    }
    
    /**
     * This method is called when the Analyzer doesn't knoe the type of the node.
     *
     * @param Node $node
     */
    protected function prepareUnknownNode(Node $node)
    {
        // Do nothing by default
    }
    
    
}
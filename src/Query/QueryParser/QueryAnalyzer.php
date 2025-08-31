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
use Sunhill\Parser\LanguageDescriptor\FunctionDescriptor;
use Sunhill\Parser\LanguageDescriptor\LanguageDescriptor;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Properties\RecordProperty;
use Sunhill\Query\Exceptions\InsufficentQueryException;
use Sunhill\Query\Exceptions\InvalidStatementException;

class QueryAnalyzer extends AbstractAnalyzer
{
    /**
     * Stores the main RecordProperty that this query refers to
     *
     * @var unknown
     */
    protected ?RecordProperty $property = null;

    /**
     * The definition of the parser language is stored here
     *
     * @var unknown
     */
    protected ?LanguageDescriptor $descriptor = null;

    /**
     * Constructor. Takes the main RecordProperty as a parameter
     */
    public function __construct(RecordProperty $property)
    {
        $this->property = $property;
    }

    /**
     * Getter for the property
     */
    protected function getProperty(): ?RecordProperty
    {
        if (! $this->property) {
            throw new InsufficentQueryException('No record property set.');
        }

        return $this->property;
    }

    /**
     * Setter for the language desciptor
     */
    public function setLanguageDescriptor(LanguageDescriptor $descriptor): static
    {
        $this->descriptor = $descriptor;

        return $this;
    }

    /**
     * Getter for the language descriptor
     */
    public function getLanguageDesciptor(): ?LanguageDescriptor
    {
        if (! $this->descriptor) {
            throw new InsufficentQueryException('No language descriptor set');
        }

        return $this->descriptor;
    }

    /**
     * Returns the type of the given identifier. If the identifier is not found return null.
     */
    protected function getTypeOfIdentifier(string $name): ?string
    {
        if (! ($element = $this->getProperty()->getElement($name))) {
            return null;
        }
        if (is_a($element, RecordProperty::class)) {
            return 'record';
        }

        return $element->getAccessType();
    }

    private function handleUnknownFunction(string $name): ?FunctionDescriptor
    {
        return null;
    }

    /**
     * Returns the FunctionDescriptor for the given function. If the function is not found returns null
     */
    protected function getProfileOfFunction(string $name): ?FunctionDescriptor
    {
        $name = strtolower($name); // @todo should we do this?
        if (! ($profile = $this->getLanguageDesciptor()->getFunctionProfile($name))) {
            if (! ($profile = $this->handleUnknownFunction($name))) {
                throw new InvalidStatementException("The functiion '$name' was not found.");
            }
        }

        return $profile;
    }

    /**
     * Returns the defined combination of types allowed for this operator (and only for this).
     * The result is an associative array with the keys "left", "right" and "resulting". The values have to be the
     * datatype that is accepted ("integer", "string", "float", "boolean", "date", "time", "datetime") or a pseudo
     * type ("numeric" or "pseudoboolean")
     */
    protected function getProfilesOfBinaryOperator(string $operator): ?array
    {
        $operator = strtolower($operator); // @todo: Should we do this?
        if (! ($profile = $this->getLanguageDesciptor()->getBinaryOperator($operator))) {
            return null;
        }

        return $profile->getAcceptedTypes();
    }

    /**
     * Returns the defined combination of types allowed for this operator (and only for this).
     * The result is an associative array with the keys "child" and "resulting". The values have to be the
     * datatype that is accepted ("integer", "string", "float", "boolean", "date", "time", "datetime") or a pseudo
     * type ("numeric" or "pseudoboolean")
     */
    protected function getProfilesOfUnaryOperator(string $operator): ?array
    {
        $operator = strtolower($operator); // @todo: Should we do this?
        if (! ($profile = $this->getLanguageDesciptor()->getUnaryOperator($operator))) {
            return null;
        }

        return $profile->getAcceptedTypes();
    }

    /**
     * This method checks the resulting datatype of the node and returns the result of the check as a boolean.
     */
    protected function checkAcceptedType(string $type): bool
    {
        return true;
    }

    /**
     * This method is called when the Analyzer doesn't knoe the type of the node.
     */
    protected function prepareUnknownNode(Node $node)
    {
        // Do nothing by default
    }
}

<?php

/**
 * @file FunctionNode.php
 * A basic class for a node that is a function
 * Lang en
 * Reviewstatus: 2025-03-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage Unit: 93.75 % (2025-06-06)
 */

namespace Sunhill\Parser\Nodes;

use Sunhill\Parser\Exceptions\FunctionNotFoundException;
use Sunhill\Parser\Exceptions\FunctionParameterException;
use Sunhill\Parser\LanguageDescriptor\FunctionDescriptor;

class FunctionNode extends Node
{
    /**
     * Simplyfied constructor that just fills the parent with default values
     */
    public function __construct(string $name)
    {
        parent::__construct('func', []);
        $this->name($name);
    }

    protected ?FunctionDescriptor $function_descriptor = null;

    public function setFunctionDescriptor(FunctionDescriptor $descriptor): static
    {
        $this->function_descriptor = $descriptor;

        return $this;
    }

    public function getFunctionDescriptor(): ?FunctionDescriptor
    {
        return $this->function_descriptor;
    }

    public function getDatatype(): ?string
    {
        if (is_null($this->getFunctionDescriptor())) {
            return null;
        }

        return $this->getFunctionDescriptor()->getReturnType();
    }

    /**
     * Simplified setter/getter for the function name. When called with parameter it acts as a setter otherwise as a getter.
     */
    public function name(?string $name = null)
    {
        if (! is_null($name)) {
            $this->children['name'] = $name;

            return $this;
        } else {
            return $this->children['name'];
        }
    }

    /**
     * Simplified setter/getter for the function arguments. When called with parameter it acts as a setter otherwise as a getter.
     */
    public function arguments(?Node $arguments = null)
    {
        if (! is_null($arguments)) {
            $this->children['arguments'] = $arguments;

            return $this;
        } else {
            return $this->children['arguments'] ?? null;
        }
    }

    public function getArgumentCount(): int
    {
        if (isset($this->children['arguments'])) {
            return is_a($this->children['arguments'], ArrayNode::class) ? $this->children['arguments']->elementCount() : 1;
        }

        return 0;
    }

    public function getArgument(int $index): ?Node
    {
        if (! isset($this->children['arguments']) || ($index < 0) || ($index >= $this->getArgumentCount())) {
            return null;
        }

        return is_a($this->children['arguments'], ArrayNode::class) ? $this->children['arguments']->getElement($index) : $this->children['arguments'];
    }

    public function toString(): string
    {
        $result = $this->name().'(';
        $first = true;
        for ($i = 0; $i < $this->getArgumentCount(); $i++) {
            $result .= ($first ? '' : ',').$this->getArgument($i)->toString();
            $first = false;
        }

        return $result.')';
    }

    private int $argument_ptr = 0;

    private function reset()
    {
        $this->argument_ptr = 0;
    }

    private function pop(): ?string
    {
        if (is_null($argument = $this->getArgument($this->argument_ptr++))) {
            return null;
        }

        return $argument->getDatatype();
    }

    private function analyzeFunctionNode(FunctionDescriptor $descriptor)
    {
        $descriptor->reset();
        $actual_argument = $this->pop();
        while (! is_null($actual_argument)) {
            $expected_argument = $descriptor->pop();
            $optional = $expected_argument[0];
            $expected_type = substr($expected_argument, 1);
            if ($actual_argument !== $expected_type) {
                throw new FunctionParameterException("Expected '$expected_type' got '$actual_argument'");
            }
            $actual_argument = $this->pop();
        }
        if (! is_null($expected_argument = $descriptor->pop())) {
            $optional = $expected_argument[0];
            $expected_type = substr($expected_argument, 1);
            if ($optional !== '?') {
                throw new FunctionParameterException("Expected '$expected_type' got nothing");
            }
        }
    }

    public function validate()
    {
        if (is_null($this->getFunctionDescriptor())) {
            throw new FunctionNotFoundException("The function '".$this->name()."' was not found.");
        }
        $this->analyzeFunctionNode($this->getFunctionDescriptor());
    }
}

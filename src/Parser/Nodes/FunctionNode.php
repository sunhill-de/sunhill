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

use Sunhill\Parser\Traits\UnknownDatatype;
use Sunhill\Parser\LanguageDescriptor\FunctionDescriptor;
use Sunhill\Parser\Exceptions\FunctionParameterException;
use Sunhill\Parser\Exceptions\FunctionNotFoundException;

class FunctionNode extends Node
{

    use UnknownDatatype;
    
    /**
     * Simplyfied constructor that just fills the parent with default values
     */   
    public function __construct(string $name)
    {
        parent::__construct('func',[]);
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
    
    /**
     * Simplified setter/getter for the function name. When called with parameter it acts as a setter otherwise as a getter.
     */      
    public function name(?string $name = null)
    {
        if (!is_null($name)) {
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
        if (!is_null($arguments)) {
            $this->children['arguments'] = $arguments;
            return $this;
        } else {
            return $this->children['arguments']??null;
        }
    }

    public function getArgumentCount(): int
    {
        if (isset($this->children['arguments'])) {
            return is_a($this->children['arguments'],ArrayNode::class)?$this->children['arguments']->elementCount():1;
        }
        return 0;
    }
    
    public function getArgument(int $index): ?Node
    {
        if (!isset($this->children['arguments']) || ($index < 0) || ($index >= $this->getArgumentCount())) {
            return null;
        }
        return is_a($this->children['arguments'],ArrayNode::class)?$this->children['arguments']->getElement($index):$this->children['arguments'];
    }
    
    public function toString(): string
    {
        $result = $this->name().'(';
        $first = true;
        for ($i=0;$i<$this->getArgumentCount();$i++) {
            $result .= ($first?'':',').$this->getArgument($i)->toString();
            $first = false;
        }
        return $result.')';
    }
    
    private function buildExpectedParameters(FunctionDescriptor $descriptor): array
    {
        $result = $descriptor->getParameterDescriptors();
        if ($descriptor->getUnlimitedParameters()) {
            for ($i=0;$i<$descriptor->getMinimumParameterCount();$i++) {
                $entry = new \stdClass();
                $entry->type = $descriptor->getUnlimitedType();
                $entry->optional = false;
                $result[] = $entry;
            }
            $entry = new \stdClass();
            $entry->type = $descriptor->getUnlimitedType();
            $entry->optional = true;
            $entry->dontshift = true;
            $result[] = $entry;
        }
        return $result;
    }
    
    private function buildGivenParameters(FunctionNode $node): array
    {
        $result = [];
        for ($i=0;$i<$node->getArgumentCount();$i++) {
            $result[] = $this->getTypeOfNode($node->getArgument($i));
        }
        return $result;
    }
    
    private function getExpectedParameter(&$expected)
    {
        $parameter = array_shift($expected);
        if (isset($parameter->dontshift)) {
            array_unshift($expected, $parameter);
        }
        return $parameter;
    }
    
    private function checkParameters(FunctionDescriptor $descriptor)
    {
        $expected = $this->buildExpectedParameters($descriptor);
        $given = $this->buildGivenParameters($node);
        while (!empty($given)) {
            $given_parameter = array_shift($given);
            $expected_parameter = $this->getExpectedParameter($expected);
            if (is_null($expected_parameter)) {
                throw new FunctionParameterException("Too many parameters.");
            }
            if (!$this->typeMatch($given_parameter,$expected_parameter->type)) {
                throw new FunctionParameterException("Parameter type mismatch. Expected '".$expected_parameter->type."', got '$given_parameter'");
            }
        }
        if (!empty($expected)) {
            $expected_parameter = $this->getExpectedParameter($expected);
            if (!$expected_parameter->optional) {
                throw new FunctionParameterException("Too few parameters. Expected ".$expected_parameter->type);
            }
        }
    }
    
    protected function analyzeFunctionNode($descriptor)
    {
        $this->checkParameters($descriptor);
    }
    
    
    public function validate()
    {
        if (is_null($this->getFunctionDescriptor())) {
            throw new FunctionNotFoundException("The function '".$this->getName()."' was not found.");
        }
        $this->analyzeFunctionNode($this->getFunctionDescriptor());
    }
    
}

<?php

/**
 * @file FunctionDescriptor.php
 * A basic class for describing a function
 *
 * Lang en
 * Reviewstatus: 2025-07-15
 * Create date: 2025-02-28
 * Localization: complete
 * Documentation: complete
 * Tests: /tests/Unit/Parser/LanguageDescriptor/FunctionDescriptorTest.php
 * Coverage Unit:
 */

namespace Sunhill\Parser\LanguageDescriptor;

use Sunhill\Basic\Base;
use Sunhill\Parser\Exceptions\LanguageDescriptorException;

class FunctionDescriptor extends Base
{
    protected string $context;

    /**
     * The name of the function
     */
    protected string $name;

    /**
     * The return type of the function
     */
    protected string $return_type;

    /**
     * An array of parameter descriptors (or empty if no parameter)
     */
    protected array $parameters = [];

    /**
     * Helper function that returns the last added parameter
     * 
     * @return array|NULL
     */
    private function stackTop(?int $position = null): ?array
    {
        if (!count($this->parameters)) {
            return null;
        }
        return ($this->parameters[is_null($position)?count($this->parameters)-1:$position]);
    }
    
    /**
     * Creates a new function descriptor and sets its name
     * 
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Getter for the name
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Setter for return type
     * 
     * @param string $type
     * @return static
     */
    public function setReturnType(string $type): static
    {
        $this->return_type = $type;
        
        return $this;
    }
    
    /**
     * Getter for return type
     * 
     * @return string
     */
    public function getReturnType(): string
    {
        return $this->return_type;
    }

    /**
     * Setter for context. The context is an addional information to tell the parse, in which context a
     * function could be used.
     * 
     * @param string $context
     * @return static
     */
    public function setContext(string $context): static
    {
        $this->context = $context;
        
        return $this;
    }
    
    /**
     * Getter for context.
     * 
     * @return string
     */
    public function getContext(): string
    {
        return $this->context;
    }
    
    /**
     * Adds an mandatory parameter that has to be passed to this function
     * 
     * @param string $type The type of this parameter
     * @return static
     */
    public function addMandatoryParameter(string $type): static
    {
        if ((count($this->parameters)) && ($this->stackTop()['param_class'] == 'optional')) {
            throw new LanguageDescriptorException("In a function descriptor an mandatory parameter must't be after an optional");
        }
        if ((count($this->parameters)) && ($this->stackTop()['param_class'] == 'ellipsis')) {
            throw new LanguageDescriptorException("In a function descriptor an mandatory parameter must't be after an ellipsis");
        }
        
        $this->parameters[] = ['type'=>$type, 'param_class'=>'mandatory'];
        
        return $this;
    }
    
    /**
     * Adds an optional parameter that can but doesn't have  to be passed to this function
     *
     * @param string $type The type of this parameter
     * @return static
     */
    public function addOptionalParameter(string $type): static
    {
        if ((count($this->parameters)) && ($this->stackTop()['param_class'] == 'ellipsis')) {
            throw new LanguageDescriptorException("In a function descriptor an optional parameter must't be after an ellipsis");
        }
        
        $this->parameters[] = ['type'=>$type, 'param_class'=>'optional'];        
        
        return $this;
    }

    /**
     * Adds an ellipsis. An ellipis is a variable count of parameters of the same type. 
     * 
     * @param string $type
     * @return static
     */
    public function addEllipsis(string $type): static
    {
        $this->parameters[] = ['type'=>$type, 'param_class'=>'ellipsis'];        
        
        return $this;
    }
    
    /**
     * Returns the count of parameters
     * 
     * @return int
     */
    public function getParameterCount(): int
    {
        return count($this->parameters);
    }

    /**
     * Used internally as a stack pointer
     * 
     * @var integer
     */
    private int $stack_pointer = 0;
    
    /**
     * Resets the "stack" for this function
     */
    public function reset()
    {
        $this->stack_pointer = 0;
    }
    
    /**
     * "pops" the next possible parameter and returns its type (or null if no parameter is left)
     * 
     * @return string|NULL
     */
    public function pop(): ?string
    {
        if (!count($this->parameters)) {
            return null;
        }
        if ($this->stack_pointer >= count($this->parameters)) {
            return null;
        }
        $result = $this->stackTop($this->stack_pointer)['type'];
        if ($this->stackTop($this->stack_pointer)['param_class'] !== 'ellipsis') {
            $this->stack_pointer++;
        }
        return $result;
    }

    /**
     * Returns true when all mandatory parameters where popped
     * 
     * @return bool
     */
    public function mandatoryFinished(): bool
    {
        return (!count($this->parameters) || ($this->stackTop($this->stack_pointer))['parameters_type'] !== 'mandatory');
    }
}

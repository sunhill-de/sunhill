<?php
/**
 * @file OperatorDescriptor.php
 * A helper class that stores all relevant informations about a operator
 * Lang en
 * Reviewstatus: 2025-07-15
 * Creation date: 2025-03-19
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage Unit:72.73 % (2025-06-06)
 */

namespace Sunhill\Parser\LanguageDescriptor;

use Sunhill\Basic\Base;
use phpDocumentor\Reflection\Types\Static_;
use Sunhill\Parser\Exceptions\LanguageDescriptorException;

class OperatorDescriptor extends Base
{
    
    /**
     * The operator we are taling of
     * 
     * @var string
     */
    protected string $operator = '';
    
    /**
     * The precedence of this operator
     * 
     * @var integer
     */
    protected int $precedence = 0;
    
    /**
     * What kind of operator (unary, binary, tertiary)
     * 
     * @var string
     */
    protected string $type = 'binary';
    
    /**
     * Stores the allowed types for this operator
     * 
     * @var array
     */
    protected array $accepted_types = [];
    
    public function __construct(string $operator)
    {
        $this->operator = $operator;
    }
    
    /**
     * Getter for the operator
     * 
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * Setter for the Type. The type of the operator may be unary or binary
     * 
     * @param string $type
     * @return static
     */
    public function setType(string $type): static
    {
        $type = strtolower($type);
        if (!in_array($type,['unary','binary'])) {
            throw new LanguageDescriptorException("The type '".$type."' is not allowed for an operator.");
        }
        $this->type = $type;
        
        return $this;
    }
    
    /**
     * Getter for the type
     * 
     * @return string
     */
    public function getType(): string
    {
        return $this->type;    
    }
    
    /**
     * Setter for the precendence. This is the priority of this operator
     * 
     * @param int $precedence
     * @return static
     */
    public function setPrecedence(int $precedence): static
    {
        $this->precedence = $precedence;
        
        return $this;
    }
    
    /**
     * Getter for precendence
     * 
     * @return int
     */
    public function getPrecedence(): int
    {
        return $this->precedence;
    }
    
    /**
     * Adds a combination of accepted types and the resulting type. Depending on the type of this node there mighht by 2 or 3 parameters
     * 
     * @param unknown ...$args
     * @return static
     */
    public function addTypes(...$args): static
    {
        if (empty($this->type)) {
            throw new LanguageDescriptorException("An operator type has to be set before addTypes");
        }
        if (($this->type == 'unary') and (count($args) !== 2)) {
            throw new LanguageDescriptorException("Excactly two parameters are expected for an unary operator");
        }
        if (($this->type == 'binary') and (count($args) !== 3)) {
            throw new LanguageDescriptorException("Excactly three parameters are expected for an binary operator");
        }
        $this->accepted_types[] = $args;
        return $this;
    }
    
    /**
     * Returns the defined types
     * 
     * @return array
     */
    public function getAcceptedTypes(): array
    {
        return $this->accepted_types;
    }
    
}
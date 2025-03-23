<?php
/**
 * @file OperatorDescriptor.php
 * A helper class that stores all relevant informations about a operator
 * Lang en
 * Reviewstatus: 2025-03-19
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage:
 */

namespace Sunhill\Parser\LanguageDescriptor;

use Sunhill\Basic\Base;
use phpDocumentor\Reflection\Types\Static_;

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
    
    protected array $accepted_types = [];
    
    public function __construct(string $operator)
    {
        $this->operator = $operator;
    }
    
    public function getOperator(): string
    {
        return $this->operator;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        
        return $this;
    }
    
    public function getType(): string
    {
        return $this->type;    
    }
    
    public function setPrecedence(int $precedence): static
    {
        $this->precedence = $precedence;
        
        return $this;
    }
    
    public function getPrecedence(): int
    {
        return $this->precedence;
    }
    
    public function addTypes(...$args): static
    {
        $this->accepted_types[] = $args;
        return $this;
    }
    
    public function getAcceptedTypes(): array
    {
        return $this->accepted_types;
    }
    
}
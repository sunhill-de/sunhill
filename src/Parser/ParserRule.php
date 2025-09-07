<?php

/**
 * @file ParserRule.php
 * A class that stores a single parser rule
 * Lang en
 * Created at: 2025-03-11
 * Reviewstatus: 2025-09-07
 * Localization: complete
 * Documentation: complete
 * Tests:
 * Coverage Unit: 92.31 % (2025-06-06)
 */

namespace Sunhill\Parser;

use Sunhill\Basic\Base;
use Symfony\Component\CssSelector\XPath\Extension\CombinationExtension;

class ParserRule extends Base
{
    /**
     * If the rule fits (and has enough priority) the right hand expression on the stack is replaced by this
     * 
     * @var string
     */
    protected string $left_hand = '';

    /**
     * The stack top has to fit these items so that the rule matches
     * 
     * @var array
     */
    protected array $right_hand = [];

    /**
     * The priority of this expression (for the case that more that one rule matches)
     * 
     * @var integer
     */
    protected int $priority = 0;

    /**
     * Defines the manipulator for the AST when this rule matches
     * 
     * @var string
     */
    protected $ast_callback = 'passThrough';

    /**
     * Returns what types the AST can accept and what resulting type this node creates. When this
     * array is empty, nothing is passed to the AST otherwise the rules are passed to the AST (for the
     * later check of the Analyzer)
     * 
     * @var array
     */
    protected $types = [];
    /**
     * Constructor takes an left_hand and a right_hand rule (see above)
     * 
     * @param string $left_hand The non-terminal that should be reduced to when the rule fits
     * @param string|array $right_hand When this parameter is a string its only this one condition
     */
    public function __construct(string $left_hand, string|array $right_hand)
    {
        $this->left_hand = $left_hand;
        if (is_array($right_hand)) {
            $this->right_hand = $right_hand;
        } else {
            $this->right_hand = [$right_hand];
        }
    }

    /**
     * Retuns the non-terminal the stack top should be reduced to when the right hand rule applies
     * 
     * @return string
     */
    public function getLeftHand(): string
    {
        return $this->left_hand;
    }

    /**
     * Return the conditions that have to apply to match this rule
     * 
     * @return array
     */
    public function getRightHand(): array
    {
        return $this->right_hand;
    }

    /**
     * Returns how many single conditions the right hand has
     * 
     * @return int
     */
    public function getRightHandRuleCount(): int
    {
        return count($this->right_hand);
    }

    /**
     * Returns the priority of this rule. When more that one rule apply to the stack top the one with the
     * highest priority is choosen.
     * 
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Setter for the priority
     * 
     * @param int $priority
     * @return static
     */
    public function setPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Returns what callback should be used for the AST
     * 
     * @return mixed
     */
    public function getASTCallback(): mixed
    {
        return $this->ast_callback;
    }

    /**
     * Sets what callback should be used for the AST
     * 
     * @param string|callable $callback
     * @return static
     */
    public function setASTCallback(string|callable $callback): static
    {
        $this->ast_callback = $callback;

        return $this;
    }
    
    /**
     * Sets the acceptes types for this rule. These are later checked by the Analyzer. The parser just
     * passes them to the AST (when not empty)
     * 
     * @param array $types An array of arrays. Each entry of the outer entry is a possible type/result type combination
     * The inner array has to have the number of non terminals plus one
     * @return static
     */
    public function setTypes(array $types): static
    {
        $this->types = $types;
        
        return $this;   
    }
    
    public function getTypes(): ?array
    {
        if (empty($this->types)) {
            return null;
        }
        return $this->types;
    }
}

<?php
/**
 * @file ParserRule.php
 * A class that stores a single parser rule
 * Lang en
 * Reviewstatus: 2025-03-11
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage:
 */

namespace Sunhill\Parser;

use Sunhill\Basic\Base;
use phpDocumentor\Reflection\Types\Mixed_;

class ParserRule extends Base
{
        
    protected string $left_hand = '';
    
    protected array $right_hand = [];
    
    protected int $priority = 0;
    
    protected $ast_callback = 'passThrough';
    
    public function __construct(string $left_hand, string|array $right_hand)
    {
        $this->left_hand = $left_hand;
        if (is_array($right_hand)) {
            $this->right_hand = $right_hand;
        } else {
            $this->right_hand = [$right_hand];
        }
    }

    public function getLeftHand(): string
    {
        return $this->left_hand;
    }
    
    public function getRightHand(): array
    {
        return $this->right_hand;
    }
    
    public function getRightHandRuleCount(): int
    {
        return count($this->right_hand);
    }
    
    public function getPriority(): int
    {
        return $this->priority;
    }
    
    public function setPriority(int $priority): static
    {
        $this->priority = $priority;
        return $this;
    }
    
    public function getASTCallback(): mixed
    {
        return $this->ast_callback;
    }
    
    public function setASTCallback(string|callable $callback): static
    {
        $this->ast_callback = $callback;
        return $this;
    }
    
}
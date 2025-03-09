<?php

namespace Sunhill\Parser;

use Sunhill\Query\Exceptions\InvalidStatementException;
use Sunhill\Basic\Base;

class Parser extends Base
{
    protected $grammar = [];

    protected $stack = [];
   
    protected $accepted_finals = [];
    
    protected $operator_precence = [];
    
    private function shift(Token $token)
    {
        $this->stack[] = $token;    
    }
    
    private function matches(array $right_hand_tokens): bool
    {
        $stack_size = count($this->stack);
        if (array_key_exists('!execute!', $right_hand_tokens)) {
            $rule_size = count($right_hand_tokens)-1;
        } else {
            $rule_size = count($right_hand_tokens);
        }
        if ($rule_size > $stack_size) {
            return false; // Stack to small, can't fit
        }
        for ($i = 0; $i < $rule_size; $i++) {
            if ($right_hand_tokens[$i] !== $this->stack[$stack_size-$rule_size+$i]->getSymbol()) {
                return false;
            }
        }
        return true;
    }
    
    private function canReduce()
    {
        foreach ($this->grammar as $left_hand => $rules) {
            foreach ($rules as $right_hand) {
                if (is_array($right_hand) && $this->matches($right_hand)) {
                    return [$left_hand => $right_hand];
                }
            }
        }
        return false;
    }
    
    private function shouldReduce(): bool
    {
        return true;    
    }
    
    private function reduce(array $rule)
    {
        $left_hand = array_keys($rule)[0];
        $right_hand = array_values($rule)[0];
        if (array_key_exists('!execute!',$right_hand)) {
            $execute = array_pop($right_hand);
        } else {
            $execute = 'passThrough';
        }
        $parameters = [];
        for ($i = 0; $i < count($right_hand) ;$i++) {
            array_unshift($parameters,array_pop($this->stack));
        }
        $new_element = new Token($left_hand);
        $new_element->setAST($this->$execute(...$parameters));
        $this->stack[] = $new_element;
    }
    
    private function shiftReducePart($lexer)
    {
        while ($token = $lexer->getNextToken()) {
            $this->shift($token);
            $this->reducePart();
        }        
    }
    
    private function reducePart()
    {
        while (($rule = $this->canReduce()) && $this->shouldReduce()) {
            $this->reduce($rule);
        }        
    }
    
    private function validateStack()
    {
        if ((count($this->stack) > 1) || (!in_array($this->stack[0]->getSymbol(), $this->accepted_finals))) {
            throw new InvalidStatementException("The input string was not parsable");
            // @todo Give some hints what went wrong
        }
    }
       
    public function parse(Lexer $lexer): Node
    {
        $this->stack = [];
        $this->shiftReducePart($lexer);
        $this->reducePart();
        $this->validateStack();
        return $this->stack[0]->getAST();
    }
    
    protected function passThrough(Token $token): ?Node
    {
        if ($token->getAST() !== null) {
            return $token->getAST();
        }

        return new TerminalNode($token->getSymbol(),$token->getValue(),$token->getTypeHint());
    }
    
    protected function twoSideOperator(Token $left, Token $operator, Token $right)
    {
        $result = new BinaryNode($operator->getSymbol());
        $result->left($left->getAST());
        $result->right($right->getAST());
        return $result;    
    }
    
    protected function bracket(Token $open, Token $expression, Token $close)
    {
        return $expression->getAST();
    }
}  

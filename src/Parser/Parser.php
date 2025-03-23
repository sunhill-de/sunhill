<?php
/**
 * @file Parser.php
 * The shift-reduce-parser with look ahead
 * Lang en
 * Reviewstatus: 2025-03-11
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/ParserTest.php
 * Coverage:98,28 % (11.3.2025)
 */

namespace Sunhill\Parser;

use Sunhill\Basic\Base;
use Sunhill\Parser\Exceptions\InputNotParsableException;
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Parser\Nodes\FloatNode;
use Sunhill\Parser\Nodes\FunctionNode;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Parser\Nodes\StringNode;
use Sunhill\Parser\Nodes\UnaryNode;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Parser\LanguageDescriptor\LanguageDescriptor;

class Parser extends Base
{

    protected $stack = [];
   
    protected $accepted_finals = [];
    
    protected $operator_precedence = [];

    protected $grammar_rules = [];
    
    public function loadLanguageDescriptor(LanguageDescriptor $descriptor)
    {
        $this->grammar_rules = $descriptor->getParserRules();
        $this->operator_precedence = $descriptor->getOperatorPrecedences();
        $this->accepted_finals = $descriptor->getAcceptedSymbols();
    }
    
    /**
     * Adds a new rule to the parser
     * 
     * @param string $left_hand The symbol that the stack could be reduced to
     * @param array|string $right_hand The necessary top stack elements that have to match
     * @return ParserRule
     */
    public function addRule(string $left_hand, array|string $right_hand): ParserRule
    {
        $rule = new ParserRule($left_hand, $right_hand);
        $this->grammar_rules[] = $rule;
        return $rule;
    }
    
    /**
     * Adds an entry to the operator precedence table
     * 
     * @param string $operator
     * @param int $precedence
     */
    public function addOperatorPrecedence(string $operator, int $precedence)
    {
        $this->operator_precedence[$operator] = $precedence;    
    }
    
    /**
     * Shifts the given token on top of the stack
     * 
     * @param Token $token
     */
    private function shift(Token $token)
    {
        $this->stack[] = $token;    
    }
    
    /**
     * Tests if the given token of a rule match to the top entries of the stack
     * 
     * @param array $right_hand_tokens
     * @return bool
     */
    private function matches(array $right_hand_tokens): bool
    {
        $stack_size = count($this->stack);
        $rule_size = count($right_hand_tokens);

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
    
    /**
     * Tests if the top stack elements match a rule. If yes it return the first matrching rule
     * 
     * @return unknown|boolean
     */
    private function canReduce()
    {
        foreach ($this->grammar_rules as $rule) {
           if ($this->matches($rule->getRightHand())) {
               return $rule;
           }
        }
        return false;
    }
    
    /**
     * The former method said that it is possible to reduce, this method checks if it should reduce or 
     * shift for a higher precedence operator that comes next
     * 
     * @param ParserRule $rule
     * @param Lexer $lexer
     * @return bool
     */
    private function shouldReduce(ParserRule $rule, ?Lexer $lexer): bool
    {
        if (!isset($lexer)) {
            return true;
        }
        $lookup = $lexer->previewOperator();
        if (is_null($lookup) || !isset($this->operator_precedence[$lookup])) {
            return true;
        }
        $rule_prio = $rule->getPriority();
        if (!$rule_prio) {
            foreach ($rule->getRightHand() as $item) {
                if (isset($this->operator_precedence[$item]) && ($this->operator_precedence[$item] > $rule_prio)) {
                    $rule_prio = $this->operator_precedence[$item];
                }
            }
        }
        return $this->operator_precedence[$lookup] <= $rule_prio;
    }
    
    /**
     * The former methods said that it possible and ok to reduce so pop the matching entries from stack
     * manipulate the ast-tree and push the new symbol back on the stack
     * 
     * @param ParserRule $rule
     */
    private function reduce(ParserRule $rule)
    {
        $left_hand = $rule->getLeftHand();
        $right_hand = $rule->getRightHand();
        $execute = $rule->getASTCallback();
        $parameters = [];
        for ($i = 0; $i < count($right_hand) ;$i++) {
            array_unshift($parameters,array_pop($this->stack));
        }
        $new_element = new Token($left_hand);
        $new_element->setAST($this->$execute(...$parameters));
        $this->stack[] = $new_element;
    }
    
    /**
     * There are token in the lexer left so decide to shift or to reduce
     * 
     * @param unknown $lexer
     */
    private function shiftReducePart($lexer)
    {
        while ($token = $lexer->getNextToken()) {
            $this->shift($token);
            $this->reducePart($lexer);
        }        
    }
    
    /**
     * Tests if it is possible to reduce, then if it makes sense to reeduce and then it call reduce()
     * @param Lexer $lexer
     */
    private function reducePart(?Lexer $lexer = null)
    {
        while (($rule = $this->canReduce()) && $this->shouldReduce($rule, $lexer)) {
            $this->reduce($rule);
        }        
    }
    
    /**
     * There are no more possibilities to shift or reduce so check the top of the stack if it is valiid
     */
    private function validateStack()
    {
        if (count($this->stack) !== 1) {
            throw new InputNotParsableException("Input not parsable: Syntaxerror");
        }
        if (!in_array($this->stack[0]->getSymbol(), $this->accepted_finals)) {
            throw new InputNotParsableException("Input not parsable: Symbol not accepted");
            // @todo Give some hints what went wrong
        }
    }
       
    /**
     * Performs the parsing
     * 
     * @param Lexer $lexer
     * @return Node
     */
    public function parse(Lexer $lexer): Node
    {
        $this->stack = [];
        $this->shiftReducePart($lexer);
        $this->reducePart();
        $this->validateStack();
        return $this->stack[0]->getAST();
    }
    
    /**
     * Internal callbacks for manipulation of the ast-tree. This method just passes through the inhertied 
     * ast tree.
     * 
     * @param Token $token
     * @return Node|NULL
     */
    protected function passThrough(Token $token): ?Node
    {
        if ($token->getAST() !== null) {
            return $token->getAST();
        }
        switch ($token->getSymbol())
        {
            case 'integer':
              return new IntegerNode($token->getValue());
            case 'float':
              return new FloatNode($token->getValue());
            case 'string':
              return new StringNode($token->getValue());
            case 'identifier':
              return new IdentifierNode($token->getValue());
        }    
    }
    
    /**
     * Internal callback fot manipulation of the ast-tree. This method handles an unary operator.
     * 
     * @param Token $operator
     * @param Token $right
     * @return \Sunhill\Parser\UnaryNode
     */
    protected function unaryOperator(Token $operator, Token $right)
    {
        $result = new UnaryNode('u'.$operator->getSymbol());
        $result->child($right->getAST());
        return $result;
    }
    
    /**
     * Internal callback for manipulating of the ast-tree. This method combines two sub trees with an operator
     * 
     * @param Token $left
     * @param Token $operator
     * @param Token $right
     * @return \Sunhill\Parser\BinaryNode
     */
    protected function twoSideOperator(Token $left, Token $operator, Token $right)
    {
        $result = new BinaryNode($operator->getSymbol());
        $result->left($left->getAST());
        $result->right($right->getAST());
        return $result;    
    }
    
    /**
     * Internal callback for manipulating the ast-tree. This method handles a bracket
     * 
     * @param Token $open
     * @param Token $expression
     * @param Token $close
     * @return unknown
     */
    protected function bracket(Token $open, Token $expression, Token $close)
    {
        return $expression->getAST();
    }
    
    protected function functionHandler(Token $name, Token $arguments)
    {
        $result = new FunctionNode($name->getValue());
        $result->arguments($arguments->getAST());
        
        return $result;
    }
}  

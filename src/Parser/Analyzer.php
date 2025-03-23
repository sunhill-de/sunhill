<?php
/**
 * @file Analyzer.php
 * A basic class for analyzing parsing trees generated by Parser
 * Lang en
 * Reviewstatus: 2025-02-28
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/AnalyzerTest.php
 * Coverage: 
 */

namespace Sunhill\Parser;

use Sunhill\Basic\Base;
use Sunhill\Parser\LanguageDescriptor\LanguageDescriptor;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Parser\Nodes\ArrayNode;
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Parser\Nodes\StringNode;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Parser\Nodes\FloatNode;
use Sunhill\Parser\Nodes\BooleanNode;
use Sunhill\Parser\Nodes\FunctionNode;
use Sunhill\Parser\Nodes\UnaryNode;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Parser\LanguageDescriptor\OperatorDescriptor;
use Sunhill\Parser\LanguageDescriptor\FunctionDescriptor;
use Sunhill\Parser\Exceptions\TypeNotExpectedException;
use Sunhill\Parser\Exceptions\IdentifierNotFoundException;
use Sunhill\Parser\Exceptions\FunctionNotFoundException;
use Sunhill\Parser\Exceptions\FunctionParameterException;

class Analyzer extends Base
{
    
    protected array $accepted_operators = [];
    
    protected array $predefined_functions = [];
    
    protected array $predefined_identifiers = [];
    
    protected array $accepted_types = [];
    
    public function addOperatorTypes(string $operator, array $types)
    {
        if (isset($this->accepted_operators[$operator])) {
            $this->accepted_operators[$operator][] = $types;
        } else {
            $this->accepted_operators[$operator] = [$types];
        }
        
        return $this;
    }
    
    public function addIdentifier(string $name, string $type)
    {
        $this->predefined_identifiers[$name] = $type;
        
        return $this;
    }
    
    public function addFunction(string $name, string $return_type): FunctionDescriptor
    {
        $func = new FunctionDescriptor($name);
        $func->setReturnType($return_type);
        $this->predefined_functions[$name] = $func;
        
        return $func;
    }
    
    public function addAcceptedType(string $type)
    {
        $this->accepted_types[] = $type;
        
        return $this;
    }
    
    public function loadLanguageDescriptor(LanguageDescriptor $descriptor)
    {
    }

    /**
     * Tests if the test rule $test matches the given rule $rule and takes care of pseudo 
     * types like numeric and pseudoboolean
     * 
     * @param string $test
     * @param string $rule
     * @return boolean
     */
    private function typeMatch(string $test, string $rule)
    {
        switch ($rule) {
            case 'pseudoboolean':
                return in_array($test,['boolean','integer','float','string']);
            case 'numeric':
            case 'float':
                return in_array($test,['integer','float']);
            default:
                return $test == $rule;
        }
    }
    
    /**
     * Checks if the given rules match the given types
     * 
     * @param array $test
     * @param array $rule
     * @return bool
     */
    private function typesMatch(array $test, array $rule): bool
    {
        if (count($test) !== count($rule)-1) {
            return false;
        }
        for ($i=0;$i<count($test);$i++) {
            if (!$this->typeMatch($test[$i], $rule[$i])) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Returns the type of the binary node
     * 
     * @param BinaryNode $node
     * @return unknown
     */
    protected function getTypeOfBinaryNode(BinaryNode $node)
    {
        $left = $this->getTypeOfNode($node->left());
        $right = $this->getTypeOfNode($node->right());
        if (!isset($this->accepted_operators[$node->getType()])) {
            // Exception
        }
        foreach ($this->accepted_operators[$node->getType()] as $types) {
            if ($this->typesMatch([$left,$right],$types)) {
                return $types[2];
            }
        }
        return 'invalid';
    }
    
    private function tryToLookupFunctionDescriptor(FunctionNode $node): FunctionDescriptor|string
    {
        return 'unknown';    
    }
    
    private function getFunctionDescriptor(FunctionNode $node): FunctionDescriptor|string
    {
        if (isset($this->predefined_functions[$node->name()])) {
            return $this->predefined_functions[$node->name()];
        } 
        return $this->tryToLookupFunctionDescriptor($node);
    }
    
    /**
     * Returns the return type of the given function
     * 
     * @param FunctionNode $node
     * @return unknown
     */
    protected function getTypeOfFunctionNode(FunctionNode $node)
    {
        if (($func = $this->getFunctionDescriptor($node)) === 'unknown') {
            return 'unknown';
        }
        return $func->getReturnType();        
    }
    
    /**
     * Returns the type of the given unary node
     * 
     * @param UnaryNode $node
     * @return unknown|string
     */
    protected function getTypeOfUnaryNode(UnaryNode $node)
    {
        $child = $this->getTypeOfNode($node->child());
        if (!isset($this->accepted_operators[$node->getType()])) {
            // Exception
        }
        foreach ($this->accepted_operators[$node->getType()] as $types) {
            if ($this->typesMatch([$child],$types)) {
                return $types[1];
            }
        }
        return 'invalid';
    }
    
    protected function tryToLookupIdentifierType(string $identifier): string
    {
        return 'unknown';    
    }
    
    protected function getIdentifierType(IdentifierNode $node): string
    {
        if (!isset($this->predefined_identifiers[$node->getName()])) {
            return $this->tryToLookupIdentifierType($node->getName());
        }
        return $this->predefined_identifiers[$node->getName()];
    }
    
    /**
     * Returns the type of the identifer
     * 
     * @param IdentifierNode $node
     */
    protected function getTypeOfIdentifierNode(IdentifierNode $node)
    {
        if (isset($this->predefined_identifiers[$node->getName()])) {
            return $this->predefined_identifiers[$node->getName()];
        }
    }
    
    protected function getTypeOfNode(Node $node)
    {
        switch ($node::class) {
            case ArrayNode::class:
                return 'array';
                break;
            case BinaryNode::class:
                return $this->getTypeOfBinaryNode($node);
                break;
            case FunctionNode::class:
                return $this->getTypeOfFunctionNode($node);
                break;
            case UnaryNode::class:
                return $this->getTypeOfUnaryNode($node);
                break;
            case BooleanNode::class:
                return 'boolean';
                break;
            case FloatNode::class:
                return 'float';
                break;
            case IntegerNode::class:
                return 'integer';
                break;
            case IdentifierNode::class:
                return $this->getTypeOfIdentifierNode($node);
            case StringNode::class:
                return 'string';
                break;
        }        
    }
    
    protected function analyzeArrayNode(ArrayNode $node)
    {
        
    }
    
    protected function analyzeBinaryNode(BinaryNode $node)
    {
        
    }
    
    private function checkFunctionExistance(FunctionNode $node)
    {
        $descriptor = $this->getFunctionDescriptor($node);
        if ($descriptor == 'unknown') {
            throw new FunctionNotFoundException("The function '".$node->name()."' was not found.");
        }
        return $descriptor;
    }
    
    private function checkUnlimitedParameters(FunctionNode $node, FunctionDescriptor $descriptor)
    {
        
    }
    
    private function checkLimitedParameters(FunctionNode $node, FunctionDescriptor $descriptor)
    {
        if (($node->getArgumentCount() !== $descriptor->getTotalParameterCount())) {
            throw new FunctionParameterException("Expected '".$descriptor->getTotalParameterCount()."' parameters, got ".$node->getArgumentCount());
        }
        for ($i=0;$i<$node->getArgumentCount();$i++) {
            $given = $this->getTypeOfNode($node->getArgument($i));
            $expected = $descriptor->getParameter($i)->type;
            if (!$this->typeMatch($given, $expected)) {
                throw new FunctionParameterException("The given type '$given', expected '$expected'");
            }
        }
    }
    
    private function checkParameters(FunctionNode $node, FunctionDescriptor $descriptor)
    {
        if ($descriptor->getTotalParameterCount() == -1) {
            $this->checkUnlimitedParameters($node, $descriptor);
        } else {
            $this->checkLimitedParameters($node, $descriptor);
        }
    }
    
    protected function analyzeFunctionNode(FunctionNode $node)
    {
        $descriptor = $this->checkFunctionExistance($node);
        $this->checkParameters($node, $descriptor);
    }
    
    protected function analyzeIdentifierNode(IdentifierNode $node)
    {
        $type = $this->getIdentifierType($node);
        if ($type == 'unknown') {
            throw new IdentifierNotFoundException("The identifier '".$node->getName()."' was not found.");
        }
    }
    
    protected function analyzeUnaryNode(UnaryNode $node)
    {
        
    }
    
    protected function analyzeNode(Node $node)
    {
        switch ($node::class) {
            case ArrayNode::class:
                $this->analyzeArrayNode($node);
                break;
            case BinaryNode::class:
                $this->analyzeBinaryNode($node);
                break;
            case FunctionNode::class:
                $this->analyzeFunctionNode($node);
                break;
            case IdentifierNode::class:
                $this->analyzeIdentifierNode($node);
                break;
            case UnaryNode::class:
                $this->analyeUnaryNode($node);
                break;
            case BooleanNode::class:
            case FloatNode::class:
            case IntegerNode::class:
            case StringNode::class:
                // Constants don't have to be analyzed
                break;
        }
    }
    
    protected function typeAccepted(Node $node)
    {
        if (!in_array($result = $this->getTypeOfNode($node), $this->accepted_types)) {
            throw new TypeNotExpectedException("The type of the expression ($result) was not expected");
        }
    }
    
    public function analyze(Node $root_node)
    {
        $this->analyzeNode($root_node);
        $this->typeAccepted($root_node);
    }

}

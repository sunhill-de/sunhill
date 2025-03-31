<?php
/**
 * @file Executor.php
 * A basic class that processes a abstract structure tree (AST).
 * Lang en
 * Created: 2025-03-31
 * Reviewstatus: 2025-03-31
 * Localization: complete
 * Documentation: complete
 * @subpackage parser
 * Tests: not testable
 * Coverage: 
 */
namespace Sunhill\Parser;

use Sunhill\Basic\Base;
use Sunhill\Parser\Nodes\Node;

/**
 * The Executor is a simple abstract class that just defines an abstract doExecute method that should perform whatever should be done with the abstract structre tree.
 * 
 * @author klaus
 *
 */
abstract class Executor extends Base
{
   
    /**
     * This method executes someting on the given AST. It can (or not) return anything.
     * 
     * @param Node $ast
     */
    abstract protected function doExecute(?Node $ast);
    
    
    /**
     * Wrapper for doExecute(). This method executes someting on the given AST. 
     * It can (or not) return anything.
     *
     * @param Node $ast
     */
    public function execute(Node $ast)
    {
        return $this->doExecute($ast);
    }
    
}
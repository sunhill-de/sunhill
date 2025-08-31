<?php

/**
 * @file AliasNode.php
 * A node that represents an alias (like "field as alias")
 * Lang en
 * Reviewstatus: 2025-07-07
 * Creation date: 2025-04-25
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Query/QueryParser/Nodes/AliasNodeTest.php
 * Coverage Unit:
 */

namespace Sunhill\Query\QueryParser\Nodes;

use Sunhill\Parser\Nodes\Node;
use Sunhill\Query\Exceptions\InvalidAliasException;

/**
 * An alias node represents the result of an expression like "expr as alias". The alias than stands for the
 * whole expression and could be used in other statements like "where".
 *
 * @author klaus
 */
class AliasNode extends Node
{
    /**
     * The constructor for an AliasNode. "alias" is then the same as "expression"
     *
     * @param  unknown  $expression
     * @param  unknown  $alias
     */
    public function __construct(Node $expression, string $alias)
    {
        parent::__construct('alias', ['expression' => $expression, 'alias' => $alias]);
    }

    /**
     * The getter and setter for expression (depending whether $node is set.
     *
     * @return \Sunhill\Query\QueryParser\Nodes\AliasNode|null|mixed
     */
    public function expression(?Node $node = null)
    {
        return $this->handleReplacingChild('expression', $node);
    }

    /**
     * The getter and setter for the alias for the former expression (depending on $alias is set)
     *
     * @param  Node  $node
     * @return \Sunhill\Query\QueryParser\Nodes\AliasNode|null|mixed
     */
    public function alias(?string $alias = null)
    {
        return $this->handleReplacingChild('alias', $alias);
    }

    /**
     * Converts this alias node into a string
     *
     * {@inheritDoc}
     *
     * @see \Sunhill\Parser\Nodes\Node::toString()
     */
    public function toString(): string
    {
        return $this->expression()->toString().' AS '.$this->alias();
    }

    /**
     * Validates the alias node. It first checks if the expression is valid then if the alias is valid
     *
     * {@inheritDoc}
     *
     * @see \Sunhill\Parser\Nodes\Node::validate()
     */
    public function validate()
    {
        $this->expression()->validate();
        if (! preg_match('/^[a-zA-Z_]([a-zA-Z_0-9]*)$/', $this->alias())) {
            throw new InvalidAliasException("The alias '".$this->alias()."' is not allowed.");
        }
    }
}

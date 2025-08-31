<?php

/**
 * @file OrderNode.php
 * A node that represents an order (like order field asc")
 * Lang en
 * Reviewstatus: 2025-07-07
 * Creation date: 2025-04-25
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Query/QueryParser/Nodes/OrderNodeTest.php
 * Coverage Unit:
 */

namespace Sunhill\Query\QueryParser\Nodes;

use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Query\Exceptions\InvalidOrderException;

/**
 * An order node represents the result of a statement like "order by field asc"
 * field has to be an identifiert or alias
 * drirection must be asc or desc
 *
 * @author klaus
 */
class OrderNode extends Node
{
    /**
     * Constructor. Accepts the field and the direction
     */
    public function __construct(?Node $field = null, string $direction = 'asc')
    {
        parent::__construct('order', ['field' => $field, 'direction' => trim($direction)]);
    }

    /**
     * Setter and getter for field
     *
     * @return \Sunhill\Query\QueryParser\Nodes\OrderNode|null|mixed
     */
    public function field(?Node $node = null)
    {
        return $this->handleReplacingChild('field', $node);
    }

    /**
     * Setter and Getter for direction
     *
     * @return \Sunhill\Query\QueryParser\Nodes\OrderNode|null|mixed
     */
    public function direction(?string $direction = null)
    {
        if (! is_null($direction)) {
            $direction = trim($direction);
        }

        return $this->handleReplacingChild('direction', $direction);
    }

    /**
     * converts the node into a string
     *
     * {@inheritDoc}
     *
     * @see \Sunhill\Parser\Nodes\Node::toString()
     */
    public function toString(): string
    {
        return $this->field()->toString().' '.strtoupper($this->direction());
    }

    /**
     * Validates if the node is correct.
     *
     * {@inheritDoc}
     *
     * @see \Sunhill\Parser\Nodes\Node::validate()
     */
    public function validate()
    {
        $this->field()->validate();
        if (! is_a($this->field(), IdentifierNode::class) && (! is_a($this->field(), AliasNode::class))) {
            throw new InvalidOrderException('The order field must be an identifer or an alias');
        }
        if ((strtolower($this->direction()) !== 'asc') && (strtolower($this->direction()) !== 'desc')) {
            throw new InvalidOrderException("The direction '".$this->direction()."' is not valid.");
        }
    }
}

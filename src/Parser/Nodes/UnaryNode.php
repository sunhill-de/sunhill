<?php

/**
 * @file UnaryNode.php
 * A basic class for a node that has only one child
 * Lang en
 * Reviewstatus: 2025-06-27
 * Create date: 2025-03-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage Unit: 100 % (2025-06-06)
 */

namespace Sunhill\Parser\Nodes;

use Sunhill\Parser\Exceptions\TypesMismatchException;

class UnaryNode extends Node
{
    protected $allowed_types = [];

    public function __construct(string $type)
    {
        parent::__construct($type, []);
    }

    /**
     * Simplified setter/getter for the child. When called with parameter it acts as a setter otherwise as a getter.
     */
    public function child(?Node $node = null): Node
    {
        if (! is_null($node)) {
            $this->children['child'] = $node;

            return $this;
        } else {
            return $this->children['child'];
        }
    }

    /**
     * Helper function that searches for a rule that fits to the given child datatype or null if none found.
     */
    private function getOperatorDesciptor(string $child): ?\stdClass
    {
        foreach ($this->allowed_types as $type) {
            if ($this->typeMatch($child, $type->child)) {
                return $type;
            }
        }

        return null;
    }

    /**
     * Returns the datatype of the child node
     *
     * {@inheritDoc}
     *
     * @see \Sunhill\Parser\Nodes\Node::getDatatype()
     */
    public function getDatatype(): ?string
    {
        if ($descriptor = $this->getOperatorDesciptor($this->child()->getDatatype())) {
            return $descriptor->resulting;
        }

        return null;
    }

    public function toString(): string
    {
        return '('.$this->getType().$this->child()->toString().')';
    }

    public function addAllowedType(string $child, string $resulting)
    {
        $entry = new \stdClass;
        $entry->child = $child;
        $entry->resulting = $resulting;
        $this->allowed_types[] = $entry;
    }

    protected function validateOperator(string $child_data_type)
    {
        if (! $this->getOperatorDesciptor($child_data_type)) {
            throw new TypesMismatchException("The unary operator '".$this->getType()."' is not allowed for '$child_data_type'");
        }
    }

    public function validate()
    {
        $this->child()->validate();
        $this->validateOperator($this->child()->getDatatype());
    }
}

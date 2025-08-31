<?php

/**
 * @file UnknownDatatype.php
 * A trait for nodes that can't tell their datatype at creation type. These are functions and identifiers.
 * Lang en
 * Reviewstatus: 2025-06-27
 * Create date: 2025-06-27
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage Unit:
 */

namespace Sunhill\Parser\Traits;

trait UnknownDatatype
{
    /**
     * The result type of a function node is not known to the node but instead detected later by
     * the analyzer. The analyzer can use setDatatype to mark the datatype.
     *
     * @var unknown
     */
    protected ?string $datatype = null;

    /**
     * Setter for the datatype
     */
    public function setDatatype(string $type): static
    {
        $this->datatype = $type;

        return $this;
    }

    /**
     * Getter for the datatype
     *
     * {@inheritDoc}
     *
     * @see \Sunhill\Parser\Nodes\Node::getDatatype()
     */
    public function getDatatype(): ?string
    {
        return $this->datatype;
    }
}

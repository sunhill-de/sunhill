<?php

/**
 * @file FilterContainer.php
 * The central object that all filters work on
 * Lang en
 * Reviewstatus: 2024-10-05
 * Localization: incomplete
 * Documentation: complete
 * Coverage Unit: 70% (2025-06-06)
 * Tests: Unit/Filter/
 *
 * @wiki /Filters
 */

namespace Sunhill\Filter;

/**
 * A standard implementation for filterable items
 *
 * @author Klaus Dimde
 */
class FilterContainer
{
    protected $options = [];

    /**
     * Returns if the item has the given condition
     */
    public function hasCondition(string $name): bool
    {
        return isset($this->options[$name]);
    }

    /**
     * Returns if the item is writeable
     */
    public function conditionWriteable(string $name): bool
    {
        return true;
    }

    /**
     * Returns the actual value of the condition
     */
    public function getCondition(string $name)
    {
        if (! isset($this->options[$name])) {
            throw new \Exception("Access of unknown option '$name'");
        }
        $result = $this->options[$name];
        if (is_scalar($result)) {
            return $result;
        }
        if (is_callable($result)) {
            return $result($this);
        }
    }

    /**
     * Sets the actual value of the condition
     *
     * @param  unknown  $value
     */
    public function setCondition(string $name, $value = true)
    {
        $this->options[$name] = $value;
    }
}
